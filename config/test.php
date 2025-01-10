<?php

use app\modules\api\Api;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'yii2-api-example-tests',
    'name' => 'Yii2-API-Example-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'modules' => [
        'api' => [
            'class' => Api::class,
        ],
    ],
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
            'messageClass' => 'yii\symfonymailer\Message'
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'rules' => [
                'GET api/v1/book' => 'api/v1/book/index',
                'GET api/v1/book/<id:\d+>' => 'api/v1/book/view',
                'POST api/v1/book' => 'api/v1/book/create',
                'DELETE api/v1/book/<id:\d+>' => 'api/v1/book/delete',
                'PUT api/v1/book/<id:\d+>' => 'api/v1/book/update',
                'PATCH api/v1/book/<id:\d+>' => 'api/v1/book/update-part',
            ],
        ],
        'user' => [
            'identityClass' => 'app\tests\api\models\TestUser',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
