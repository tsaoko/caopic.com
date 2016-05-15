<?php
/**
 * Created by PhpStorm.
 * User: yangtanfang
 * Date: 16/4/8
 * Time: 11:10
 */
namespace api\modules\v3\models;

use Yii;



class Percentage extends \common\models\Percentage
{
    public function fields(){
        $fields = parent::fields();
        $fields['total_amount'] = function($model){
            return number_format((int)$model->total_amount,2);
        };
        $fields['total_percent'] = function($model){
            return number_format((int)$model->total_percent,2);
        };
        return $fields;
    }
}