<?php

namespace app\controllers;

use app\components\Response;
use app\controllers\actions\Convert;
use app\controllers\actions\Rate;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
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
                'class'       => Rate::class,
                'modelClass'  => '',
                'checkAccess' => [$this, 'checkAccess']
            ],
            'convert' => [
                'class'       => Convert::class,
                'modelClass'  => '',
                'checkAccess' => [$this, 'checkAccess']
            ]
        ];
    }

    public function actionIndex(
        string   $method,
        Request  $request,
        Response $response
    )
    {
        try {
            $token = $this->request->getHeaders()->get('Authorization');
            $token = substr($token, 7);
            $user = User::findIdentityByAccessToken($token);
            if (!$user) {
                throw new ForbiddenHttpException('Invalid token');
            }
            Yii::$app->user->login($user);
            $payload = [];
            switch ($method) {
                case 'rates': {
                    $parameter = $request->getQueryParam('parameter', null);
                    $payload   = ['parameter' => $parameter];
                    break;
                }
                case 'convert': {
                    $content = file_get_contents('php://input');
                    $body    = json_decode($content, true);
                    extract($body);
                    $payload = [
                        'from'   => $currency_from,
                        'to'     => $currency_to,
                        'amount' => $value
                    ];
                    break;
                }
            }
            $this->runAction($method, $payload);
        } catch (ForbiddenHttpException $e) {
            $response->setError(403, $e->getMessage());
        }
// catch (\Throwable $e) { // чтобы видить ошибки для отладки
//            $response->setError(403);
//        }
    }

    public function checkAccess($action, $model = null, $params = [])
    {
        switch ($action) {
            case 'rates': {
                return true;
            }
            case 'convert': {
                return true;
            }
            default: throw new ForbiddenHttpException('Invalid token');
        }
    }
}
