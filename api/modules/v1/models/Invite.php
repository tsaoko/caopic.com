<?php

namespace api\modules\v3\models;

use common\helpers\Util;
use xj\qrcode\QRcode;
use xj\qrcode\widgets\Text;
use xr0m3oz\simplehtml\SimpleHtmlDom;
use Yii;
use common\models\User;
use yii\helpers\Url;

class Invite extends \common\models\Invite
{
    public function fields(){
        $fields['user_name'] = function($model){
            return User::getUsername($model->user_id);
        };
        $fields['created_at'] = function($model){
            return date('Y-m-d H:i:s',$model->created_at);
        };
        return $fields;
    }

    public function index($user, $device='app'){
        switch($device) {
            case 'weixin':
                $data['user_id'] = $user->id;
                $data['invite'] = number_format($user->invite,0);
                $data['percentage'] = number_format($user->percentage,2);
                $data['invite_amount'] = number_format($user->inviteamount,2);
                break;
            case 'pc':
                $data = [];
                break;
            case 'app':
            default:
                $data['qrcode'] = $this->getQrcode($user->id);
                $data['invite_number'] = $user->invite;
                $data['total_percentage'] = $user->percentage;
                $data['available_amount'] = $user->inviteamount;
                break;
        }
        return $data;
    }

    public function share($user){
        $data['dayNumber'] = Util::count_days(date('Y-m-d'),date('Y-m-d',$user->created_at));;
        $invest_amount = Invest::find()->where(['user_id'=>$user->id,'status'=>[Invest::STATUS_YES,Invest::STATUS_FINISHED,Invest::STATUS_REPAYMENT],'is_pay'=>1])->sum('amount');
        $data['invest'] = number_format($invest_amount,2);
        $data['income'] = number_format($user->income,2);

        $data['qrcode'] = $this->getQrcode($user->id);

        return $data;
    }

    private function getQrcode($id){
        $url = Url::to(['/site/register', 'invite_code' => $id], true);
        $qrcode = Text::widget(['outputDir' => '@webroot/uploads/qrcode',
            'outputDirWeb' => Url::toRoute(['/uploads/qrcode'],true),
            'ecLevel' => QRcode::QR_ECLEVEL_L,
            'text' => $url,
            'size' => 7,]);
        $dom = new SimpleHtmlDom();
        $dom->load($qrcode);
        $qrcode = $dom->find('img[src]');
        return empty($qrcode)?'':$qrcode[0]->attr['src'];
    }
}