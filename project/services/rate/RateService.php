<?php

namespace app\services\rate;


use app\models\ApiResponseExtractor;
use app\models\ApiResponseExtractorInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\httpclient\Response;
use yii\web\ForbiddenHttpException;

class RateService implements RateServiceInterface
{
    private RateApiClientInterface|RateApiClient $client;
    private ?array $rates = null;

    public function __construct(RateApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws NotInstantiableException|ForbiddenHttpException|InvalidConfigException
     */
    public function getRates(callable $comparator = null)
    {
        if (is_null($this->rates)) {
            $apiResponse = $this->requestRates();
            /** @var ApiResponseExtractor $apiResponseExtractor */
            $apiResponseExtractor = Yii::$container->get(ApiResponseExtractorInterface::class, ['response' => $apiResponse]);
            $this->rates = $apiResponseExtractor->extract($comparator);
        }
        return $this->rates;
    }

    private function requestRates(): Response
    {
        $apiResponse = $this->client->getRates();
        $statusCode = $apiResponse->statusCode;
        if ($statusCode != 200) {
            throw new ForbiddenHttpException('Invalid token');
        }
        return $apiResponse;
    }
}