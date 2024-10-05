<?php

namespace app\models;

class Commission
{
    public function charge(array &$rates): void
    {
        array_walk($rates, fn(array &$a) => $a['rate'] += CommissionRate::ON_CONVERT->value * $a['rate'] * .01);
    }
}