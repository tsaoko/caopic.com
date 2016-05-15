<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_avatar}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $image
 * @property integer $is_default
 * @property string $created_at
 */
class UserAvatar extends \yii\db\ActiveRecord
{

    public $modelName = '用户头像';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_avatar}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'is_default'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['created_at'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '关联用户表',
            'image' => '头像地址',
            'is_default' => '是否为默认',
            'created_at' => '添加时间 ',
        ];
    }


    public function getUser()
    {
        return $this->one(User::className(),['user_id'=>'id']);
    }


}
