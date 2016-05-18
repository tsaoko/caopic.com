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
            'traceLevel' => 3,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'except'=>['api', 'yii\db\*'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'categories'=>['api', 'yii\base\UserException'],
                    'logFile'=>'@runtime/logs/api.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info', 'trace'],
                    'categories'=>['share'],
                    'logVars'=>['_GET', '_POST', '_SERVER'],
                    'logFile'=>'@runtime/logs/share.log',
                ],
                'email'=>[
                    'class' => 'yii\log\EmailTarget',
                    'mailer' => 'mailer',
                    'levels' => ['error'],
                    'except'=> ['yii\base\UserException', 'yii\web\HttpException:404', 'yii\web\HttpException:400', 'yii\web\HttpException:403', 'common\base\*'],
                    'message' => [
                        'from' => ['no-reply@caoguo.com'],
                        'to' => ['uutan@qq.com'],
                        'subject' => 'from caopic.com new message',
                    ],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'logVars'=>['_GET', '_POST', '_FILES', '_SESSION'],
                    'except'=> ['yii\base\UserException', 'yii\web\HttpException:404', 'yii\web\HttpException:400', 'yii\web\HttpException:403', 'common\base\*'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'categories' => ['admin'],
                    'logTable' => 'adminlog',
                    'logVars'=>['_GET', '_POST', '_FILES'],
                    'prefix'=>function($message){
                        if (Yii::$app === null) {
                            return '';
                        }
                        $ip = $_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : '-';
                        /* @var $user \yii\web\User */
                        $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                        if ($user && ($identity = $user->getIdentity(false))) {
                            $userID = $identity->getId();
                        } else {
                            $userID = '-';
                        }
                        if ($user && ($identity = $user->getIdentity(false))) {
                            $userName = $identity->employee ===null ? $identity->username : $identity->employee->name;
                        } else {
                            $userName = '-';
                        }

                        return "[$ip][$userID][$userName]";
                    }
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

                'GET v1/aliyun/signature' => 'v1/aliyun/signature',
                'POST v1/aliyun/osscallback' => 'v1/aliyun/osscallback',



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
