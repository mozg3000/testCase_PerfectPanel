<?php

namespace app\services\rate;

use app\infrastructure\http\ClientInterface;
use app\infrastructure\http\RestClient;
use app\infrastructure\http\RestMessage;
use app\infrastructure\http\Sender;
use app\infrastructure\http\SenderInterface;
use yii\httpclient\Response;

class RateApiClient implements RateApiClientInterface
{
    private Sender     $sender;
    private RestClient $client;

    public function __construct(
        SenderInterface $sender,
        ClientInterface $client
    )
    {
        $this->sender = $sender;
        $this->client = $client;
    }

    public function createMessage(string $url, array $data = null, string $method = 'GET'): RestMessage
    {
        $request   = new RestMessage($this->client);
        $str       = $data ? json_encode($data, JSON_FORCE_OBJECT) : null;
        $request->setMethod($method)
            ->setUrl($url)
            ->setHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
        ;
        if ($str) {
            $request->setContent($str);
        }
        return $request;
    }

    private function getResponse(RestMessage $message): Response
    {
        $res = $this->sender->send($message);
        return $res;
    }

    public function getRates(string $currency = null): Response
    {

        $url = "v2/rates";
        $message = $this->createMessage($url);

        return $this->getResponse($message);
    }
}