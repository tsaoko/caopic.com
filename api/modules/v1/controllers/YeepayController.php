<?php

namespace api\modules\v3\controllers;

use Yii;
use api\modules\v3\component\ActiveController;
use common\api\yeepay\YeePayMobile;
use common\helpers\Util;
use common\models\YeepayNotify;
use common\models\Recharge;
use common\models\Withdraw;


class YeepayController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public $enableCsrfValidation = false;

    /**
     * 异步通知
     *
     * @return [type] [description]
     */
    public function actionNotify()
    {
        Util::log('------异步回调开始'."------\n");
        $post = $_POST;

        if (!isset($post["sign"]) || !isset($post["notify"])) {
            Util::log("缺少参数notify|sign");
            exit();
        }

        $yee = new YeePayMobile;
        $verify = $yee->verify($post["notify"], $post["sign"]);
        $notify = $yee->getResult($post["notify"]);
        $action = \Yii::$app->request->get('act');
        $code = '';

        if(count($temp = explode('|',\Yii::$app->request->get('act')))>1){
            $action = $temp[0];
            $code = $temp[1];
        }

        // TODO 这里需要将所有数据打印记录起来
        Util::log('------异步回调：'.$action."------\n");
        Util::log('回调数据：'.var_export($post,true)."\n");

        if ($notify->code != '1') {
            Util::log("回调返回：" . var_export($notify, true));
            die;
        }

        $flag=false;
        // TODO 后续根据类型处理反馈情况
        switch ($action)
        {
            case 'register':
                $flag= YeepayNotify::register($notify->platformUserNo);
                break;
            case 'recharge':
                $flag=YeepayNotify::recharge($notify->requestNo, $notify->amount, $notify->fee, $notify->feeMode,$verify);
                break;
            case 'withdraw':
                $flag=YeepayNotify::withdraw($notify->requestNo, $notify->amount, $notify->fee, $notify->feeMode,$verify);
                break;
            case 'project_invest':
                $flag=YeepayNotify::projectInvest($notify->requestNo, $verify,$code);
                break;
            case 'modify_mobile':
                $flag = YeepayNotify::modify_mobile($notify->platformUserNo,$code);
                break;
        }
        if($flag)
        {
            echo "SUCCESS";
            exit();
        }
    }


    /**
     * 后台反馈
     */
    public function actionCallback()
    {
        $post = $_POST;

        if (!isset($post["sign"]) || !isset($post["resp"])) {
            Util::log("缺少参数notify|sign");
            exit();
        }
        $yee = new YeePayMobile;

        $verify = $yee->verify($post['resp'], $post['sign']);

        $data = $yee->getResult($post['resp']);

        $action = \Yii::$app->request->get('act');
        $temp = explode('|',\Yii::$app->request->get('act'));
        $invite_code = '';
        if(count($temp)>1) {
            $action = $temp[0];
            $invite_code = $temp[1];
        }
        Util::log("\n\n--------前台访问：frontend/callback->" . $action . '----------------------');

        $flag=false;
        $msg="";
        $msg_content="";
        $url='';

        //返回结果是否成功
        $success = true;
        $requestNo = isset($data->requestNo)?$data->requestNo:-1;
        Util::log("同步返回：" . print_r($data, true));
        if(!$verify){
            Util::log("验签失败。" . $requestNo);
            $success =  false;
        }
        if($data->code != 1){
            Util::log("操作失败。" . $requestNo);
            $success =  false;
        }
        switch ($action)
        {
            case 'register':
                $msg='开通易宝托管';
                $flag = $success;
                break;
            case 'recharge':
                $msg='充值';
                if($success) {
                    $flag=$this->callbackrecharge($requestNo);
                }
                break;
            case 'withdraw':
                $msg='提现';
                if($success) {
                    $flag=$this->callbackwithdraw($requestNo);
                }
                break;
            case 'project_invest':
                $msg='投资';
                if($success) {
                    $flag = YeepayNotify::projectInvest($requestNo, $verify,$invite_code);
                }
                break;
            case 'modify_mobile':
                $msg='修改手机号码';
                $flag = $success;
                break;
        }
        $key = $flag?'success':'error';
        $msg=$flag?$msg."成功":$msg.'失败';
        $msg=$msg.'.'.$msg_content;

        return $this->render('index',['status'=>$key,'message'=>$msg]);
    }

    private function  callbackwithdraw($requestNo)
    {
        $order = Withdraw::findBySql("SELECT * FROM withdraw WHERE requestNo = :requestNo", array(':requestNo' => $requestNo))->one();
        if (!$order) {
            Util::log("订单不存在。" . $requestNo);
            return false;
        }

        if ($order->status == Withdraw::STATUS_REPLAY || $order->status == Withdraw::STATUS_YES) {
//            WeixinService::sendContent(2,'http://m.faxeye.com/user/withdraw',Withdraw::STATUS_YES,$order->amount,$order->fee,time());
            return true;
        } else {
            Util::log('订单存在，但状态为：' . $order->status);
            return false;
        }
    }

    private function callbackrecharge($requestNo)
    {
        $order = Recharge::findBySql("SELECT * FROM recharge WHERE requestNo = :requestNo", array(':requestNo' => $requestNo))->one();
        if (!$order) {
            Util::log("订单不存在。" . $requestNo);
            return false;
        }

        if ($order->status == Recharge::STATUS_REPLAY || $order->status == Recharge::STATUS_YES) {
//            WeixinService::sendContent(3,'http://m.faxeye.com/user/recharge',$order->amount,time());
            return true;
        } else {
            Util::log('订单存在，但状态为：' . $order->status);
            return false;
        }

    }
}
