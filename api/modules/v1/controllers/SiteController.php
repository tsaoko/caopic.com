<?php
namespace api\modules\v1\controllers;

use api\components\Oauth;
use api\modules\v1\models\RegisterForm;
use common\models\Help;
use common\models\Info;
use common\models\Invest;
use common\models\Invite;
use common\models\Message;
use common\models\Project;
use common\models\Repayment;
use common\models\RepaymentPlan;
use common\models\Slide;
use common\models\Zhaiquan;
use Yii;
use api\modules\v1\component\ActiveController;
use api\modules\v1\models\LoginForm;
use common\models\User;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use common\models\SmsList;
use common\api\sms\sms;
use common\models\Mail;
use common\helpers\Util;
use common\models\Score;
use api\modules\v1\models\ForgetPasswordForm;

class SiteController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $action = $this->action->id;
        if (!in_array($action, ['index','isexit','login', 'register', 'sms','forget-password','province-list','city-list','version','refresh'])) {
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

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    //首页
    public function actionIndex(){
        $get = Yii::$app->request->get();
        $device = isset($get['device'])?$get['device']:'app';
        switch($device){
            case 'weixin':
                $data = $this->indexWeixin();
                break;
            case 'pc':
                $data = $this->indexPC();
                break;
            case 'app':
            default:
                $data = $this->indexApp();
                break;
        }
        return ['status' => 'success', 'data' => $data, 'msg' => '操作成功'];
    }


    /**
     * 判断手机号是注册
     *
     * @return array
     */
    public function actionIsexit()
    {
        $post = \Yii::$app->request->post();
        if( isset($post['phone']) )
        {
            $model = User::find()->where(['username'=>$post['phone']])->one();
            $data = [];
            if( $model )
            {
                $data['isexit'] = true;
            }else{
                $data['isexit'] = false;
            }
            return ['status'=>'success','msg'=>'查询成功','data'=>$data];
        }else{
            return ['status'=>'tail','msg'=>'传值出错','data'=>$post];
        }
    }



    //登录
    public function actionLogin()
    {
        $post = \Yii::$app->request->post();
        $username = isset($post['phone']) ? $post['phone'] : '';
        $password = isset($post['password']) ? $post['password'] : '';

        $model = new LoginForm();
        $arr['LoginForm'] = array(
            'username' => $username,
            'password' => $password,
        );

        if ($model->load($arr) && $model->login()) {
            $user = \Yii::$app->user->identity;
            $userModel = User::findOne($user->id);

            $oauth = new Oauth();
            $result = $oauth->getAccessToken($username,$password);
            if(!isset($result['access_token'])) {
                return ['status' => 'fail', 'data' => $result, 'msg' => '登录失败'];
            }

            $userModel->access_token = $result['access_token'];
            $userModel->refresh_token = isset($result['refresh_token'])?$result['refresh_token']:'';
            if (!$userModel->update(true, ['access_token','refresh_token'])) {
                return ['status' => 'fail', 'data' => [], 'msg' => '登录失败'];
            }

            return ['status' => 'success', 'data' => $result, 'msg' => '登录成功'];
        } else {
            return ['status' => 'fail', 'data' => [], 'msg' => '帐号或密码不正确'];
        }
    }

    //重新获取access_token
    public function actionRefresh(){
        $post = \Yii::$app->request->post();
        $refresh_token = isset($post['refresh_token'])?$post['refresh_token']:'';
        $user  = User::find()->andWhere(['refresh_token'=>$refresh_token])->one();
        if(!$user){
            return ['status' => 'fail', 'data' => [], 'msg' => 'refresh_token不存在'];
        }

        $oauth = new Oauth();
        $result = $oauth->refreshAccessToken($refresh_token);
        if(!isset($result['access_token'])) {
            return ['status' => 'fail', 'data' => [], 'msg' => '操作失败'];
        }

        $user->access_token = $result['access_token'];
        $user->refresh_token = isset($result['refresh_token'])?$result['refresh_token']:'';
        if ($user->update(true, ['access_token','refresh_token'])) {
            return ['status' => 'success', 'data' => $result, 'msg' => '操作成功'];
        } else {
            return ['status' => 'fail', 'data' => [], 'msg' => '操作失败'];
        }
    }

    //注册
    public function actionRegister()
    {
        $post = \Yii::$app->request->post();
        // 已登录情况
        if (!\Yii::$app->user->isGuest)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '用户已登录'];
        }
        $model = new RegisterForm();
        $model->mobile = isset($post['phone']) ? $post['phone'] : '';
        $model->password = isset($post['password']) ? $post['password'] : '';
        $model->chkpassword = isset($post['chkpassword'])?$post['chkpassword']:'';
        $model->email = isset($post['email']) ? $post['email'] : '';
        $model->invitecode = isset($post['invite_phone']) ? $post['invite_phone'] : '';
        $model->checkcode = isset($post['checknum']) ? $post['checknum'] : '';
        $model->rememberMe = isset($post['rememberMe'])?$post['rememberMe']:0;

        if ($model->validate() ) {
            $yeeId = 'faxeye-user-' . Util::rand();

            $user = new User();
            $user->username = $model->mobile;
            $user->setPassword($model->password);
            $user->generateAuthKey();
            $user->yeepayUserNo = $yeeId;
            $user->mobile = $model->mobile;
            $user->invite_code = Util::getInviteCode();
            $user->email = $model->email;
            $user->source = isset($post['source'])?$post['source']:'';

            if ($user->save()) {
                User::ChangeScore($user->id,500,[
                    'type'=>Score::TYPE_REGISTER,
                    'remark'=>'',
                ]);

                if($model->invitecode){
                    $user_id = User::getIdByName($model->invitecode);
                    $invite_from = Invite::find()->andWhere(['user_id'=>$user_id])->one();
                    $seed_user_id=$invite_from?$invite_from->from_user_id:0;
                    $invite = new Invite();
                    $invite->from_user_id = $user_id;
                    $invite->seed_user_id = $seed_user_id;
                    $invite->user_id = $user->id;
                    $invite->created_at = time();

                    $invite->save(false);
                }

                // 有邮箱的才发送
                if ($model->email) {
                    Mail::createMail($user->id, Mail::TYPE_REGISTER, 0);
                }
                Message::addMessage($user->id,Message::TYPE_REGISTER);

                return ['status' => 'success', 'data' => [], 'msg' => '注册成功'];
            } else {
                return  ['status' => 'fail', 'data' => [], 'msg' => '注册失败'];
            }
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
    }

    /**
     * 找回密码
     */
    public function actionForgetPassword()
    {
        $post = Yii::$app->request->post();

        $mobile = isset($post['mobile'])?$post['mobile']:'';
        $password = isset($post['password'])?$post['password']:'';
        $chkpassword = isset($post['chkpassword'])?$post['chkpassword']:'';
        $checkcode = isset($post['checkcode'])?$post['checkcode']:'';

        $user = User::find()->where(['mobile'=>$mobile])->one();
        if(!$user)
        {
            return ['status' => 'fail', 'data' => [], 'msg' => '手机号未注册'];
        }

        $forgetPassword = new ForgetPasswordForm();
        $forgetPassword->mobile = $mobile;
        $forgetPassword->password = $password;
        $forgetPassword->chkpassword = $chkpassword;
        $forgetPassword->checkcode = $checkcode;

        if($forgetPassword->validate())
        {
            $user->setPassword($password);
            if($user->save(false, ['password_hash']))
            {
                return ['status' => 'success', 'data' => [], 'msg' => '找回密码成功'];
            } else {
                return ['status' => 'fail', 'data' => [], 'msg' => '找回密码失败'];
            }
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($forgetPassword->getFirstErrors())[0]];
        }

    }

    //短信
    public function actionSms()
    {
        $post = Yii::$app->request->post();
        $mobile = isset($post['mobile']) ? $post['mobile'] : '';
        $cate = isset($post['cate']) ? $post['cate'] : '';

        if (!$mobile) {
            return ['status' => 'fail', 'data' => [], 'msg' => '手机号不能为空'];
        }

        // 判断该手机号是否注册
        $isMobile = User::findOne(['mobile' => $mobile]);
        if ($isMobile && $cate == 'mobile') {
            return ['status' => 'fail', 'data' => [], 'msg' => '该手机号已被注册'];
        }

        $sms_id = '28001';

        $verifycode = rand(1000, 9999);

        $ss = SmsList::find()->where(['mobile' => $mobile, 'type' => '1'])->one();
        if (!$ss) {
            $ss = new SmsList;
            $ss->mobile = $mobile;
            $ss->type = '1';
            $ss->count = 1;
        } else {
            if ($ss->created_at + 60 > time()) {
                return ['status' => 'fail', 'data' => [], 'msg' => '60秒内不能重发，请勿频繁点击！'];
            }
            if ($ss->created_at + 60 * 60 * 12 > time() && $ss->count >= 20) {
                return ['status' => 'fail', 'data' => [], 'msg' => '操作次数过于频繁，请稍后再试！'];
            }

            $ss->count += 1;
            if ($ss->created_at + 60 * 60 * 12 <= time()) {
                $ss->count = 1;
            }
            $ss->save(false);
        }
        $ss->code = $verifycode;
        $ss->created_at = time();
        $ss->ip = Util::getClientIP();

        if ($ss->save(false)) {
            $sms = new sms();

            $result = $sms->sendTemplateSMS($mobile, [$verifycode, '5'], $sms_id);

            if ($result['status'] == 1) {
                return ['status' => 'success', 'data' => [], 'msg' => '发送成功'];
            }

        }

        return ['status' => 'fail', 'data' => [], 'msg' => '发送失败'];


    }

    //省份列表
    public function actionProvinceList()
    {
        $list=  \common\models\CityCode::find()->select('id,province')->groupBy('province')->asArray()->all();

        foreach($list as $key=>$item)
        {
            $list[$key]=['id'=>$item['id'],'name'=>$item['province']];
        }

        return ['status' => 'success', 'data' => $list, 'msg' => '操作成功'];
    }

    //城市列表
    public function actionCityList()
    {
        $province = Yii::$app->request->get('province');

        $list =  \common\models\CityCode::find()->select('city,id')->where(['province'=>$province])->asArray()->all();
        foreach($list as $key=>$item)
        {
            $list[$key]=['id'=>$item['id'],'name'=>$item['city']];
        }
        return ['status' => 'success', 'data' => $list, 'msg' => '操作成功'];
    }

    private function indexPC(){
        // 统计注册人数
        $data['registerNumber'] = User::find()->count();
        $data['total_invest_amount'] = Invest::find()->andWhere(['is_pay'=>1])->sum('amount');
        $data['total_interest'] =  Repayment::find()->sum('interest');

        //定期
        $fixed_list = [];
        $items =Project::findBySql('select * from (select * from project where is_enabled=1 and cate_id=:cate_id order by status asc ,id desc ) as p limit 5 ',[':cate_id'=>Project::CATE_FIXED])->all();
        foreach($items as $key=>$item){
            $fixed_list[$key]['id'] = $item->id;
            $fixed_list[$key]['name'] = $item->name;
            $fixed_list[$key]['limit'] = $item->limit;
            $fixed_list[$key]['interest'] = $item->interest;
            $fixed_list[$key]['total_amount'] = $item->total_amount;
            $fixed_list[$key]['bid_start_time'] = $item->bid_start_time;
            $fixed_list[$key]['loan_time'] = $item->loan_time;
            $fixed_list[$key]['status'] = $item->status;
            $fixed_list[$key]['ketou'] = $item->ketou;
            $fixed_list[$key]['progress'] = $item->Progress;
            $arr = Zhaiquan::typeOptions();
            $fixed_list[$key]['icon_name'] = (isset($item->zhaiquan)&&isset($arr[$item->zhaiquan->type]))?$arr[$item->zhaiquan->type]:'';
            $fixed_list[$key]['icon'] = (isset($item->zhaiquan)&&isset(Zhaiquan::$type_icon[$item->zhaiquan->type]))?Zhaiquan::$type_icon[$item->zhaiquan->type]:'';
            $fixed_list[$key]['zhaiquan_type'] = isset($item->zhaiquan)?$item->zhaiquan->type:'';
            $fixed_list[$key]['evaluation'] = isset($item->zhaiquan)?$item->zhaiquan->evaluation:0;
            $fixed_list[$key]['lend_amount'] = isset($item->zhaiquan)?$item->zhaiquan->lend_amount:$item->total_amount;
        }
        $data['fixed_list'] = $fixed_list ;

        //行业公告
        $type = Info::ABOUT_UPDATE;
        $ann = [];
        $items = Info::find()->andWhere(['type'=>$type])->andWhere("start_time<:start_time",[':start_time'=>time()])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $ann[$key]['id'] = $item->id;
            $ann[$key]['title'] = $item->title;
        }
        $data['annList'] = $ann;

        //行业新闻
        $type = Info::ABOUT_NEWS;
        $news = [];
        $items = Info::find()->andWhere(['type'=>$type])->andWhere("start_time<:start_time",[':start_time'=>time()])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $news[$key]['id'] = $item->id;
            $news[$key]['title'] = $item->title;
            $news[$key]['created_at'] = date('Y-m-d', $item->created_at);
        }
        $data['news'] = $news;

        //行业动态
        $type = Info::ABOUT_MEDIA;
        $media = [];
        $items = Info::find()->andWhere(['type'=>$type])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $media[$key]['id'] = $item->id;
            $media[$key]['title'] = $item->title;
            $media[$key]['created_at'] = date('Y-m-d', $item->created_at);
        }
        $data['media'] = $media;

        //新手指引
        $guild = [];
        $items = Help::find()->andWhere(['forum_id'=>52684])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $guild[$key]['id'] = $item->id;
            $guild[$key]['title'] = $item->title;
        }
        $data['guild'] = $guild;
        //理财分享
        $share = [];
        $items = Help::find()->andWhere(['forum_id'=>52714])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $share[$key]['id'] = $item->id;
            $share[$key]['title'] = $item->title;
        }
        $data['share'] = $share;
        //安全保障
        $safe = [];
        $items = Help::find()->andWhere(['forum_id'=>52715])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $safe[$key]['id'] = $item->id;
            $safe[$key]['title'] = $item->title;
        }
        $data['safe'] = $safe;

        //幻灯片
        $slide = [];
        $items = Slide::find()->where(['type'=>Slide::TYPE_PC,'status'=>Slide::STATUS_YES])->orderBy(['code'=>SORT_DESC])->all();
        foreach($items as $key=>$item){
            $slide[$key]['url'] = $item->url;
            $slide[$key]['image'] = $item->image;
        }
        $data['slides'] = $slide;
        //还款公告
        $repay_list = [];
        $items = RepaymentPlan::find()->leftJoin('project','project.id=repayment_plan.project_id')->andWhere(['repayment_plan.status'=>1,'project.is_enabled'=>1])->orderBy('dateline desc')->limit(3)->all();
        foreach($items as $key=>$item){
            $repay_list[$key]['id'] = $item->id;
            $repay_list[$key]['name'] = $item->project->name;
            $repay_list[$key]['dateline'] = date('Y-m-d',$item->dateline);
        }
        $data['repay_list'] = $repay_list;
        //最新投标
        $invest = [];
        $items = Invest::find()->joinWith('user')->where(['is_pay'=>1])->andWhere(['>','amount',99])->orderBy(['created_at'=>SORT_DESC])->limit(8)->all();
        foreach($items as $key=>$item){
            $invest[$key]['username'] = Util::formatMaskUserName($item->user->username);
            $invest[$key]['amount'] = number_format($item->amount,2);
            $invest[$key]['pay_time'] = date('m-d H:i',$item->pay_time);
        }
        $data['invest'] = $invest;

        return $data;
    }

    private function indexWeixin(){
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

        //最近的项目
        $projects = [];
        $items = Project::find()->andWhere(['cate_id'=>Project::CATE_FIXED,'is_enabled'=>1])->orderBy(['created_at'=>SORT_DESC])->limit(3)->all();
        foreach($items as $key=>$item){
            $projects[$key]['id'] = $item->id;
            $projects[$key]['name'] = $item->name;
            $projects[$key]['status'] = $item->status;
            $projects[$key]['type_id'] = $item->type_id;
            $projects[$key]['interest'] = $item->interest;
            $projects[$key]['total_amount'] = $item->total_amount;
            $projects[$key]['ketou'] = $item->ketou;
            $projects[$key]['limit'] = $item->limit;
        }
        $data['project'] = $projects;

        //幻灯片
        $slide = [];
        $items = Slide::find()->where(['type'=>Slide::TYPE_WX,'status'=>Slide::STATUS_YES])->orderBy(['code'=>SORT_DESC])->all();
        foreach($items as $key=>$item){
            $slide[$key]['url'] = $item->url;
            $slide[$key]['image'] = $item->image;
        }
        $data['slides'] = $slide;

        $data['registerNumber'] = User::registerNumber();
        $data['totalInvestAmount'] = Invest::totalInvestAmount();
        return $data;
    }

    private function indexApp(){
        //注册人数
        $data['registerNumber'] = User::find()->count();
        //幻灯片
        $slide = Slide::find()->andWhere(['type'=>Slide::TYPE_APP,'status'=>Slide::STATUS_YES])->orderBy(['code'=>SORT_DESC])->all();
        $list = [];
        foreach($slide as $key=>$item){
            $list[$key]['title'] = $item->title;
            $list[$key]['image'] = $item->image;
            $list[$key]['url'] = $item->url;
            $list[$key]['code'] = $item->code;
        }
        $data['slide'] = $list;

        //项目列表
        $fixed_list =Project::findBySql('select * from (select * from project where is_enabled=1 and cate_id=:cate_id order by status asc ,id desc ) as p limit 5 ',[':cate_id'=>Project::CATE_FIXED])->all();
        $list = [];
        foreach($fixed_list as $key=>$item){
            $list[$key]['id'] = $item->id;
            $list[$key]['name'] = $item->name;
            $list[$key]['status'] = $item->status;
            $list[$key]['interest'] = $item->interest*100;
            $list[$key]['plus_interest'] = $item->plus_interest*100;
            $arr = Zhaiquan::typeOptions();
            $list[$key]['icon_name'] = $arr[$item->zhaiquan->type];
            $list[$key]['total_amount'] = number_format($item->total_amount/10000,2);
            $list[$key]['limit'] = $item->limit;
            // 万元收益
            $list[$key]['wan'] = number_format(10000 * $item->interest / 365 * $item->limit,2);
            $list[$key]['danyue'] = number_format(10000 * $item->interest / 365 * 30,2);
            $list[$key]['start_amount'] = $item->start_amount;
            $list[$key]['ketou'] = number_format($item->ketou/10000,2);
        }
        $data['list'] = $list;
        $number_total = User::find()->count();
        $data['member_total'] = number_format($number_total,0);

        return $data;
    }

}
