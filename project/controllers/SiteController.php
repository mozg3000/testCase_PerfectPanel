<?php

namespace app\controllers;

use app\components\Response;
use app\controllers\actions\Rate;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\Request;

class SiteController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
    }

    public function actions()
    {
        return [
            'rates' => [
                'class' => Rate::class,
                'modelClass' => '',
                'checkAccess' => [$this, 'checkAccess']
            ]
        ];
    }

    public function actionIndex(string $method, Request $request, Response $response)
    {
        $token = $this->request->getHeaders()->get('Authorization');
        $token = substr($token, 7);
        $user = User::findIdentityByAccessToken($token);
        if (!$user) {
            return $response->getErrorResponse();
        }
        Yii::$app->user->login($user);
        $parameter = $request->getQueryParam('parameter', null);

        return $this->runAction($method, ['parameter' => $parameter]);
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        switch ($action) {
            default: return true;
        }
    }
}
