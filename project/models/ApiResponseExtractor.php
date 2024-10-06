<?php

namespace app\models;

use yii\httpclient\Response;

/**
 * Extracts data from rate api response
 */
class ApiResponseExtractor implements ApiResponseExtractorInterface
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function extract(callable $comparator = null): array
    {
        $data = $this->response?->data['data'] ?? [];
        $filteredData = $comparator ? array_filter($data, $comparator) : $data;
        return array_map(fn(array $a) => [
            'currency'  => $a['symbol'],
            'rate'      => $a['rateUsd']
        ], $filteredData);
    }
}