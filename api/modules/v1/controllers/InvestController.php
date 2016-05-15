<?php

namespace api\modules\v3\controllers;

use common\models\Invest;
use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class InvestController extends ActiveController
{
    //用户投资记录
    public $modelClass = 'api\modules\v3\models\Invest';

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

    public function actions(){
        $actions = parent::actions();
        $device = isset($_GET['device'])?$_GET['device']:'app';
        if($device == 'app'){
            unset($actions['view']);
        }
        return $actions;
    }

    public function actionView(){
        $device = isset($_GET['device'])?$_GET['device']:'app';
        $get = Yii::$app->request->get('id');
        $id = isset($get)?$get:'';

        if(empty($id))
        {
            return ['status' => 'fail', 'data' => [], 'msg' => 'id不能为空'];
        }
        if(Yii::$app->user->isGuest){
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;
        $invest = Invest::find()->andWhere(['id'=>$id,'user_id'=>$user->id])->one();
        if(!$invest){
            return ['status' => 'fail', 'data' => [], 'msg' => '数据异常'];
        }

        $model = new $this->modelClass();
        $data = $model->view($invest,$device);
        if(!empty($data)) {
            return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
        }else{
            return ['status' => 'fail', 'data' => $data, 'msg' => '操作失败'];
        }

    }

}