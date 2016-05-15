<?php
namespace api\modules\v3\controllers;

use api\modules\v3\models\InfoForm;
use api\modules\v3\models\ModifyInfoForm;
use api\modules\v3\models\ModifyMobileForm;
use api\modules\v3\models\Recharge;
use api\modules\v3\models\RechargeForm;
use api\modules\v3\models\WithdrawLlpayForm;
use common\models\CityCode;
use common\models\Info;
use api\modules\v3\models\InvestWxForm;
use api\modules\v3\models\RechargeLlpayForm;
use api\modules\v3\models\WithdrawWxForm;
use common\models\InvestFriend;
use common\models\InvestInterest;
use common\models\Repayment;
use common\models\RepaymentPlan;
use common\models\UserInfo;
use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\Withdraw;
use common\api\yeepay\YeePayMobile;
use common\helpers\Util;
use yii\helpers\Url;
use common\api\yeepay\YeePayPc;
use common\models\Invest;
use common\models\Project;
use common\models\User;
use frontend\modules\user\models\AvatarForm;
use yii\web\UploadedFile;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $action = $this->action->id;
        if(!in_array($action, ['to-recharge'])){
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ['class' => HttpBearerAuth::className()],
                ['class' => QueryParamAuth::className(), 'tokenParam' => 'access_token'],
            ]
        ];
        }
        return $behaviors;
    }

    //个人中心首页
    public function actionIndex()
    {
        $get = Yii::$app->request->get();
        $device = isset($get['device'])?$get['device']:'app';
        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = \Yii::$app->user->identity;
        $avatar_size = isset($_GET['avatar_size'])?$_GET['avatar_size']:50; //头像大小

        //用户信息
        switch($device){
            case 'weixin':
                $data = $this->indexWeixin($user,$avatar_size);
                break;
            case 'pc':
                $data = $this->indexPC($user,$avatar_size);
                break;
            case 'app':
            default:
                $data = $this->indexApp($user,$avatar_size);
                break;
        }
        return ['status' => 'success', 'data'=>$data, 'msg' => '操作成功'];
    }

    //获取姓名，身份证，银行卡号
    public function actionReal()
    {
        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = \Yii::$app->user->identity;
        $data = [];
        $data['realnameLLpay'] = isset($user->realnameLLpay)?$user->realnameLLpay:'';
        $data['idsnLLpay'] = isset($user->idsnLLpay)?$user->idsnLLpay:'';
        $data['card_no'] = isset($user->bank_card)?$user->bank_card:'';
        $data['lian_balance'] = $user->total_balance-$user->freeze_balance;

        if(in_array($user->bank_code, ['01020000','01030000','03080000','01040000','03030000','03100000'])){
            $data['need_position'] = 0;
        } else {
            $data['need_position'] = 1;
            $user_info = UserInfo::findOne(['user_id'=>$user->id]);
            $data['province'] = isset($user_info->bank_province)?$user_info->bank_province:'';
            $data['city'] =  isset($user_info->bank_city)?$user_info->bank_city:'';
            $data['brabank'] =  isset($user_info->brabank)?$user_info->brabank:'';
        }

        return ['status' => 'success', 'data'=>$data, 'msg' => '操作成功'];
    }

    /**
     * 连连充值
     *
     * device：来源设别(pc,weixin,app)
     */
    public function actionRechargeLian()
    {
        $post = Yii::$app->request->post();
        $device = isset($post['device'])?$post['device']:'app';

        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;

        $model = new RechargeLlpayForm();
        $model->type = isset($post['type'])?$post['type']:Recharge::PAY_TYPE_AUTH;
        $model->amount = isset($post['amount'])?$post['amount']:'';
        $model->realnameLLpay = isset($post['realname'])?$post['realname']:($user->realnameLLpay?:'');
        $model->idsnLLpay = isset($post['id_no'])?$post['id_no']:($user->idsnLLpay?:'');
        $model->bank_code =  isset($post['bank_code'])?$post['bank_code']:($user->bank_code?:'');//选填
        $model->card_no = isset($post['card_no'])?$post['card_no']:($user->bank_card?:'');
        $is_pay  = isset($post['is_pay'])?$post['is_pay']:false;//选填
        $project_id = isset($post['project_id'])?$post['project_id']:'';//选填
        $return_url = isset($post['return_url'])?$post['return_url']:'';

        if($model->validate()) {
            switch($device){
                case 'pc':
                    $data = Recharge::rechargeLlpayApi($model,$is_pay,$project_id);
                    break;
                case 'weixin':
                case 'app':
                default:
                    $data = Recharge::rechargeWxLlpayApi($model,$return_url,$is_pay,$project_id);
                    break;
            }
            return $data;
        }else{
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
    }

    //连连提现
    public function actionWithdrawLian()
    {
        $post = Yii::$app->request->post();

        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = \Yii::$app->user->identity;
        if(!isset($user->bank_card)){
            return  ['status' => 'fail', 'data'=>[], 'msg' =>'请先绑卡后再提现'];
        }
        $model = new WithdrawLlpayForm();
        $model->mobile =$user->mobile;
        $user_info = UserInfo::findOne(['user_id'=>$user->id]);
        if(!$user_info){
            return  ['status' => 'fail', 'data'=>[], 'msg' =>'用户信息异常'];
        }
        $model->province = isset($post['province'])?$post['province']:($user_info->bank_province?:'');
        $model->city =  isset($post['city'])?$post['city']:($user_info->bank_city?:'');
        $model->brabank =  isset($post['brabank'])?$post['brabank']:($user_info->brabank?:'');
        $model->amount =  isset($post['amount'])?$post['amount']:0;

        if($model->validate() )
        {
            $notify_url = Url::toRoute(['notify-llpay/process','act'=>'withdraw||'],true);
            $flag = Withdraw::withdraw($model, $user, $notify_url);
            if($flag){
                return  ['status' => 'success', 'data'=>[], 'msg' =>'提现成功'];
            } else {
                return  ['status' => 'fail', 'data'=>[], 'msg' =>'提现失败'];
            }
        }else{
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
    }

    /**
     * 投定期标
     */
    public function actionInvest()
    {
        $post = Yii::$app->request->post();
        $device = isset($post['device'])?$post['device']:'app';

        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = \Yii::$app->user->identity;

        $id = isset($post['project_id'])?$post['project_id']:0;
        $model = Project::findOne($id);
        if(!$model){
            return  ['status' => 'fail', 'data'=>[], 'msg' =>'项目不存在'];
        }

        $invest = new InvestWxForm();
        $invest->project_id = $id;
        $invest->pay_type = isset($post['pay_type'])?$post['pay_type']:User::PAY_TYPE_LLPAY;
        $invest->coupon_id = isset($post['coupon_id'])?$post['coupon_id']:null;
        $invest->amount = isset($post['amount'])?$post['amount']:0;
        $invest->rememberMe = true;
        if ($invest->validate())
        {
            if($invest->pay_type == User::PAY_TYPE_LLPAY){
                if( $invest->amount > $user->total_balance-$user->freeze_balance )
                {
                    return  ['status' => 'fail', 'data'=>[], 'msg' =>'余额不足'];
                }
            }else{
                if( $invest->amount > $user->balance )
                {
                    return  ['status' => 'fail', 'data'=>[], 'msg' =>'余额不足'];
                }
            }

            $requestNo = Util::rand();
            $toin = new Invest;
            $toin->requestNo = $requestNo;
            $toin->user_id = $user->id;
            $toin->target_user_id = $model->user_id;
            $toin->project_id = $model->id;
            $toin->type_id = $model->type_id;
            $toin->amount = $invest->amount;
            $limit=$model->limit+1;
            $toin->repay_time=strtotime(date('Y-m-d',strtotime("+$limit day")));
            $toin->updated_at = $toin->created_at = time();
            $toin->source = '微信';
            $toin->status = Invest::STATUS_READY;
            $toin->pay_type = $invest->pay_type;
            //使用加息券
            if ($invest->coupon_id) {
                $toin->coupon_id = $invest->coupon_id;
            }
            if ($toin->save(false)) {
                if($toin->pay_type == User::PAY_TYPE_LLPAY){
                    $flag = Invest::buy($requestNo,$model);
                    if(!$flag){
                        return  ['status' => 'fail', 'data'=>[], 'msg' =>'投标失败'];
                    }
                }else{
                    $targetUser = User::findOne($model->user_id);
                    $return_url = isset($post['return_url'])?$post['return_url']:'';
                    $callback_url = $return_url?:Url::toRoute(['yeepay/callback','act'=>'project_invest|'.$model->cate_id], true);
                    $notify_url = Url::toRoute(['yeepay/notify', 'act'=>'project_invest|'], true);
                    switch($device){
                        case 'pc':
                            $pay = new YeePayPc();
                            $pay->toInvest(
                                $user->yeepayUserNo, //
                                $targetUser->yeepayUserNo, // 借款人平台ID
                                $requestNo, $toin->amount, $model->order_no, $model->name, $model->total_amount, $model->name, $notify_url, $callback_url, $model->total_amount
                            );
                            break;
                        case 'weixin':
                        case 'app':
                        default:
                            $pay = new YeePayMobile;
                            $pay->toInvest(
                                $user->yeepayUserNo, //
                                $targetUser->yeepayUserNo, // 借款人平台ID
                                $requestNo, $toin->amount, $model->order_no, $model->name, $model->total_amount, $model->name,$model->total_amount, $notify_url, $callback_url
                            );
                            break;
                    }
                }
                return  ['status' => 'success', 'data'=>[], 'msg' =>'投资成功'];
            }
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($invest->getFirstErrors())[0]];
        }


    }

     /**
     * 易宝充值
     */
    public function actionRechargeYee()
    {
        $post = Yii::$app->request->post();
        $device = isset($post['device'])?$post['device']:'app';

        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;

        if(!$user->is_yee )
        {
            return ['status' => 'fail', 'data'=>[], 'msg' => '未开通易宝支付'];
        }
        $model = new RechargeForm();
        $model->amount = isset($post['amount'])?$post['amount']:0;

        if($model->validate()){
            $return_url = isset($post['return_url'])?$post['return_url']:'';
            $requestNo = 'faxeye-recharge-'.Util::rand();
            $notify_url = Url::toRoute(['yeepay/notify','act'=>'recharge|'],true);
            $callback_url = $return_url?$return_url:Url::toRoute(['yeepay/callback', 'act'=>'recharge|'],true);
            $feeMode = 'PLATFORM';

            $fee = '0';
            $re = new Recharge();
            $re->user_id = $user->id;
            $re->requestNo = $requestNo;
            $re->amount = $model->amount-$fee;
            $re->feeMode = $feeMode;
            $re->fee = $fee; // 用户自己出
            $re->status = Recharge::STATUS_REPLAY;
            $re->create_at = $re->update_at = time();
            $re->remark = '';
            $re->pay_type = Recharge::PAY_TYPE_YEE;

            if( $re->save(false) )
            {
                switch($device){
                    case 'pc':
                        // 跳转至第三方托管
                        $pay = new YeePayPc();
                        $pay->toRecharge(
                            $user->yeepayUserNo,
                            $requestNo,
                            $notify_url,
                            $callback_url,
                            $model->type,
                            $feeMode,
                            $model->amount
                        );
                        break;
                    case 'weixin':
                    case 'app':
                    default:
                        // 跳转至第三方托管
                        $pay = new YeePayMobile;
                        $pay->toRecharge(
                            $user->yeepayUserNo,
                            $requestNo,
                            $model->amount,
                            $notify_url,
                            $callback_url,
                            $feeMode
                        );
                        break;
                }
                return ['status' => 'success', 'data'=>[], 'msg' => '充值成功'];
            } else {
                return ['status' => 'fail', 'data'=>[], 'msg' => '充值失败'];
            }
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
    }

    /**
     * 易宝提现至银行卡
     */
    public function actionWithdrawYee()
    {
        $post = Yii::$app->request->post();
        $device = isset($post['device'])?$post['device']:'app';

        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;
        if( !$user->is_yee )
        {
            return ['status' => 'fail', 'data'=>[], 'msg' => '未开通易宝支付'];
        }
        $data['user'] = $user;
        $model = new WithdrawWxForm();
        $model->amount = isset($post['amount'])?$post['amount']:0;
        if ($model->validate())
        {
            $return_url = isset($post['return_url'])?$post['return_url']:'';
            $notify_url = Url::toRoute(['yeepay/notify', 'act'=>'withdraw|'],true);
            $callback_url = $return_url?$return_url:Url::toRoute(['yeepay/callback', 'act'=>'withdraw|'],true);
            $feeMode = 'USER';

            $requestNo = 'faxeye-withdraw-'.Util::rand();
            // 提现
            $fee = Recharge::getFee($model->amount, 'withdraw');
            $withdraw = new Withdraw;
            $withdraw->user_id = $user->id;
            $withdraw->requestNo = $requestNo;
            $withdraw->amount = $model->amount;
            $withdraw->feeMode = $feeMode;
            $withdraw->fee = $fee; // 用户自己出
            $withdraw->ip = Util::getClientIP();
            $withdraw->status = Withdraw::STATUS_REPLAY;
            $withdraw->create_at = $withdraw->update_at = time();
            $withdraw->remark = '';
            $withdraw->pay_type = Withdraw::PAY_TYPE_YEEPAY;
            if($withdraw->save(false) )
            {
                switch($device){
                    case 'pc':
                        $yeepay = new YeePayPc();
                        $yeepay->toWithdraw(
                            $user->yeepayUserNo,
                            $requestNo,
                            $notify_url,
                            $callback_url,
                            $feeMode,
                            'NORMAL',
                            $model->amount);
                        break;
                    case 'weixin':
                    case 'app':
                    default:
                        $yeepay = new YeePayMobile;
                        $yeepay->toWithdraw(
                            $user->yeepayUserNo,
                            $requestNo,
                            $model->amount,
                            $notify_url,
                            $callback_url,
                            $feeMode
                        );
                        break;
                }
                return ['status' => 'success', 'data'=>[], 'msg' => '提现成功'];
            } else {
                return ['status' => 'fail', 'data'=>[], 'msg' => '提现失败'];
            }
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
    }

    /**
     * 同步易宝
     *
     * @return [type] [description]
     */
    public function actionAccountinfo()
    {
        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;

        // 有开通易宝支付
        if( !$user->is_yee )
        {
            return ['status' => 'fail', 'data'=>[], 'msg' => '未开通易宝支付'];
        }

        $pay = new YeePayPc();

        $data = $pay->accountInfo($user->yeepayUserNo);

        if( $data->code != '1' )
        {
            Util::log('同步易宝信息失败:'.print_r($data, true));
            return ['status' => 'fail', 'data'=>[], 'msg' => '同步易宝信息失败'];
        }

        if( $user->balance != $data->availableAmount )
        {
            User::updateAll(['balance'=>$data->availableAmount],['id'=>$user->id]);
        }

        if( $user->freeze_amount != $data->freezeAmount )
        {
            User::updateAll(['freeze_amount'=>$data->freezeAmount],['id'=>$user->id]);
        }

        return ['status' => 'success', 'data'=>[], 'msg' => '同步易宝信息完成'];
    }

    //设置头像
    public function actionAvatar() {
        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;
        $model = new AvatarForm();
        $image= UploadedFile::getInstanceByName('avatar');
        $model->avatar =$image;
        if(!$model->validate())
        {
             return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
        if ($model->user->avatar) {
            // 删除头像
            $model->deleteImage();
        }

        $model->avatar = \Yii::$app->security->generateRandomString() . ".{$image->extension}";

        if ($model->save()) {
            if ($image !== false) {
                $path = $model->getImageFile();
                $image->saveAs($path);
            }
            return ['status' => 'success', 'data'=>['img'=>$user->getUserAvatar(50)], 'msg' => '操作成功'];
        }
       return ['status' => 'fail', 'data'=>[], 'msg' =>  '操作失败'];
    }

    //用户基本信息
    public function actionInfo(){
        $post = Yii::$app->request->post();
        $device = isset($post['device'])?$post['device']:'app';

        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;
        $info = UserInfo::find()->andWhere(['user_id'=>$user->id])->one();

        switch($device){
            case 'pc':
                $model = new InfoForm();
                if($info){
                    $model->birthday = isset($post['birthday'])?$post['birthday']:$info->birthday;
                    $model->gender = isset($post['gender'])?$post['gender']:$info->gender;

                    if ($model->validate() )
                    {
                        $info->birthday = $model->birthday;
                        $info->gender = $model->gender;
                        if($info->save(false)){
                            break;
                        } else {
                            return ['status' => 'fail', 'data'=>[], 'msg' => '个人信息更新失败'];
                        }
                    } else {
                        return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
                    }
                } else {
                    return ['status' => 'fail', 'data'=>[], 'msg' => '个人信息更新失败'];
                }
                break;
            case 'weixin':
                $model = new ModifyInfoForm();
                $model->gender = isset($post['gender'])?$post['gender']:$info->gender;
                $model->contact = isset($post['contact'])?$post['contact']:$info->contact_mobile;

                if ($model->validate() )
                {
                    $info->gender = $model->gender;
                    $info->contact_mobile = $model->contact;
                    if($info->save(false)){
                        break;
                    } else {
                        return ['status' => 'fail', 'data'=>[], 'msg' => '基本信息保存失败'];
                    }
                } else {
                    return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
                }
            case 'app':
            default:
                break;
        }
        return ['status' => 'success', 'data'=>[], 'msg' => '基本信息保存成功'];
    }

    //修改手机号
    public function actionMobile(){
        if (\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户未登录'];
        }

        $user = Yii::$app->user->identity;
        $post = Yii::$app->request->post();

        $model = new ModifyMobileForm();
        $model->realname = isset($post['realname'])?$post['realname']:'';
        $model->idsn  = isset($post['id_no'])?$post['id_no']:'';
        $model->mobile = isset($post['mobile'])?$post['mobile']:'';
        $model->checkcode = isset($post['checkcode'])?$post['checkcode']:'';

        if($model->validate()){
            if($user->is_yee){
                $yeepay = new YeePayPc();
                $callback_url = Url::toRoute(['yeepay/callback','act'=>'modify_mobile|'.$model->mobile],true);
                $notify_url = Url::toRoute(['yeepay/notify','act'=>'modify_mobile|'.$model->mobile],true);
                $yeepay->toResetMobile($user->yeepayUserNo, 'mobile-'.time(),$callback_url ,$notify_url);
            } else {
                $user->username = $model->mobile;
                $user->mobile = $model->mobile;
                if(!$user->save(FALSE)){
                    return ['status' => 'fail', 'data'=>[], 'msg' => '手机号码修改失败'];
                }
            }
            return ['status' => 'success', 'data'=>[], 'msg' => '手机号码修改成功'];
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }

    }

    //还款日
    public function actionRepayDate(){
        $year = Yii::$app->request->get('year');
        $month = Yii::$app->request->get('month');

        $repay_date=  $this->mFristAndLast($year,$month);
        $max_day=$repay_date['lastday'];
        $min_day=$repay_date['firstday'];
        $user = User::findOne(Yii::$app->user->id);
        $repay_all= Repayment::find()->select('dateline')->where(['to_user_id'=>$user->id])->andWhere(['<=','dateline',$max_day])->andWhere(['>=','dateline',$min_day])->groupBy('dateline')->asArray()->all();
        $repay=[];
        foreach($repay_all as $item)
        {
            $repay[]=date('Y-n-j',$item['dateline']);
        }
        return ['status'=>'success','data'=>$repay,'msg'=>'操作成功'];
    }

    //取得一个月的第一和最后一天
    private function mFristAndLast($y = "", $m = "") {
        if ($y == "")
            $y = date("Y");
        if ($m == "")
            $m = date("m");
        $m = sprintf("%02d", intval($m));
        $y = str_pad(intval($y), 4, "0", STR_PAD_RIGHT);

        $m > 12 || $m < 1 ? $m = 1 : $m = $m;
        $firstday = strtotime($y . $m . "01000000");
        $firstdaystr = date("Y-m-01", $firstday);
        $lastday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
        return array("firstday" => $firstday, "lastday" => $lastday);
    }

    private function indexPC($user,$size){
        //用户信息
        $data['username'] = $user->username;
        $data['type'] = User::$type_list[$user->type];//账户类型
        $data['score'] = $user->score;//积分
        $data['unread'] = $user->messageCount;//未读消息数量
        $data['unused'] = $user->unusedCoupon;//未用加息券数量
        $data['avatar'] = $user->getUserAvatar($size);//头像
        $data['level'] = $user->level;
        //易宝账户
        $data['yee_balance'] = $user->balance;//可用余额
        $data['yee_use'] = $user->getUseAmount();//在投产品
        $data['yee_total'] = $user->getTotalGains();//账户总资产
        //连连账户
        $data['lian_balance'] = $user->total_balance-$user->freeze_balance;//可用余额
        $data['lian_use'] = $user->getUseAmountLlpay();//在投产品
        $data['lian_total'] = $user->getTotalGainsLlpay();//账户总资产
        //我的邀请
        $data['percentage'] = $user->percentage;
        $data['invite'] = $user->invite;
        $data['invite_amount'] = $user->inviteamount;
        //待收明细
        $data['return_amount_total_1']= Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_1])->sum('repayment.amount');
        $data['return_amount_profit_1']= Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_1])->sum('repayment.interest');
        $data['return_count_1']=Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->groupBy('invest_id')->andWhere(['project.type_id'=>Project::TYPE_FIXED_1])->count();

        $data['return_amount_total_2']= Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_2])->sum('repayment.amount');
        $data['return_amount_profit_2']= Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_2])->sum('repayment.interest');
        $data['return_count_2']=Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->groupBy('invest_id')->andWhere(['project.type_id'=>Project::TYPE_FIXED_2])->count();

        $data['return_amount_total_3']= Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_3])->sum('repayment.amount');
        $data['return_amount_profit_3']= Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_3])->sum('repayment.interest');
        $data['return_count_3']=Repayment::find()->joinWith('project')->where(['repayment.to_user_id'=>$user->id,'repayment.status'=>RepaymentPlan::STATUS_NO])->andWhere(['project.type_id'=>Project::TYPE_FIXED_3])->groupBy('invest_id')->count();

        //城市列表
        $data['city_list'] = [];
        if(isset($user->userInfo->bank_province) && $user->userInfo->bank_province){
            $city = CityCode::find()->andWhere(['<>', 'city', ''])->andWhere(['province'=>CityCode::getName($user->userInfo->bank_province)])->all();
            $city_list = [];
            foreach($city as $item){
                $city_list[$item['id']] = $item['city'];
            }
            $data['city_list'] = $city_list;
        }

        //开户行省份列表
        $province = CityCode::find()->andWhere(['city'=> ''])->all();
        $province_list = [];
        foreach($province as $item){
            $province_list[$item['id']] = $item['province'];
        }
        $data['province_list'] = $province_list;

        return $data;
    }

    private function indexWeixin($user,$size){
        //用户信息
        $data['username'] = $user->username;
        $data['type'] = User::$type_list[$user->type];//账户类型
        $data['score'] = $user->score;//积分
        $data['unread'] = $user->messageCount;//未读消息数量
        $data['unused'] = $user->unusedCoupon;//未用加息券数量
        $data['avatar'] = $user->getUserAvatar($size);//头像
        $data['level'] = $user->level;

        //公告（显示本星期最近三天的公告，如没有则显示最近的一篇公告）
        $list = [];
        $items = Info::find()->andWhere(['type'=>Info::ABOUT_UPDATE, 'YEARWEEK(FROM_UNIXTIME(created_at, \'%Y-%m-%d\' ))'=>date('Y').date('W')])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        if(!count($list)){
            $items = Info::find()->andWhere(['type'=>Info::ABOUT_UPDATE])->orderBy(['created_at'=>SORT_DESC])->limit(1)->all();
        }
        foreach($items as $key=>$item){
            $list[$key]['id'] = $item->id;
            $list[$key]['title'] = $item->title;
        }
        $data['list'] = $list;

        //待收金额
        $data['collect'] = number_format($user->collect+$user->collectLian,2);
        //累计投资收益
        $friend_interest = InvestInterest::find()->andWhere(['user_id'=>$user->id])->sum('amount');
        $data['income'] = number_format($user->getRegularIncomeLlpay()+$user->getRegularIncome()+$friend_interest,2);

        //易宝账户
        $data['yee_balance'] = $user->balance;//可用余额
        $data['yee_use'] = $user->getUseAmount();//在投产品
        $data['yee_total'] = $user->getTotalGains();//账户总资产
        //连连账户
        $data['lian_balance'] = $user->total_balance-$user->freeze_balance;//可用余额
        $data['lian_use'] = $user->getUseAmountLlpay();//在投产品
        $data['lian_total'] = $user->getTotalGainsLlpay();//账户总资产

        //可提现提成
        $data['invite_amount'] = $user->inviteamount;

        $data['registerNumber'] = User::registerNumber();
        $data['totalInvestAmount'] = Invest::totalInvestAmount();

        return $data;
    }

    private function indexApp($user,$size){
        //用户信息
        $data['username'] = $user->username;
        $data['type'] = User::$type_list[$user->type];//账户类型
        $data['score'] = $user->score;//积分
        $data['unread'] = $user->messageCount;//未读消息数量
        $data['unused'] = $user->unusedCoupon;//未用加息券数量
        $data['avatar'] = $user->getUserAvatar($size);//头像
        $data['level'] = $user->level;
        //我的余额
        $balance = $user->balance + $user->total_balance-$user->freeze_balance;
        $data['balance'] = number_format($balance,2);
        //待收本息
        $unrepay = Repayment::find()->where(['to_user_id'=>$user->id,'status'=>Repayment::STATUS_NO])->sum('amount');
        $data['unrepay'] = number_format($unrepay,2);
        //总资产
        $data['total'] = number_format($balance + $unrepay,2);

        return $data;
    }

    public function actionToRecharge(){
        $html= <<<HTML
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>连连支付wap交易接口</title>
            </head>
            <body onload="document.getElementById('llpaysubmit').submit();">
            <form id='llpaysubmit' name='llpaysubmit' action='https://yintong.com.cn/llpayh5/authpay.htm' method='post'>
                <input type='hidden' name='req_data' value='{"acct_name":"王亚彬","app_request":"3","bank_code":"03080000","busi_partner":"101001","card_no":"9555500270917767","dt_order":"20160413100719","id_no":"420107198409021515","info_order":"9555500270917767|王亚彬|420107198409021515","money_order":"1","name_goods":"充值","no_order":"faxeye-recharge-146051323949939","notify_url":"http://localhost/v3/notify-llpay/process?act=recharge%7C%7C","oid_partner":"201509151000500512","risk_item":"{\"frms_ware_category\":\"2009\",\"user_info_mercht_userno\":\"19\",\"user_info_dt_register\":\"20150723122626\",\"user_info_full_name\":\"王亚彬\",\"user_info_id_no\":\"420107198409021515\",\"user_info_identify_state\":\"0\"}","sign_type":"RSA","url_return":"http://localhost/v3/callback/process?act=recharge%7C%7C","user_id":"19","valid_order":"10080","sign":"BJUZKOwjIT5dwS9peO6CrLGfuO53msbcU+h+Y7d7VOkM4AL//clncPf4Wwc9KIYwkjIx6qzry3VFnca7Cf5yxHtE6I8qdBwicS21CsNOG1vNYUI1zCJsy0zkgwbsAc4j+ybia0LTCrLh2mznNtrhoINZNIGngN/Hi1qIbVugmwM="}'/>
            </form>
            </body>
            </html>
HTML;
        print $html;
    }
}
