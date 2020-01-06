<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    /*
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    */
    'language'=>'ru-RU',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'werERHweQwery26y23DSFhb',
            'enableCsrfValidation'=>false,
//            'enableCookieValidation'=>false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'cookieParams' => ['httponly' => true, 'lifetime' => 3600],
            'timeout' => 3600, //session expire
            'useCookies' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\UserI',
            'enableAutoLogin' => true,
            'authTimeout' => 3600,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [],
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
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
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
            ],
        ],
        'i18n' => [
        'translations' => [
            'app*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
                'sourceLanguage' => 'en-US',
                //'fileMap' =>    [
                //                'app' => '@app/messages/ru/app.php',
                //                'app/error' => 'error.php',
                //                ],
                    ],
                ],
         ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    /*
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    */

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
                    'class'=>'yii\debug\Module',
                    'allowedIPs'=>['127.0.0.1', '::1', '*', '195.24.154.18'],
                    'historySize' => 16384,
                        ];



}


    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
                    'class' => 'yii\gii\Module',
                    'allowedIPs'=>['127.0.0.1', '::1', '*', '195.24.154.18'],
                    'generators' => [
                            'crud'   => [
                                'class'     => 'yii\gii\generators\crud\Generator',
                                'templates' => ['crud' => '@app/templates/gii-crud/rb'],
                            ],
                        ],
                    ];

return $config;
