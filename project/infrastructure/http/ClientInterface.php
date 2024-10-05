<?php

namespace app\infrastructure\http;

interface ClientInterface
{
    public function send($message);
}