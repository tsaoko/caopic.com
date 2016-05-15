<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%site}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $logo
 * @property integer $is_wall
 */
class Site extends \yii\db\ActiveRecord
{

    public $modelName = '站点';

    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%site}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'is_wall'], 'integer'],
            [['title', 'url', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'url' => '网址',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
            'logo' => 'LOGO',
            'is_wall' => '是否需要翻墙',
        ];
    }
}
