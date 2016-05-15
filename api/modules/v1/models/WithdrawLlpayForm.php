<?php

namespace api\modules\v3\models;

use common\api\fraudmetrix\FraudMetrix;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\SmsList;
use common\helpers\Util;



class WithdrawLlpayForm extends Model
{
    public $province;
    public $mobile;
    public $city;
    public $brabank;
    public $amount;
    public $checkcode;
//    public $pay_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount'], 'required'],
            [['amount'], 'trim'],
            ['amount', 'validateAmount'],
            [['amount','province','city'],'number'],
            [['brabank'],'string'],
//            ['pay_password', 'validatePayPassword'],
//            ['pay_password', 'filter','filter' => 'trim'],
            ['checkcode','validateCheckcode'],
//            [['amount'],'validateAccount']
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'province' => '省份',
            'city' => '城市',
            'brabank' => '开户支行名称',
            'amount' => '提现金额',
            'pay_password' => '交易密码',
            'checkcode' => '短信验证码'
        ];
    }

    public function validateCheckcode($attribute, $params)
    {
        if( !$this->hasErrors() )
        {
            $ss = SmsList::find()->where(['mobile'=>$this->mobile,'type'=>'1'])->one();
            if( $ss )
            {
                if( $ss->code != $this->checkcode )
                {
                    $this->addError($attribute,'验证码不正确，请重新输入');
                }
            }else{
                $this->addError($attribute,'您还没有提交短信验证');
            }
        }
    }

    public function validateAmount($attribute, $params)
    {
        $user = Yii::$app->user->identity;

        if (!$this->hasErrors()) {
            if( $this->amount <= 0 )
            {
                $this->addError($attribute, '提现金额需要大于0');
            }
            if( $this->amount > Util::roundValue($user->availablewithdraw,2))
            {
                $this->addError($attribute, '提现金额不能大于可提金额：'.Util::roundValue($user->availablewithdraw,2).' 元');
            }
        }
    }

    public function validatePayPassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if(!isset($user->pay_password)||!Yii::$app->security->validatePassword($this->pay_password, $user->pay_password)){
            $this->addError('pay_password','交易密码不正确');
        }
    }

}