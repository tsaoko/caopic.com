<?php
/**
 * @Author: forecho
 * @Date:   2015-01-30 23:01:28
 * @Last Modified by:   forecho
 * @Last Modified time: 2015-01-31 21:08:34
 */

namespace api\modules\v3\models;

use common\components\Mailer;
use yii\base\Model;

class InfoForm extends Model
{
    public $gender;
    public $birthday;
    public $province;
    public $city;
    public $contact_name;
    public $contact_mobile;

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['gender','integer'],
            [['birthday','province','city','contact_name'],'string'],
            ['contact_mobile', 'match', 'pattern' => '/^1[3|5|7|8|][0-9]{9}$/', 'message' => '手机号码格式不正确'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'gender'            => '性别',
            'birthday'         => '出生年月',
            'province'     => '省份',
            'city'          => '城市',
            'address' => '地址',
            'contact_name' => '联系人姓名',
            'contact_mobile' => '联系人电话'
        ];
    }
}
