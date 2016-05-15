<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%resource}}".
 *
 * @property integer $id
 * @property string $provider
 * @property string $name
 * @property string $filename
 * @property integer $size
 * @property string $type
 */
class Resource extends \yii\db\ActiveRecord
{

    public $modelName = '资源';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%resource}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['size'], 'integer'],
            [['provider', 'name', 'filename'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provider' => '来源',
            'name' => '上传时的文件名',
            'filename' => '源地址',
            'size' => '大小',
            'type' => '类型',
        ];
    }
}
