<?php

namespace app\models;

interface RateApiResponseInterface
{
    public function fromApiResponse(array $rates);
}