<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%share_tags}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $count
 * @property integer $created_at
 * @property integer $updated_at
 */
class ShareTags extends \yii\db\ActiveRecord
{

    public $modelName = '分享标签';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%share_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'count', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '标签名',
            'count' => '统计',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
