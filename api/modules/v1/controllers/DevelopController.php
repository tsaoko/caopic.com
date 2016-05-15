<?php

namespace api\modules\v3\controllers;

use common\models\Develop;
use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;


class DevelopController extends ActiveController
{
    //发展历程
    public $modelClass = 'common\models\Develop';

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

    public function actionIndex(){
        $develop = Develop::find()->select('dateline,code,content')->orderBy(['code'=>SORT_DESC])->all();
        return ['status'=>'success','data'=>$develop,'msg'=>'操作成功'];
    }
}