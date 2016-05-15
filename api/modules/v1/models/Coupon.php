<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Coupon extends \common\models\Coupon
{
    public function fields(){
        $fields = parent::fields();
        unset($fields['code']);
        $fields['user_name'] = function($model){
            return User::getUsername($model->user_id);
        };
        $fields['type_name'] = function($model){
            return isset(self::$type_list[$model->type])?self::$type_list[$model->type]:'';
        };
        $fields['status_name'] = function($model){
            return isset(self::$status_list[$model->status])?self::$status_list[$model->status]:'';
        };

        $fields['vip'] = function($model){
            return $model->is_vip ? '是' : '否';
        };

        $fields['active_time'] = function($model){
            return date('Y-m-d',$model->active_time);
        };
        $fields['created_at'] = function($model){
            return date('Y-m-d H:i:s',$model->created_at);
        };
        $fields['updated_at'] = function($model){
            return date('Y-m-d H:i:s',$model->updated_at);
        };
        return $fields;
    }
}