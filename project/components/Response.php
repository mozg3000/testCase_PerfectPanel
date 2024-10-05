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

    /**
     * Sets error response
     * @param  int      $code
     * @param  string   $message
     * @return void
     */
    public function setError(int $code, string $message = 'Invalid token'): void
    {
        $this->data = [
            'status'  => Response::ERROR,
            'code'    => $code,
            'message' => $message
        ];
    }

    /**
     * Sets success response
     * @param  array $data
     * @param  int   $code
     * @return void
     */
    public function setSuccess(array $data, int $code = 200): void
    {
        $this->data = [
            'status' => Response::SUCCESS,
            'code'   => $code,
            'data'   => $data
        ];
    }
}
