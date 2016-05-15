<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_account}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $provider
 * @property string $client_id
 * @property string $data
 * @property integer $created_at
 */
class UserAccount extends \yii\db\ActiveRecord
{

    public $modelName = '用户第三方账号';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_account}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['data'], 'string'],
            [['provider', 'client_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '关联表',
            'provider' => '授权提供商',
            'client_id' => '第三方ID',
            'data' => '开放的数据',
            'created_at' => '授权时间',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'id']);
    }

}
