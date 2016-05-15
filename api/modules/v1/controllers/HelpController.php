<?php

namespace api\modules\v3\controllers;

use common\models\Forum;
use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class HelpController extends ActiveController
{
    //帮助中心
    public $modelClass = 'api\modules\v3\models\help';

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

    public function actionForums(){
        $data =  Forum::getAll();
        return  ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
    }
}