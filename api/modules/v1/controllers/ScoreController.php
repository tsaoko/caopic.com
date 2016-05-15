<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class ScoreController extends ActiveController
{
    //积分记录
    public $modelClass = 'api\modules\v3\models\Score';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ['class' => HttpBearerAuth::className()],
                ['class' => QueryParamAuth::className(), 'tokenParam' => 'access_token'],
            ]
        ];

        return $behaviors;
    }
}