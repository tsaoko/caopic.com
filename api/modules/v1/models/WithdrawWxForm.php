<?php

namespace api\modules\v3\models;

use common\api\fraudmetrix\FraudMetrix;
use Yii;
use yii\base\Model;
use common\models\User;



class WithdrawWxForm extends Model
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
            ['amount', 'number'],
            ['amount', 'validateAmount'],
//            [['amount'],'validateAccount'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => '提现金额',
        ];
    }

    public function validateAmount($attribute, $params)
    {
        $user = Yii::$app->user->identity;

        if (!$this->hasErrors()) {
            if( $this->amount <= 0 )
            {
                $this->addError($attribute, '提现金额需要大于0');
            }
            if( $this->amount + 2 > $user->balance )
            {
                $this->addError($attribute, '提现金额不能大于可提金额：'. ($user->balance - 2>0?$user->balance - 2:0) .' 元');
            }
        }
    }

}