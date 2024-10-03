<?php

namespace app\controllers\actions;

use app\components\Response;
use yii\rest\Action;

class Rate extends Action
{
    public function run(string $parameter, Response $response)
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        return $response->getSuccessResponse([]);
    }
}