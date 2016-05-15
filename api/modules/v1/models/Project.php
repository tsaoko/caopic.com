<?php

namespace api\modules\v3\models;

use common\helpers\Util;
use common\models\Help;
use common\models\Invest;
use common\models\Repayment;
use common\models\RepaymentPlan;
use common\models\User;
use common\models\Zhaiquan;
use common\models\ZhaiquanImage;
use Yii;
use common\models\Slide;
use yii\helpers\ArrayHelper;

class Project extends \common\models\Project
{
    public function fields(){
        $fields = parent::fields();
        unset($fields['img'],$fields['is_beginner'],$fields['ver']);
        $fields['total_amount'] = function($model){
            return number_format($model->total_amount,2);
        };
        $fields['cate_name'] = function($model){
            return isset(self::$cate_list[$model->cate_id])?self::$cate_list[$model->cate_id]:'';
        };
        $fields['repay_name'] = function($model){
            return isset(Repayment::$repay_type_list[$model->repay_type])?Repayment::$repay_type_list[$model->repay_type]:'';
        };
        $fields['status_name'] = function($model){
            return isset(self::$status_list[$model->status])?self::$status_list[$model->status]:'';
        };
        $fields['interest'] = function($model){
            return $model->interest * 100;
        };
        $fields['total_amount'] = function($model){
            return $model->total_amount / 10000;
        };
        $fields['ketou'] = function($model){
            return number_format($model->ketou / 10000,2);
        };
        // 万元收益
        $fields['wan'] = function($model){
            $amount = 10000 * $model->interest / 365 * $model->limit;
            return number_format($amount,2);
        };
        // 万元单月收益
        $fields['danyue'] = function($model){
            $amount = 10000 * $model->interest / 365 * 30;
            return number_format($amount,2);
        };

        $fields['type_name'] = function($model){
            return isset(self::$type_list[$model->type_id])?self::$type_list[$model->type_id]:'';
        };

        $fields['repayment_time'] = function($model){
            return date('Y-m-d',$model->repayment_time);
        };
        $fields['bid_start_time'] = function($model){
            return date('Y-m-d H:i:s',$model->bid_start_time);
        };
        $fields['bid_end_time'] = function($model){
            return date('Y-m-d',$model->bid_end_time);
        };
        $fields['loan_time'] = function($model){
            return date('Y-m-d',$model->loan_time);
        };
        $fields['created_at'] = function($model){
            return date('Y-m-d H:i:s',$model->created_at);
        };
        $fields['updated_at'] = function($model){
            return date('Y-m-d H:i:s',$model->updated_at);
        };
        $fields['icon'] = function($model){
            return isset(Zhaiquan::$type_icon[$model->zhaiquan->type])?Zhaiquan::$type_icon[$model->zhaiquan->type]:'';
        };
        $fields['icon_name'] = function($model){
            $arr = Zhaiquan::typeOptions();
            return isset(Zhaiquan::$type_icon[$model->zhaiquan->type])?$arr[$model->zhaiquan->type]:'';
        };
        return $fields;
    }

    public function mainIndex()
    {
        $data = [];
        $slide = Slide::find()->where(['type'=>Slide::TYPE_APP,'status'=>Slide::STATUS_YES])->all();
        $data['slide'] = $slide;
         
        $novices =$this->findBySql('select * from project where is_enabled=1 and cate_id=:cate_id order by id desc limit 1',[':cate_id'=>Project::CATE_FIXED])->one();
        if($novices) {
            $data = $this->view($novices->id);
        }

        return $data;
    }

    /**
     * 项目详情
     *
     * 返回：project：项目信息；invest：前十投标信息；company_images：项目详情图片；
     *      voucher_images：合同协议图片；zhaiquan：债权信息；plan：还款计划；help：理财分享
     */
    public function view($id,$device='app')
    {
        $model = $this->findOne($id);
        if(!$model){
            return false;
        }

        switch($device){
            case 'pc':
                $data = $this->viewPC($model);
                break;
            case 'weixin':
               $data = $this->viewWeixin($model);
                break;
            case 'app':
            default:
               $data = $this->viewApp($model);
               break;

        }
        return $data;
    }

    //项目债权详情
    public function viewZhaiquan($id){
        $zhaiquan = Zhaiquan::findOne($id);
        if($zhaiquan){
            $data = $zhaiquan->attributes;
            $data['order_no'] = date('Ymd',$zhaiquan->created_at).sprintf("%03d", $zhaiquan->id);
            $data['zhaiquan'] = number_format($zhaiquan->zhaiquan/10000,2);
        }

        $zhaiquanImages = ZhaiquanImage::find()->andWhere(['zhaiquan_id'=>$id])->all();
        $images = [];
        foreach($zhaiquanImages as $image){
            $images = ArrayHelper::merge($images,Util::getImgCotent($image));
        }
        $data['images'] = $images;
        return $data;
    }

    //项目投标列表
    public function viewInvest($id){
        $query = Invest::find()->where('pay_time>0')->andWhere(['project_id'=>$id,'status'=>[Invest::STATUS_YES,Invest::STATUS_REPAYMENT,Invest::STATUS_FINISHED]])->orderBy(['pay_time'=>SORT_DESC]);
        if($id==156)
        {
            $query=$query->andWhere(['>','amount',99]);
        }

        $list = $query->limit(10)->all();
        foreach($list as $key=>$item){
            $data[$key]['name'] = Util::formatMaskUserName($item->user->username);
            $data[$key]['amount'] = number_format($item->amount,2);
            $data[$key]['pay_time'] = date('Y-m-d H:i:s',$item->pay_time);
        }
        return $data;
    }

    private function viewPC($model){
        //项目
        $project = $model->attributes;
        unset($project['repayment_time'],$project['bid_start_time'],$project['bid_end_time'],$project['loan_time'],$project['is_enabled'],$project['img'],$project['current_amount'],$project['created_at'],$project['updated_at'],$project['is_beginner'],$project['ver']);
        $project['interest'] = $model->interest*100;
        $project['plus_interest'] = $model->plus_interest*100;
        $project['total_amount'] = number_format($model->total_amount,2);
        $project['status_name'] = self::$status_list[$model->status];
        $project['ketou'] = number_format($model->ketou,2);
        $project['icon'] = isset(Zhaiquan::$type_icon[$model->zhaiquan->type])?Zhaiquan::$type_icon[$model->zhaiquan->type]:'';
        $arr = Zhaiquan::typeOptions();
        $project['icon_name'] = isset(Zhaiquan::$type_icon[$model->zhaiquan->type])?$arr[$model->zhaiquan->type]:'';
        $project['project_id'] = date('Ymd',$model->zhaiquan->created_at).sprintf("%03d", $model->zhaiquan->id);
        $project['repay_type'] = Repayment::$repay_type_list[$model->repay_type];
        $project['progress'] = $model->Progress;
        $project['investCount'] = $model->investCount;
        $user = Yii::$app->user->identity;
        if($user){
            $project['yee_balance']=$user?$user->balance:'0';
            $project['lian_balance']=$user?($user->total_balance-$user->freeze_balance):'0';
        }
        $data['project'] = $project;

        // 调出投标数据
        $temp = Invest::find()->andWhere(['project_id'=>$model->id,'is_pay'=>1])->orderBy('pay_time desc,id asc')->limit(10);
        if($model->id==156)
        {
            $temp=$temp->andWhere(['>','amount',99]);
        }
        $temp = $temp->all();
        $invest = [];
        foreach($temp as $key=>$item){
            $invest[$key]['username'] = Util::formatMaskUserName(User::getUsername($item->user_id));
            $invest[$key]['amount'] = $item->amount;
            $invest[$key]['pay_time'] = date('Y-m-d H:i:s',$item->pay_time);
        }
        $data['invest'] = $invest;

        //图片
        $companyImages = ZhaiquanImage::find()->andWhere(['zhaiquan_id'=>$model->zhaiquan_id,'type'=>ZhaiquanImage::TYPE_COMPANY])->all();
        $company_images = [];
        foreach($companyImages as $image){
            $company_images = ArrayHelper::merge($company_images,Util::getImgCotent($image));
        }
        $voucherImages = ZhaiquanImage::find()->andWhere(['zhaiquan_id'=>$model->zhaiquan_id,'type'=>ZhaiquanImage::TYPE_GUARANTOR])->all();
        $voucher_images = [];
        foreach($voucherImages as $image){
            $voucher_images = ArrayHelper::merge($voucher_images,Util::getImgCotent($image));
        }
        $data['company_images'] = $company_images;
        $data['voucher_images'] = $voucher_images;

        //债权信息
        $zhaiquan = Zhaiquan::find()->select(['operation_condition','vouchers','content','fund_use','repay_source','pledge','url','guarantor_opinion'])->andWhere(['id'=>$model->zhaiquan_id])->one();
        $data['zhaiquan'] = $zhaiquan;

        //还款计划
        $temp = RepaymentPlan::find()->andWhere(['project_id'=>$model->id])->orderBy(['dateline'=>SORT_ASC])->all();
        $plan = [];
        foreach($temp as $key=>$item){
            $plan[$key]['project_name'] = $item->project->name;
            $plan[$key]['amount'] = $item->amount;
            $plan[$key]['dateline'] = date('Y-m-d',$item->dateline);
            $plan[$key]['status'] = RepaymentPlan::$status_list[$item->status];
        }
        $data['plan'] = $plan;

        //理财分享
        $help = Help::find()->select(['title'])->andWhere(['forum_id'=>52714])->limit(3)->all();
        $data['help'] = $help;

        return $data;
    }

    private function viewWeixin($model){
        $project = $model->attributes;
        unset($project['repayment_time'],$project['bid_start_time'],$project['bid_end_time'],$project['loan_time'],$project['is_enabled'],$project['img'],$project['current_amount'],$project['created_at'],$project['updated_at'],$project['is_beginner'],$project['ver']);
        $project['interest'] = $model->interest*100;
        $project['plus_interest'] = $model->plus_interest*100;
        $project['total_amount'] = number_format($model->total_amount,2);
        $project['status_name'] = self::$status_list[$model->status];
        $project['ketou'] = number_format($model->ketou,2);
        $project['one_interest'] = Util::roundValue(10000*$model->limit*$model->interest/365,2);//万元收益
        $project['investCount'] = $model->investCount;
        $project['progress'] = $model->Progress;
        $data['project'] = $project;

        $data['registerNumber'] = number_format(User::registerNumber(),0);
        $data['totalInvestAmount'] = number_format(Invest::totalInvestAmount(),2);

        return $data;
    }

    private function viewApp($model){
        $project = $model->attributes;
        unset($project['repayment_time'],$project['content'],$project['zhaiquan_id'],$project['loan_time'],$project['is_enabled'],$project['img'],$project['current_amount'],$project['created_at'],$project['updated_at'],$project['is_beginner'],$project['ver']);
        $project['interest'] = $model->interest*100;
        $project['plus_interest'] = $model->plus_interest*100;
        $project['total_amount'] = number_format($model->total_amount,2);
        $project['status_name'] = self::$status_list[$model->status];
        $project['one_interest'] = Util::roundValue(10000*$model->limit*$model->interest/365,2);//万元收益
        $project['bid_start_time'] = date('Y-m-d',$model->bid_start_time);
        $project['bid_end_time'] = date('Y-m-d',$model->bid_end_time);
        $project['repay_type'] = Repayment::$repay_type_list[$model->repay_type];
        $project['ketou'] = number_format($model->ketou,2);
        $project['bank'] = number_format($model->interest/0.35 * 100,2);

        $data['project'] = $project;
        return $data;
    }

}