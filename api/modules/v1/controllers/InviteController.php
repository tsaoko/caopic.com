<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class InviteController extends ActiveController
{
    //邀请相关
    public $modelClass = 'api\modules\v3\models\Invite';

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

    //邀请首页
    public function actionIndex(){
        if(Yii::$app->user->isGuest){
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }
        $user = Yii::$app->user->identity;
        $get = Yii::$app->request->get();
        $device = isset($get['device'])?$get['device']:'app';

        $model = new $this->modelClass();
        $data = $model->index($user,$device);

        return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
    }

    //微信专属二维码
    public function actionShare(){
        if(Yii::$app->user->isGuest){
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }
        $user = Yii::$app->user->identity;

        $model = new $this->modelClass();
        $data = $model->share($user);

        return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
    }
}