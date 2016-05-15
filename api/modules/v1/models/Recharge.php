<?php

namespace api\modules\v3\models;

use common\api\lianlian\LianPayWap;
use common\api\lianlian\LianPayWeb;
use common\api\yeepay\YeePayPc;
use common\helpers\Util;
use Yii;
use common\models\User;
use yii\helpers\Url;

class Recharge extends \common\models\Recharge
{
    public function fields(){
        $fields = parent::fields();
        $fields['amount'] = function($model){
            return number_format($model->amount,2);
        };
        $fields['pay_type_name'] = function($model){
            return isset(self::$pay_type_list[$model->pay_type])?self::$pay_type_list[$model->pay_type]:'';
        };
        $fields['create_at'] = function($model){
            return date('Y-m-d H:i:s',$model->create_at);
        };
        $fields['update_at'] = function($model){
            return date('Y-m-d H:i:s',$model->update_at);
        };
        return $fields;
    }


    /**
     * 使用连连支付api
     *
     * @return RechargeLlpayForm
     */
    public static function rechargeLlpayApi($model, $is_pay=false, $order_no='')
    {
        $user = Yii::$app->user->identity;

        $pay = new LianPayWeb();

        //检测银行卡信息
        $result = $pay->queryBankCard($model->card_no);
        if(!isset($result->ret_code)||!isset($result->bank_code)||$result->ret_code != '0000'){
            return ['status' => 'fail', 'data'=>[], 'msg' => '银行卡信息不符'];
        }
        $banks = Yii::$app->params['lian_auth_bank'];
        if(!isset($banks[$result->bank_code]) && $model->type == Recharge::PAY_TYPE_AUTH){
            return ['status' => 'fail', 'data'=>[], 'msg' => '快捷支付不支持此家银行'];
        }

        $pay = new LianPayWeb();

        $requestNo = 'faxeye-recharge-'.Util::rand();

        $notify_url = Url::toRoute(['notify-llpay/process','act'=>'recharge|'.$is_pay.'|'.$order_no],true);
        $callback_url = Url::toRoute(['callback-llpay/process','act'=>'recharge|'.$is_pay.'|'.$order_no],true);
        $feeMode = 'PLATFORM';

        // 手续费
        if($model->type == Recharge::PAY_TYPE_AUTH){
            $fee = Recharge::getFee($model->amount,'quick_recharge_lian');
        } else {
            $fee = Recharge::getFee($model->amount,'web_recharge_lian');
        }

        $re = new Recharge;
        $re->user_id = $user->id;
        $re->requestNo = $requestNo;
        $re->amount = $model->amount;
        $re->feeMode = $feeMode;
        $re->fee = $fee; // 平台出
        $re->status = Recharge::STATUS_REPLAY;
        $re->create_at = $re->update_at = time();
        $re->remark = '';
        $re->bank_code = $result->bank_code;
        $re->card_no = $model->card_no;
        $re->pay_type = $model->type;

        if( $re->save(false) )
        {
            $pay->toAuthPay(
                $user->id,
                $user->created_at,
                '101001',
                $requestNo,
                $model->amount,
                '充值',
                $model->type=='D'?$model->bank_code:'',
                $model->type=='D'?$model->card_no:'',
                $model->type=='D'?$model->realnameLLpay:'',
                $model->type=='D'?$model->idsnLLpay:'',
                $model->type,
                $notify_url,
                $callback_url,
                $model->card_no.'|'.$model->realnameLLpay.'|'.$model->idsnLLpay
            );
            return ['status' => 'success', 'data'=>[], 'msg' => '充值成功'];
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => '充值失败'];
        }
    }

    /**
     * 使用易宝支付api
     *
     * @return RechargeLlpayForm
     */
    public static function rechargeYeepayApi()
    {
        $model = new RechargeForm();
        $user = Yii::$app->user->identity;

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            //最小充值金额
            $requestNo = 'faxeye-recharge-'.Util::rand();

            $notify_url = Url::toRoute(['notify/process','act'=>'recharge'],true);
            $callback_url = Url::toRoute(['callback/process','act'=>'recharge'],true);
            $feeMode = 'PLATFORM';

            // TODO 添加充值操作记录
            // 手续费情况
            //$fee = Recharge::getFee($model->amount);
            $fee = '0';

            $re = new Recharge;
            $re->user_id = $user->id;
            $re->requestNo = $requestNo;
            $re->amount = $model->amount;
            $re->feeMode = $feeMode;
            $re->fee = $fee; // 用户自己出
            $re->status = Recharge::STATUS_REPLAY;
            $re->create_at = $re->update_at = time();
            $re->pay_type = Recharge::PAY_TYPE_YEE;
            $re->remark = '';

            if( $re->save(false) )
            {
                // 跳转至第三方托管
                $pay = new YeePayPc();
                return $pay->toRecharge(
                    $user->yeepayUserNo,
                    $requestNo,
                    $notify_url,
                    $callback_url,
                    $model->type,
                    $feeMode,
                    $model->amount
                );
            }
        }
        return $model;
    }

    /**
     * 微信使用连连支付api
     *
     * @return RechargeLlpayForm
     */
    public static function rechargeWxLlpayApi($model,$return_url='', $is_pay=false,$project='',$notify_utl='')
    {
        $user = Yii::$app->user->identity;
        $model->is_pay = $is_pay;

        $investNo='';
        $pay = new LianPayWap();

        $result = $pay->queryBankCard($model->card_no);
        if(!isset($result->ret_code)||$result->ret_code != '0000'){
            return ['status' => 'fail', 'data'=>[], 'msg' => '银行卡号信息错误'];
        }

        $bank_code = $result->bank_code;
        $banks = Yii::$app->params['lian_auth_bank'];
        if(!isset($banks[$bank_code])){
            return ['status' => 'fail', 'data'=>[], 'msg' => '认证支付不支持此家银行'];
        }

        $requestNo = 'faxeye-recharge-'.Util::rand();

        $callback_url = $return_url?:Url::toRoute(['callback/process','act'=>'recharge|'.$is_pay.'|'.$investNo],true);
        $feeMode = 'PLATFORM';

        $fee = $model->type==Recharge::PAY_TYPE_AUTH?Recharge::getFee($model->amount,'quick_recharge_lian'):Recharge::getFee($model->amount,'web_recharge_lian');
        $re = new Recharge;
        $re->user_id = $user->id;
        $re->requestNo = $requestNo;
        $re->amount = $model->amount;
        $re->feeMode = $feeMode;
        $re->fee = $fee; // 用户自己出
        $re->status = Recharge::STATUS_REPLAY;
        $re->create_at = $re->update_at = time();
        $re->remark = '';
        $re->bank_code = $bank_code;
        $re->card_no = $model->card_no;
        $re->pay_type = Recharge::PAY_TYPE_AUTH;
        $re->code = Util::random_keys(10);
        $re->id_no = $model->realnameLLpay;
        $re->realname = $model->idsnLLpay;

        if( $re->save() )
        {
            $url = Url::toRoute(['/v3/lian/recharge','id'=>$re->id,'callback_url'=>$callback_url,'verify_code'=>$re->code],true);
            return ['status' => 'success', 'data'=>$url, 'msg' => '充值成功'];
        }else{
            return ['status' => 'fail', 'data'=>[], 'msg' => '充值失败'];
        }
    }
}