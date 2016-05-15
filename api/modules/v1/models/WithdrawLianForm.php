<?php

namespace api\modules\v3\models;

use common\api\fraudmetrix\FraudMetrix;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\SmsList;
use common\helpers\Util;



class WithdrawLianForm extends Model
{
    public $province;
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
            [['amount','province','brabank','city'], 'required'],
            [['amount'], 'trim'],
            ['amount', 'validateAmount'],
            [['province','city'],'number'],
            ['brabank','string'],
//                 ['checkcode', 'filter', 'filter' => 'trim'],
//            ['checkcode', 'required'],
//            ['checkcode', 'validateCheckCode'],
//            ['pay_password', 'validatePayPassword'],
//            ['pay_password', 'filter','filter' => 'trim'],
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
        ];
    }

    public function validateAmount($attribute, $params)
    {
        $user = Yii::$app->user->identity;

        if (!$this->hasErrors()) {
            if(!isset($user->bank_card)){
                $this->addError($attribute, '请先充值后再提现');
            }
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
    /**
     * 验证短信
     *
     * @param  [type] $attribute [description]
     * @param  [type] $params    [description]
     * @return [type]            [description]
     */
    public function validateCheckCode($attribute, $params)
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
    public function validatePayPassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if(!isset($user->pay_password)||!Yii::$app->security->validatePassword($this->pay_password, $user->pay_password)){
            $this->addError('pay_password','交易密码不正确');
        }
    }

}