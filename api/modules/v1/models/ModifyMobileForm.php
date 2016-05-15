<?php

namespace api\modules\v3\models;

use common\api\fraudmetrix\FraudMetrix;
use common\helpers\Util;
use common\models\SmsList;
use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Login form
 */
class ModifyMobileForm extends Model {

    public $realname;
    public $idsn;
    public $mobile;
    public $checkcode;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['realname','idsn','mobile','checkcode'],'required'],

            ['mobile', 'match', 'pattern' => '/^1[3|5|7|8|][0-9]{9}$/', 'message' => '手机号码格式不正确'],
            ['mobile', 'unique', 'targetClass' => 'common\models\User', 'message' => '手机号码已存在'],

            ['checkcode', 'filter', 'filter' => 'trim'],
            ['checkcode', 'validateCheckCode'],

            ['realname','validateReal'],
        ];
    }

    public function attributeLabels() {
        return [
            'realname' => '真实姓名',
            'idsn' => '证件号码',
            'mobile' => '新手机号码',
            'checkcode'=>'手机验证码'
        ];
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

    //判断用户身份
    public function validateReal($attribute, $params){
        if( !$this->hasErrors() )
        {
            $user = User::find()->andWhere(['idsn'=>$this->idsn])->one();
            $user_lian = User::find()->andWhere(['idsnLLpay'=>$this->idsn])->one();
            if((!$user&&!$user_lian)||($user->realname!=$this->realname && $user_lian->realnameLLpay!=$this->realname) ){
                $this->addError($attribute,'身份信息不正确');
            }
        }
    }

}
