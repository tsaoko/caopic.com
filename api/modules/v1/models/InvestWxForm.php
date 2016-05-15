<?php

namespace api\modules\v3\models;


use common\models\Invest;
use common\models\UserAgent;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\Project;
use common\models\Coupon;

class InvestWxForm extends Model
{

//    public $pay_password;
    public $amount;
    public $min_amount;
    public $max_amount;
    public $project_id;
    public $pay_type;
    public $rememberMe = false;
    public $is_rate=false;
    public $coupon_id;
    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['amount','rememberMe','pay_type'], 'required'],
            ['rememberMe', 'validateRem'],
            ['amount','validateAmount'],
            ['coupon_id','integer'],
//            ['pay_password', 'validatePayPassword'],
//            ['pay_password', 'filter','filter' => 'trim'],
            ['rememberMe', 'boolean'],
//            [['is_rate'], 'validateCoupon'],
        ];
    }

    public function validateRem($attribute, $params)
    {
        if (!$this->hasErrors()) {

            if( $this->rememberMe == false )
            {
                $this->addError('rememberMe','请勾选此项');
            }
        }
    }
    public function validateCoupon($attribute, $params){
        if(!$this->hasErrors()){
            $coupon = Coupon::find()->andWhere(['user_id'=>Yii::$app->user->identity->id,'type'=>Coupon::TYPE_RATE, 'status'=>Coupon::STATUS_UNUSED])->count();
            if($coupon<=0&&$this->is_rate==true){
                $this->addError($attribute, '没有可以使用的加息券');
            }
        }
    }
    public function validateAmount($attribute, $params)
    {
        if (!$this->hasErrors())
        {

            // 还没有登录
            if(  Yii::$app->user->isGuest )
            {
                $this->addError($attribute,'您还没有登录');
            }


            $user = Yii::$app->user->identity;
            $model = Project::findOne($this->project_id);
            $type = Project::$all_cate_list[$model->cate_id][$model->type_id];
            $this->min_amount = $model->start_amount;
            if(isset($type['max_amount'])){
                $this->max_amount = $type['max_amount'];
            }

            // 新手标只能投一次
           // 新手标只能投一次
            if($model->cate_id == Project::CATE_NOVICE)
            {
               if($this->amount>$this->max_amount)
                {
                    $this->addError('amount', '上限'.$this->max_amount.'元');
                }
                
               $invest = Invest::findBySql('select * from invest where user_id = :user_id and is_pay=1 and  type_id = :type_id',
                  [':user_id'=>$user->id,':type_id'=>  $model->type_id])->one();
                if(!empty($invest))
                {

                  $this->addError($attribute, '新手专享每人只能投一次');

                }

            }
            //月月升最大额度限制
           if($model->cate_id == Project::CATE_STAR)
           {
               $total_amount = Invest::findBySql('select * from invest where user_id = :user_id and is_pay=1 and  type_id = :type_id',
                  [':user_id'=>$user->id,':type_id'=>  $model->type_id])->sum('amount');
               if($total_amount+$this->amount>$this->max_amount)
               {
                    $this->addError($attribute, '该项目最大累计投资不能超过50万');
               }
           }
           

            if( $model->status!=Project::STATUS_BIDDING ){
                $this->addError('amount', '项目当前状态不可投！');
            }
            // 余额应该在于想投的金额
            if($this->pay_type == User::PAY_TYPE_LLPAY){
                if( $this->amount > $user->total_balance-$user->freeze_balance )
                {
//                    $this->addError('amount', '您的余额不足，请充值');
                }
            }else{
                if( $this->amount > $user->balance )
                {
//                    $this->addError('amount', '您的余额不足，请充值');
                }
            }
            if($this->amount<$this->min_amount)
            {
                $this->addError($attribute, '最小投资金额为'.$this->min_amount.'元');
            }

            // 要大于起投金额
            if($model->total_amount-$model->current_amount>=$this->min_amount&&$this->amount < $this->min_amount)
            {
                $this->addError('amount', '起投金额为 '.$this->min_amount.' 元');
            }
            if(floor($this->amount)!=$this->amount)
            {
                $this->addError('amount', '投资金额必须是整数');
            }
            // 当可投金额大于最小金额时 按倍数投标
            if($model->total_amount-$model->current_amount>=$this->min_amount&&$this->min_amount>0&& $this->amount % $this->min_amount != 0)
            {
                $this->addError($attribute, '投资金额必须是 '.$this->min_amount.' 的倍数');
            }

            // 可投份额
            if( $this->amount > $model->total_amount-$model->current_amount )
            {
                $ketou=$model->total_amount-$model->current_amount;
                $this->addError('amount','可投金额不足。可投金额为：'.number_format($ketou,2,'.',',').'元');
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


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        'pay_password' => '交易密码',
        'amount' => '投资金额',
        'pay_type' => '支付方式',
        'rememberMe' => '我同意《金融眼投资协议》',
        ];
    }


}