<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Help extends \common\models\Help
{
    public function fields(){
        $fields = parent::fields();
        $fields['forum'] = function($model){
            return $model->forum->name;
        };
        $fields['content'] = function($model){
            return str_replace('&nbsp;','',strip_tags($model->content));
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