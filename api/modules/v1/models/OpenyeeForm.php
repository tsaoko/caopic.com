<?php

namespace api\modules\v3\models;

use Yii;
use yii\base\Model;
use common\models\User;



class OpenyeeForm extends Model
{
    public $realname;
    public $idsn;
    

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // realname and password are both required
            [['realname','idsn'], 'required'],

           ['realname', 'filter', 'filter' => 'trim'],
           ['realname', 'required'],
           ['realname', 'string', 'min' => 2, 'max' => 8],

           ['idsn', 'filter', 'filter' => 'trim'],
           ['idsn', 'required'],
           ['idsn', 'string','min'=>15,'max'=>18],
           ['idsn', 'unique','filter'=>['is_yee'=>'1'],'targetClass' => '\common\models\User', 'message' => '此{attribute}已经被使用'],

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
        ];

    }



}