<?php
/**
 * Created by PhpStorm.
 * User: yangtanfang
 * Date: 16/2/3
 * Time: 17:24
 */

namespace api\modules\v3\action\master;

use Yii;
use yii\rest\Action;


class ViewAction extends Action
{

    public function run($id)
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        return ['status'=>'success','data'=>$model,'msg'=>'操作成功'];
    }

}