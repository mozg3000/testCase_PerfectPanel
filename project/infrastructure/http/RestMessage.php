<?php

namespace app\infrastructure\http;

use yii\httpclient\Client;
use yii\httpclient\Request;

class RestMessage extends Request implements MessageInterface
{
    private array $data;
    public function __construct(
        Client $client,
        $config = []
    )
    {
        parent::__construct($config);
        $this->client = $client;
    }

    public function composeMessage()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data = $data;
        $data = $this->composeMessage();

        return parent::setData($data);
    }
}
