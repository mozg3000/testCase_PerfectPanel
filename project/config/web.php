<?php

use app\components\Response;
use app\infrastructure\http\ClientInterface;
use app\infrastructure\http\RestClient;
use app\infrastructure\http\Sender;
use app\infrastructure\http\SenderInterface;
use app\models\ApiResponseExtractor;
use app\models\ApiResponseExtractorInterface;
use app\models\RateApiResponse;
use app\models\RateApiResponseInterface;
use app\services\rate\RateApiClient;
use app\services\rate\RateApiClientInterface;
use app\services\rate\RateService;
use app\services\rate\RateServiceInterface;
use yii\di\Container;

$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => [
        'singletons' => [
            ClientInterface::class => RestClient::class,
            SenderInterface::class => function (Container $container) {
                $client = $container->get(ClientInterface::class);
                return new Sender($client);
            },
            ApiResponseExtractorInterface::class => ApiResponseExtractor::class,
            RateServiceInterface::class => function (Container $container) {
                $client = $container->get(RateApiClientInterface::class);
                return new RateService($client);
            },
            RateApiResponseInterface::class => RateApiResponse::class
        ],
        'definitions' => [
            RateApiClientInterface::class => function (Container $container) {
                /** @var RestClient $client */
                $client = $container->get(ClientInterface::class);
                $client->baseUrl = 'https://api.coincap.io/';
                $sender = $container->get(SenderInterface::class);
                return new RateApiClient($sender, $client);
            }
        ]
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'qPcAhUmifI0p3KpdNSLcC8KXpLoKKzuF',
            'enableCsrfValidation' => false
        ],
        'response' => [
            'class' => Response::class
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/',
            'rules' => [
                'api/v1' => 'site/index'
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
