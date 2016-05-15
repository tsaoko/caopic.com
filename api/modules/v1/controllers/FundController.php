<?php
namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

class FundController extends ActiveController
{
    //项目相关
    public $modelClass = 'api\modules\v3\models\Project';
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

    public function actions(){
        $actions = parent::actions();
        unset($actions['view']);
        return $actions;
    }


    /**
     * 项目详情
     * page：分页
     * type：项目类型
     *
     * 返回：project：项目详细信息；company：信贷服务商信息；lend：借款人信息；list：投标列表
     */
    public function actionView()
    {
        $get = Yii::$app->request->get('id');
        $id = isset($get)?$get:'';
        $device = isset($_GET['device'])?$_GET['device']:'app';
        if(empty($id))
        {
            $message = 'id不能为空';
            return ['status' => 'fail', 'data' => [], 'msg' => $message];
        }

        $model = new $this->modelClass();
        $data = $model->view($id,$device);
        if($data) {
            return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
        }else{
            return ['status' => 'fail', 'data' => $data, 'msg' => '操作失败'];
        }
    }

    //项目债权信息
    public function actionZhaiquan(){
        $get = Yii::$app->request->get();
        $id = isset($get['id'])?$get['id']:'';
        if(!$id)
        {
            $message = 'id不能为空';
            return ['status' => 'fail', 'data' => [], 'msg' => $message];
        }

        $model = new $this->modelClass();
        $data = $model->viewZhaiquan($id);
        if($data) {
            return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
        }else{
            return ['status' => 'fail', 'data' => $data, 'msg' => '操作失败'];
        }
    }

    //项目投标列表
    public function actionInvest(){
        $get = Yii::$app->request->get();
        $id = isset($get['id'])?$get['id']:'';
        if(!$id)
        {
            $message = 'id不能为空';
            return ['status' => 'fail', 'data' => [], 'msg' => $message];
        }

        $model = new $this->modelClass();
        $data = $model->viewInvest($id);
        if($data) {
            return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
        }else{
            return ['status' => 'fail', 'data' => $data, 'msg' => '操作失败'];
        }
    }
}