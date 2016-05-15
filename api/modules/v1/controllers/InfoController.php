<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class InfoController extends ActiveController
{
    //资讯中心
    public $modelClass = 'api\modules\v3\models\Info';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $header = Yii::$app->request->headers->get('Authorization');
        if ($header || isset($_GET['access_token'])) {
            $behaviors['authenticator'] = [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    ['class' => HttpBearerAuth::className()],
                    ['class' => QueryParamAuth::className(), 'tokenParam' => 'accessToken'],
                ]
            ];
        }

        return $behaviors;
    }
}