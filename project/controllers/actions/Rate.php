<?php

namespace app\controllers\actions;

use app\components\Response;
use app\models\Commission;
use app\models\RateApiResponse;
use app\models\RateApiResponseInterface;
use app\services\rate\RateService;
use app\services\rate\RateServiceInterface;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\rest\Action;
use yii\web\ForbiddenHttpException;

class Rate extends Action
{
    /**
     * @param string|null                               $parameter
     * @param Response                                  $response
     * @param RateServiceInterface|RateService          $rateService
     * @param RateApiResponseInterface|RateApiResponse  $rateApiResponse
     * @return void
     * @throws ForbiddenHttpException
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function run(
        ?string                  $parameter,
        Response                 $response,
        RateServiceInterface     $rateService,
        RateApiResponseInterface $rateApiResponse
    ): void
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $comparator = null;
        if ($parameter) {
            $comparator = fn(array $a) => $a['symbol'] === $parameter;
        }
        $ratesIncome = $rateService->getRates($comparator);
        $commission = new Commission();
        $commission->charge($ratesIncome);
        $rates = $rateApiResponse->fromApiResponse($ratesIncome);
        $response->setSuccess($rates);
    }
}