<?php

namespace app\controllers;

use app\components\Response;
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
                'class' => Rate::class,
                'modelClass' => '',
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
                $response->setError(403);
                throw new ForbiddenHttpException('Invalid token');
            }
            Yii::$app->user->login($user);
            $parameter = $request->getQueryParam('parameter', null);
            $this->runAction($method, ['parameter' => $parameter]);
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
            default: throw new ForbiddenHttpException('Invalid token');
        }
    }
}
