<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'name' => '草皮网',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'targets' => [
             'file' => [
                 'class' => 'yii\log\FileTarget',
                 'levels' => ['trace', 'info'],
                 'categories' => ['yii\*'],
             ],
             'email' => [
                 'class' => 'yii\log\EmailTarget',
                 'levels' => ['error', 'warning'],
                 'message' => [
                     'to' => ['uutan@qq.com'],
                     'subject' => 'New caopic.com log message',
                 ],
             ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
    'params' => $params,
];
