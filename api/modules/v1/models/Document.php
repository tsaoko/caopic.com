<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Document extends \common\models\Document
{
    public function fields(){
        $fields = parent::fields();
        unset($fields['type']);
        $fields['title_name'] = function($model){
            $arr = self::titleOption($model->type);
            return isset($arr[$model->title])?$arr[$model->title]:'';
        };
        $fields['created_at'] = function($model){
            return date('Y-m-d',$model->created_at);
        };
        $fields['updated_at'] = function($model){
            return date('Y-m-d',$model->updated_at);
        };
        return $fields;
    }
}