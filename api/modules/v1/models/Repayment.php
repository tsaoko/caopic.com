<?php

namespace api\modules\v3\models;

use common\models\User;
use Yii;

class Repayment extends \common\models\Repayment
{
    public function fields(){
        $fields = parent::fields();
        unset($fields['check_status'],$fields['back_trade_no'],$fields['back_time'],$fields['back_status'],$fields['from_side'],$fields['type']);
        $fields['dateline'] = function($model){
            return date('Y-m-d',$model->dateline);
        };
        $fields['project_name'] = function($model){
            return $model->project->name;
        };
        $fields['status_name'] = function($model){
            return isset(self::$status_list[$model->status])?self::$status_list[$model->status]:'';
        };
        $fields['pay_type_name'] = function($model){
            return isset(User::$pay_type_list[$model->pay_type])?User::$pay_type_list[$model->pay_type]:'';
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