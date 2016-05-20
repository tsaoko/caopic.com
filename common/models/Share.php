<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%share}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property integer $resource_id
 * @property string $desc
 * @property string $source
 * @property integer $site_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $level
 * @property integer $user_share_group_id
 */
class Share extends \yii\db\ActiveRecord
{

    public $modelName = '分享';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%share}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'resource_id', 'site_id', 'created_at', 'updated_at', 'level', 'user_share_group_id'], 'integer'],
            [['resource_id', 'user_share_group_id'], 'required'],
            [['desc', 'source'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'title' => '标题',
            'resource_id' => '资源ID',
            'desc' => '描述',
            'source' => '采集来源',
            'site_id' => '来源站关联',
            'created_at' => '采集时间',
            'updated_at' => '更新时间 ',
            'level' => '分级',
            'user_share_group_id' => '用户分组',
        ];
    }


    public function getResource()
    {
        return $this->hasOne(Resource::className(),['resource_id'=>'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(),['user_id'=>'id']);
    }

    public function getSite()
    {
        return $this->hasOne(Site::className(),['site_id'=>'id']);
    }


    public function getShareGroup()
    {
        return $this->hasOne(ShareGroup::className(),['user_share_group_id' => 'id']);
    }

}
