<?php

namespace app\controllers\actions;

use app\components\Response;
use app\models\Commission;
use app\models\RateApiResponse;
use app\models\RateApiResponseInterface;
use app\services\rate\RateService;
use app\services\rate\RateServiceInterface;
use yii\rest\Action;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\web\ForbiddenHttpException;

class Convert extends Action
{
    /**
     * @param string                                    $from
     * @param string                                    $to
     * @param float                                     $amount
     * @param Response                                  $response
     * @param RateServiceInterface|RateService          $rateService
     * @param RateApiResponseInterface|RateApiResponse  $rateApiResponse
     * @return void
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function run(
        string                   $from,
        string                   $to,
        float                    $amount,
        Response                 $response,
        RateServiceInterface     $rateService,
        RateApiResponseInterface $rateApiResponse
    ): void
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $parameter   = $from !== 'USD' ? $from : $to;
        $comparator  = fn(array $a) => $a['symbol'] === $parameter;
        $ratesIncome = $rateService->getRates($comparator);
        $commission  = new Commission();
        $commission->charge($ratesIncome);

        $rateData  = array_pop($ratesIncome);
        $rate      = $rateData['rate'];
        $parameter !== $from && $rate = 1/$rate;
        $converted = $from === 'BTC'
            ? round($amount * $rate, 2)
            : round(round($amount, 2) * $rate, 10);

        $data = $rateApiResponse->converssionResponse(
            $from,
            $to,
            $amount,
            $converted,
            $rate
        );

        $response->setSuccess($data);
    }
}