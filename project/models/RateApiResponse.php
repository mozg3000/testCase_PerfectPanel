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
}