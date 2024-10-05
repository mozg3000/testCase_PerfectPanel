<?php

namespace app\infrastructure\http;

use Generator;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Response;

class Sender implements SenderInterface
{
    private array $middlewares;
    private int   $currentMiddleware = 0;
    private RestMessage $message;
    /** @var Client $client */
    private $client;
    private Response $response;
    private $trigger = 401;
    private $wait = 0;

    public function __construct(ClientInterface $client)
    {

        $this->client = $client;
    }

    public function send(MessageInterface $message)
    {
        $this->message = $message;
        /** @var RestMessage $message */
        /** @var Generator $gen */
        $gen = $this->trySend($message);
        foreach ($gen as $n => $s) {
            $this->next();
        }
        /** @var Response $res */
        $res = $gen->getReturn();
        return $res;
    }

    public function setTrigger(int $status)
    {
        $this->trigger = $status;
        return $this;
    }

    public function setWait(int $ms)
    {
        $this->wait = $ms;
        return $this;
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function trySend(MessageInterface $message)
    {
        $statusCode = null;
        $n = 0;
        while ($statusCode != 200 && $n < 2) {
            $res = $this->client->send($this->message);
            $this->response = $res;
            $statusCode = $res->getStatusCode();
            $n++;
            switch ($statusCode) {
                case 401:
                case $this->trigger:
                {
                    yield $res;
                    if ($this->wait > 0) {
                        usleep($this->wait);
                    }
                    break;
                }
                default: break 2;
            }
        }
        return $res;
    }

    public function middleware(array $middlewares): self
    {
        foreach ($middlewares as $closure) {
            $this->middlewares[] = $closure;
        }
        return $this;
    }

    public function next()
    {
        $current = $this->currentMiddleware++;
        if (isset($this->middlewares[$current])) {
            $do = $this->middlewares[$current]($this->message, $this->response, [$this, 'next']);
            if (!$do) {
                $this->currentMiddleware = 0;
            }
        } else {
            $this->currentMiddleware = 0;
        }
    }
}
