<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_info}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $email
 * @property integer $is_email_check
 * @property string $mobile
 * @property integer $is_mobile_cechk
 * @property string $realname
 * @property string $info
 * @property integer $prev_login_time
 * @property string $prev_login_ip
 * @property string $prev_login_device
 * @property integer $last_login_time
 * @property string $last_login_ip
 * @property string $last_login_device
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $sex
 */
class UserInfo extends \yii\db\ActiveRecord
{

    public $modelName = '用户信息';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'is_email_check', 'is_mobile_cechk', 'prev_login_time', 'last_login_time', 'created_at', 'updated_at', 'sex'], 'integer'],
            [['info'], 'string'],
            [['email', 'realname'], 'string', 'max' => 255],
            [['mobile', 'prev_login_ip', 'prev_login_device', 'last_login_ip', 'last_login_device'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'email' => '邮箱',
            'is_email_check' => '是否通过验证',
            'mobile' => '手机号',
            'is_mobile_cechk' => '手机是否通过验证',
            'realname' => 'Realname',
            'info' => '个人信息',
            'prev_login_time' => '上次登录时间',
            'prev_login_ip' => '上次登录IP',
            'prev_login_device' => '上回登录设备',
            'last_login_time' => '最后一次登录时间 ',
            'last_login_ip' => '最后一次登录IP',
            'last_login_device' => '最后一次登录使用的设备',
            'created_at' => '添加时间 ',
            'updated_at' => '修改时间 ',
            'sex' => '性别',
        ];
    }


    public function getUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'id']);
    }

}
