<?php
namespace api\modules\v3\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\SmsList;


class ForgetPasswordForm extends Model
{
    public $mobile;
    public $password;
    public $chkpassword;
    public $checkcode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile','checkcode', 'password', 'chkpassword'], 'required'],

            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile', 'required'],
            ['mobile', 'match','pattern'=>'/^1[3|5|7|8|][0-9]{9}$/','message'=>'手机号码格式不正确'],
            // username and password are both required
            [['password', 'chkpassword'], 'required'],
            // rememberMe must be a boolean value

            ['checkcode', 'filter', 'filter' => 'trim'],
            ['checkcode', 'validateCheckCode'],

            ['password', 'string', 'min' => 6],
            ['chkpassword', 'compare', 'compareAttribute' => 'password', 'message' => '两次输入的密码不一致'],
            [['chkpassword','password'], 'filter', 'filter' => 'trim'],

            // 
        ];
    }





    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号',
            'password' => '设置密码',
            'chkpassword' => '确认密码',
            'checkcode' => '验证码',
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->mobile);
        }

        return $this->_user;
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


}
