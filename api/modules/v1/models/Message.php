<?php

namespace api\modules\v3\models;

use Yii;
use common\models\User;

class Message extends \common\models\Message
{
    public function fields(){
        $fields = parent::fields();
        $fields['created_at'] = function($model){
            return date('Y-m-d H:i:s',$model->created_at);
        };
        $fields['updated_at'] = function($model){
            return date('Y-m-d H:i:s',$model->updated_at);
        };
        return $fields;
    }
}