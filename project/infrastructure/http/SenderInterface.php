<?php

namespace app\infrastructure\http;


interface SenderInterface
{
    public function send(MessageInterface $massage);
}