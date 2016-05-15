<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Detail extends \common\models\Detail
{
    public function fields(){
        $fields = parent::fields();
        $fields['type_name'] = function($model){
            return isset(self::$type_list[$model->type])?self::$type_list[$model->type]:$model->type;
        };
        $fields['amount'] = function($model){
            return number_format($model->amount,2);
        };
        $fields['balance'] = function($model){
            return number_format($model->balance,2);
        };
        // 判断是否为支出
        $fields['pro'] = function($model){
            if(substr($model->amount,0,1) == '-') {
                return '-';
            }else{
                return '+';
            }
        };

        $fields['created_at'] = function($model){
            return date('Y-m-d H:i:s',$model->created_at);
        };
        $fields['updated_at'] = function($model){
            return date('Y-m-d H:i:s',$model->updated_at);
        };
        $fields['pay_type_name'] = function($model){
            return isset(User::$pay_type_list[$model->pay_type])?User::$pay_type_list[$model->pay_type]:'';
        };
        return $fields;
    }
}