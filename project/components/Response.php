<?php

namespace app\components;

use yii\base\Model;

/**
 * Class Response
 * @package app\components\Response
 */
final class Response extends \yii\web\Response
{
    /**
     * Statuses of a response
     */
    public const SUCCESS = 'success';
    public const ERROR   = 'error';

    public function getErrorResponse()
    {
        return [
            'status' => Response::ERROR,
            'code' => 403,
            'message' => 'Invalid token'
        ];
    }
    public function getSuccessResponse(array $data)
    {
        return [
            'status' => Response::SUCCESS,
            'code' => 200,
            'data' => $data
        ];
    }
}
