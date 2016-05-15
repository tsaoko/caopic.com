<?php

namespace api\modules\v3\controllers;
use common\api\lianlian\LianPayWap;
use common\api\lianlian\LLpayNotify;
use common\models\LianNotify;
use Yii;
use common\helpers\Util;
use api\modules\v3\component\ActiveController;

class NotifyLlpayController extends ActiveController
{
    public $enableCsrfValidation = false;

    public $modelClass = 'common\models\User';

    public function actionProcess()
    {
        $llWeb = new LianPayWap();
        $llpayNotify = new LLpayNotify($llWeb->getConfig());
        $llpayNotify->verifyNotify();
        if (!isset($llpayNotify->notifyResp)) {
            Util::log("缺少参数notifyResp|sign");
            exit();
        }
        $val = $llpayNotify->notifyResp;
        if (!$llpayNotify->result) {
            Util::log("验签失败。" . print_r($val, true));
            die("{'ret_code':'9999','ret_msg':'验签失败'}");
        }
        $temp = explode('|',\Yii::$app->request->get('act'));
        $action = $temp[0];
        $is_pay =  $temp[1];
        $requestNo = $temp[2];
        Util::log("\n\n--------后台访问：m/notify->" . $action .'|'.$is_pay.'----------------------');
        Util::log('异步数据：' . print_r($val, true));

        $flag = false;
        switch ($action) {
            case 'recharge':
                $flag = LianNotify::rechargeLlpay($val,$is_pay,$requestNo);
                break;
            case 'withdraw':
                $flag = LianNotify::withdrawLlpay($val);
                break;

        }
        if ($flag) {
            die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
        }
    }
}