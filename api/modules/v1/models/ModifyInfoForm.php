<?php
namespace api\modules\v3\models;

use common\api\fraudmetrix\FraudMetrix;
use Yii;
use yii\base\Model;
use common\models\User;
use common\models\SmsList;



class ModifyInfoForm extends Model
{
    public $gender;
    public $contact;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gender','contact'],'required'],
            ['contact', 'filter', 'filter' => 'trim'],
            ['contact', 'match','pattern'=>'/^1[3|5|7|8|][0-9]{9}$/','message'=>'手机号码格式不正确'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gender' => '性别',
            'contact' => '紧急联系人',
        ];
    }
}
