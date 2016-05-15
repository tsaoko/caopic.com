<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Score extends \common\models\Score
{
    public function fields(){
        $fields = parent::fields();
        $fields['type_name'] = function($model){
            return isset(self::$typeOptions[$model->type])?self::$typeOptions[$model->type]:$model->type;
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
        return $fields;
    }
}