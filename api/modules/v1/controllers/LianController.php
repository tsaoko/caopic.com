<?php
namespace api\modules\v3\controllers;

use common\api\lianlian\LianPayWap;
use common\models\Recharge;
use common\models\User;
use Yii;
use yii\helpers\Url;

class LianController extends \yii\base\Controller {

    public function beforeAction($action)
    {
        $ac = $this->action->id;
        if($ac == 'recharge')
        {
            Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
        }
        return true;
    }

    public function actionRecharge(){

        $get = Yii::$app->request->get();
        $id = isset($get['id'])?$get['id']:'';
        $code = isset($get['verify_code'])?$get['verify_code']:'';
        $is_pay = false;
        $investNo = '';
        $callback_url = isset($get['callback_url'])?$get['callback_url']:'';
        $model = Recharge::findOne($id);
        if(!$model || $code != $model->code){
            return json_encode(['status' => 'fail', 'data'=>[], 'msg' => '信息非法']);
        }
        $notify_url = Url::toRoute(['notify-llpay/process','act'=>'recharge|'.$is_pay.'|'.$investNo],true);
        Recharge::updateAll(['code'=>null],['id'=>$model->id]);
        $user = User::findOne($model->user_id);

        $pay = new LianPayWap();
        $html_text = $pay->toAuthPayApi(
            $user->id,
            $user->created_at,
            '101001',
            $model->requestNo,
            $model->amount,
            '充值',
            $model->bank_code,
            $model->card_no,
            $model->realname,
            $model->id_no,
            $notify_url,
            $callback_url,
            $model->card_no.'|'.$model->realname.'|'.$model->id_no
        );

        echo $html_text;
    }

}
