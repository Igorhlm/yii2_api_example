<?php

declare(strict_types=1);

use yii\web\Request;
use yii\web\Response;

use app\modules\api\Api;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'yii2-api-example',
    'name' => 'Yii2-API-Example',

    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'debug'],
    'language' => 'ru-RU',

    'modules' => [
        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'httpclient' => [
                    'class' => 'yii\\httpclient\\debug\\HttpClientPanel',
                ],
            ],
        ],
        'api' => [
            'class' => Api::class,
        ],
    ],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'components' => [
        'request' => [
            'class' => Request::class,
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],

        'response' => [
            'class' => Response::class,
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user' => [
            'identityClass' => 'app\modules\api\modules\v1\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
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

        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,

            'rules' => [
                'GET /' => 'site/index',
                'GET /index' => 'site/index',
                'GET /about' => 'site/about',

                'GET api/v1/book' => 'api/v1/book/index',
                'GET api/v1/book/<id:\d+>' => 'api/v1/book/view',
                'POST api/v1/book' => 'api/v1/book/create',
                'DELETE api/v1/book/<id:\d+>' => 'api/v1/book/delete',
                'PUT api/v1/book/<id:\d+>' => 'api/v1/book/update',
                'PATCH api/v1/book/<id:\d+>' => 'api/v1/book/update-part',

                'POST api/v1/auth/login' => 'api/v1/auth/login',
                'POST api/v1/auth/refresh-tokens' => 'api/v1/auth/refresh-tokens',
                'PATCH api/v1/auth/logout' => 'api/v1/auth/logout',
            ],
        ],
    ],

    'container' => [
        'definitions' => [
            'yii\data\Pagination' => [
                'class' => 'app\modules\api\modules\v1\components\PaginationExample',
                'validatePage' => false,
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
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
