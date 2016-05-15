<?php
namespace api\modules\v3\models;
use common\api\fraudmetrix\FraudMetrix;
use common\models\Invest;
use common\models\Project;
use Yii;
use yii\base\Model;



class RechargeLlpayForm extends Model
{
    public $realnameLLpay;
    public $idsnLLpay;
    public $card_no;
    public $bank_code;
    public $amount;
    public $is_pay;
    public $type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['realnameLLpay','idsnLLpay','card_no','amount'], 'required'],
            [['realnameLLpay','idsnLLpay','card_no','bank_code','type'],'string'],
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
            'realnameLLpay' => '真实姓名',
            'idsnLLpay' => '身份证号',
            'amount' => '充值金额',
            'card_no'=>'银行卡号',
        ];
    }

    public function validateAmount($attribute, $params)
    {
        if (!$this->hasErrors())
        {
            if($this->is_pay){
                if($this->amount!=1)
                {
                    $this->addError('amount', '新手产品只能投1元');
                }

                $todayNumber = Invest::find()->where(['and',['>=','created_at',strtotime(date('Y-m-d'))],'type_id'=>Project::TYPE_NOVICE_1])->count();
                if( $todayNumber >= 50 )
                {
                    $this->addError('amount','今日新手标份额已满，明天请早！');
                }

            }else{
                if( $this->amount < 100 && YII_ENV == 'prod')
                {
                    $this->addError('amount', '充值金额100元起步');
                }
                if( $this->amount > 9999999 )
                {
                    $this->addError('amount', '充值金额单笔金额不能大于9999999元');
                }
            }
        }
    }
}
