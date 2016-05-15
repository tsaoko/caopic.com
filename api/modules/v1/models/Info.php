<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Info extends \common\models\Info
{
    public function fields(){
        $fields = parent::fields();
        $fields['type_name'] = function($model){
            return isset(self::$typeOptions[$model->type])?self::$typeOptions[$model->type]:'';
        };
        $fields['content'] = function($model){
            return str_replace('&nbsp;','',strip_tags($model->content));
        };
        $fields['start_time'] = function($model){
            return date('Y-m-d',$model->start_time);
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