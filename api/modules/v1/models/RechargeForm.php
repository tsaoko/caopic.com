<?php

namespace api\modules\v3\models;

use common\api\fraudmetrix\FraudMetrix;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\UserInfo;



class RechargeForm extends Model
{
    public $amount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['amount'], 'required'],
            ['amount','number'],
            ['amount','validateAmount'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => '充值金额',
        ];
    }



    public function validateAmount($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if( $this->amount < 100 )
            {
                $this->addError($attribute, '充值金额100元起步');
            }
            if( $this->amount > 50000 )
            {
                $this->addError($attribute, '充值金额单笔金额不能大于50,000元');
            }
        }
    }
}