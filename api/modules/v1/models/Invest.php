<?php

namespace api\modules\v3\models;

use common\models\Project;
use common\models\Repayment;
use Yii;
use common\models\User;

class Invest extends \common\models\Invest
{
    public function fields(){
        $fields = parent::fields();
        unset($fields['repay_time'],$fields['refund_time'],$fields['refund_status'],$fields['returned_amount'],$fields['unreturn_amount'],$fields['freeze_time'],$fields['yeepayUserNo'],$fields['targetYeepayUserNo'],$fields['ver']);
        $fields['project_name'] = function($model){
            return $model->project->name;
        };
        $fields['pay_time'] = function($model){
            return date('Y-m-d H:i:s',$model->pay_time);
        };
        $fields['limit'] = function($model){
            return $model->project->limit;
        };
        $fields['amount'] = function($model){
            return number_format($model->amount,2);
        };
        $fields['interest'] = function($model){
            return $model->project->interest*100;
        };
        $fields['repay_type'] = function($model){
            return isset(Repayment::$repay_type_list[$model->project->repay_type])?Repayment::$repay_type_list[$model->project->repay_type]:'';
        };
        $fields['pay_type_name'] = function($model){
            return isset(User::$pay_type_list[$model->pay_type])?User::$pay_type_list[$model->pay_type]:'';
        };
        $fields['type_name'] = function($model){
            return isset(Project::$type_list[$model->type_id])?Project::$type_list[$model->type_id]:'';
        };
        $fields['status_name'] = function($model){
            return isset(self::$status_list[$model->status])?self::$status_list[$model->status]:'';
        };
        $fields['created_at'] = function($model){
            return date('Y-m-d H:i:s',$model->created_at);
        };
        $fields['updated_at'] = function($model){
            return date('Y-m-d H:i:s',$model->updated_at);
        };
        return $fields;
    }

    public function view($invest,$device='app'){
        $data = [];
        switch($device){
            case 'pc':
                break;
            case 'weixin':
                break;
            case 'app':
            default:
                $data['name'] = $invest->project->name;
                $data['amount'] = $invest->amount;
                $data['interest'] = $invest->project->interest*100;
                //已收本息
                $payed = Repayment::find()->andWhere(['invest_id'=>$invest->id,'to_user_id'=>$invest->user_id])->andWhere(['status'=>Repayment::STATUS_YES])->sum('amount');
                $data['payed'] = number_format($payed,2);
                //待收本息
                $unpay = Repayment::find()->andWhere(['invest_id'=>$invest->id,'to_user_id'=>$invest->user_id])->andWhere(['status'=>Repayment::STATUS_NO])->sum('amount');
                $data['unpay'] = number_format($unpay,2);
                //还款明细
                $data['status'] = Invest::$status_list[$invest->status];
                $list = Repayment::find()->select('dateline,amount,interest,status')->andWhere(['invest_id'=>$invest->id,'to_user_id'=>$invest->user_id])->orderBy(['dateline'=>SORT_ASC])->all();
                $data['list'] = [];
                foreach($list as $key=>$item){
                    $data['list'][$key]['dateline'] = date('Y-m-d',$item->dateline);
                    $data['list'][$key]['principal'] = number_format($item->amount-$item->interest,2);
                    $data['list'][$key]['interest'] = number_format($item->interest,2);
                    $data['list'][$key]['status'] = Repayment::$status_list[$item->status];
                }
                break;
        }
        return $data;
    }

}