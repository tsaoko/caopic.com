<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'name' => '草皮接口',
    'language' => 'zh-CN',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [

        'v1' => [
            'class' => 'api\modules\v1\Module'
        ],

        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'tokenParamName' => 'access_token',
            'tokenAccessLifetime' => 3600 * 24,
            'storageMap' => [
                'user_credentials' => 'api\modules\v1\models\UserClass',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ]
            ]
        ]

    ],

    'components' => [

        'user' => [
            'identityClass' => 'common\models\User',
            'enableSession' => false,
            'loginUrl' => null,
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                ],
            ],
        ],

        'errorHandler' => [
            'errorAction' => 'v1/site/error',
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,

            'rules' => [

                //v1
                'GET  v1/site/error' => 'v1/site/error',
                'POST v1/site/login' => 'v1/site/login',
                'POST v1/site/isexit' => 'v1/site/isexit',
                'POST v1/site/refresh' => 'v1/site/refresh',//重新获取access_token
                'GET  v1/site/logout' => 'v1/site/logout',
                'POST v1/site/register' => 'v1/site/register',

                'POST v1/user/invest' => 'v1/user/invest',//投资
                'POST v1/user/info' => 'v1/user/info',//用户基本信息修改
                'POST v1/user/mobile' => 'v1/user/mobile',//手机号码修改

                'GET,HEAD <module:\w+>/<controller:\w+>/index' => '<module>/<controller>/index',//获取数据
                'GET,HEAD <module:\w+>/<controller:\w+>/list' => '<module>/<controller>/list',//获取数据列表
                'GET,HEAD <module:\w+>/<controller:\w+>/view' => '<module>/<controller>/view', //获取数据详情
                'GET,HEAD <module:\w+>/<controller:\w+>/view/<id:\d+>' => '<module>/<controller>/view', //获取数据详情

                'POST oauth2/<action:\w+>' => 'oauth2/rest/<action>',

            ],
        ],
        'request' => [
            'enableCookieValidation'=>false,
            'enableCsrfValidation'=>false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
    ],
    'params' => $params,
];
