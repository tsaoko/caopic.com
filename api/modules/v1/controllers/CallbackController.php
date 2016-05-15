<?php

namespace api\modules\v3\controllers;

use api\modules\v3\component\ActiveController;
use common\api\lianlian\LianPayWap;
use common\api\lianlian\LLpayNotify;
use common\models\Recharge;
use yii;
use common\helpers\Util;

class CallbackController extends ActiveController
{
    public $enableCsrfValidation = false;
    public function actionProcess()
    {
        $data = json_decode($_POST['res_data']);

        if (!isset($data->result_pay)) {
            Util::log("缺少参数result_pay|sign");
            exit();
        }

        $lian = new LianPayWap();
        $temp = explode('|',\Yii::$app->request->get('act'));
        $action = $temp[0];
        $is_pay = $temp[1];
        $order_no = $temp[2];
        Util::log("\n\n--------前台访问：frontend/callback->" . $action . '----------------------');

        $flag=false;
        $msg="";
        $msg_content="";
        $url='/site/index';

        //返回结果是否成功
        $success = true;
        $requestNo = isset($data->no_order)?$data->no_order:-1;
        Util::log("同步返回：" . print_r($data, true));
        $llpayNotify = new LLpayNotify($lian->getConfig());
        $verify_result = $llpayNotify->verifyWapReturn();
        if(!$verify_result){
            Util::log("验签失败。" . $requestNo);
            $success =  false;
        }
        if($data->result_pay != 'SUCCESS'){
            Util::log("操作失败。" . $requestNo);
            $success =  false;
        }

        switch ($action) {
            case 'recharge': 
                $msg='充值';
                if($success){
                    $flag = $this->recharge($requestNo);
                    $url = '/fund/index';
                }
                break;
        }

        $key = $flag?'success':'error';
        $msg=$flag?$msg."成功":$msg.'失败';
        $msg=$msg.'.'.$msg_content;
        $this->setFlash($key, $msg);
        return $this->redirect($url);
    }

    private function recharge($requestNo)
    {
        $order = Recharge::findBySql("SELECT * FROM recharge WHERE requestNo = :requestNo", array(':requestNo' => $requestNo))->one();
        if (!$order) {
            Util::log("订单不存在。" . $requestNo);
            return false;
        }

        if ($order->status == Recharge::STATUS_REPLAY || $order->status == Recharge::STATUS_YES) {
            return true;
        } else {
            Util::log('订单存在，但状态为：' . $order->status);
            return false;
        }

    }
}
