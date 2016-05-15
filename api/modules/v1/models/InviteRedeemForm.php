<?php
namespace api\modules\v3\models;


use common\models\CurrentMain;
use Yii;
use yii\base\Model;
use common\models\Currency;

class InviteRedeemForm extends Model
{

    public $amount;
    public $pay_type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount','pay_type'], 'required'],
            [['amount'], 'trim'],
            [['amount'], 'validateAmount'],
        ];
    }


    public function validateAmount($attribute, $params)
    {
        if (!$this->hasErrors()) {
     
            $user = Yii::$app->user->identity;
            //可提取提成
            $amount = $user->inviteamount;

            if($this->amount<=0)
            {
                $this->addError($attribute, '提现金额不正确');
                return false;
            }

//            if($amount-$this->amount<50&&$amount-$this->amount>0)
//            {
//                $this->addError($attribute, '您的提成所剩金额不能小于50元');
//                return false;
//            }
            
            if(!$amount || $amount < $this->amount )
            {
                $this->addError($attribute,'提成可取资金不足');
                return false;
            }
        }
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        'amount' => '您要赎回的金额',
        'pay_type' => '帐户类型',
        ];
    }



}