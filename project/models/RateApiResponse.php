<?php

namespace app\models;

use app\models\RateApiResponseInterface;
use yii\helpers\ArrayHelper;

class RateApiResponse implements RateApiResponseInterface
{

    public function fromApiResponse(array $rates): array
    {
        usort($rates, fn(array $a, array $b) => $a['rate'] - $b['rate'] <=> 0);
        $rates = ArrayHelper::map($rates, 'currency', 'rate');
        return $rates;
    }

    public function converssionResponse(
        string $from,
        string $to,
        float  $amount,
        float  $converted,
        float  $rate
    )
    {
        return [
            'currency_from'     => $from,
            'currency_to'       => $to,
            'value'             => $amount,
            'converted_value'   => $converted,
            'rate'              => $rate

        ];
    }
}