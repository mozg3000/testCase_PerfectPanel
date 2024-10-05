<?php

namespace app\services\rate;

interface RateServiceInterface
{
    public function getRates(callable $comparator = null);
}