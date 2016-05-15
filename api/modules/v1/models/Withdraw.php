<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Withdraw extends \common\models\Withdraw
{
    public function fields(){
        $fields = parent::fields();
        $fields['amount'] = function($model){
            return number_format($model->amount,2);
        };
        $fields['pay_type_name'] = function($model){
            return isset(self::$pay_type_list[$model->pay_type])?self::$pay_type_list[$model->pay_type]:'';
        };
        $fields['create_at'] = function($model){
            return date('Y-m-d H:i:s',$model->create_at);
        };
        $fields['update_at'] = function($model){
            return date('Y-m-d H:i:s',$model->update_at);
        };
        return $fields;
    }
}