<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class DocumentController extends ActiveController
{
    //关于我们
    public $modelClass = 'api\modules\v3\models\Document';

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