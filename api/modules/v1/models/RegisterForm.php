<?php

namespace api\modules\v3\models;

use common\helpers\Util;
use common\models\Invite;
use common\models\User;
use Yii;
use yii\base\Model;
use common\models\SmsList;
use common\api\fraudmetrix\FraudMetrix;


class RegisterForm extends Model
{
    //public $realname;
    //public $idsn;
    public $mobile;
    public $checkcode;
    public $password;
    public $chkpassword;
    public $invitecode;
    public $email;
    public $captcha;

    public $rememberMe = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // realname and password are both required
            [['mobile','checkcode', 'chkpassword', 'password','rememberMe', 'email'], 'required'],

            // 实名认证
            [['mobile','password'],'validateRealname'],
            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile', 'required'],
            ['mobile', 'match','pattern'=>'/^1[3|5|7|8|][0-9]{9}$/','message'=>'手机号码格式不正确'],
            ['mobile', 'unique', 'targetClass' => '\common\models\User', 'message' => '此{attribute}已经被使用'],

            ['checkcode', 'filter', 'filter' => 'trim'],
            ['checkcode', 'required'],
            ['checkcode', 'validateCheckCode'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['invitecode', 'filter', 'filter' => 'trim'],
            ['invitecode', 'match','pattern'=>'/^1[3|5|7|8|][0-9]{9}$/','message'=>'推荐人手机号格式不正确'],
            [['invitecode'], 'validateInviteCode'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => '此{attribute}已经被使用'],

            ['chkpassword', 'compare', 'compareAttribute'=>'password','message'=>'两处输入的密码并不一致'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {

        return [
            'realname' => '真实姓名',
            'idsn' => '身份证号',
            'mobile' => '手机号',
            'checkcode' => '短信验证码',
            'email' => '邮箱',
            'password' => '登录密码',
            'chkpassword' => '确认登录密码',
//            'captcha' => '图形验证码',
            'rememberMe' => '我同意《平台使用协议》和《账户使用协议》',
            'invitecode' => '邀请人'
        ];

    }

    /**
     * 实名认証
     *
     * @param  [type] $attribute [description]
     * @param  [type] $params    [description]
     * @return [type]            [description]
     */
    public function validateRealname($attribute, $params)
    {
        if( !$this->hasErrors() )
        {
            if( $this->rememberMe == false )
            {
                $this->addError('rememberMe','请勾选此项');
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

    /**
     * 验证邀请人
     *
     * @param  [type] $attribute [description]
     * @param  [type] $params    [description]
     * @return [type]            [description]
     */
    public function validateInviteCode($attribute, $params)
    {
        if( !$this->hasErrors() )
        {
            if($this->invitecode == $this->mobile){
                $this->addError($attribute,'不能邀请自己');
            }

            $user = User::find()->andWhere(['mobile'=>$this->invitecode])->one();
            if(!$user){
                $this->addError($attribute,'此邀请人不存在，请重新输入');
            }
        }
    }


}