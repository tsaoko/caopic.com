<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%share_group}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $sort
 * @property string $desciption
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $image
 */
class ShareGroup extends \yii\db\ActiveRecord
{

    public $modelName = '分享小组';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%share_group}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'sort', 'created_at', 'updated_at'], 'integer'],
            [['desciption'], 'string'],
            [['name', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '关联用户',
            'name' => '分类名称',
            'sort' => '排序',
            'desciption' => '描述',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
            'image' => '头像',
        ];
    }


    public function getUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'id']);
    }


}
