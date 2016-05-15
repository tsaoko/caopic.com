<?php

namespace api\modules\v3\controllers;


use api\modules\v3\models\InviteRedeemForm;
use common\api\yeepay\YeePayPc;
use common\helpers\Util;
use common\models\Detail;
use common\models\InviteRedeem;
use common\models\User;
use Yii;
use api\modules\v3\component\ActiveController;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\base\Exception;


class PercentageController extends ActiveController
{
    //提成相关
    public $modelClass = 'api\modules\v3\models\Percentage';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                ['class' => HttpBearerAuth::className()],
                ['class' => QueryParamAuth::className(), 'tokenParam' => 'access_token'],
            ]
        ];

        return $behaviors;
    }

    public function actionRedeem(){
        $post = Yii::$app->request->post();
        $user = Yii::$app->user->identity;

        $model = new InviteRedeemForm();
        $model->pay_type = isset($post['pay_type'])?$post['pay_type']:InviteRedeem::PAY_TYPE_LLPAY;
        $model->amount = isset($post['amount'])?$post['amount']:0;
        if ($model->validate() )
        {
            $transaction = Yii::$app->db->beginTransaction();

            try{
                $redeem = new InviteRedeem();
                $redeem->user_id = $user->id;
                $redeem->amount = $model->amount;
                $redeem->status = InviteRedeem::STATUS_YES;
                $redeem->ip = Util::getClientIP();
                $redeem->pay_type = $model->pay_type;
                $redeem->save(false);

                //易宝提现至余额
                if($model->pay_type == InviteRedeem::PAY_TYPE_YEEPAY){

                    if($user->is_yee!=1)
                    {
                        return ['status' => 'fail', 'data'=>[], 'msg' => '未开通易宝支付'];
                    }

                    $requestNo = 'f-r-'.Util::rand();
                    $yee = new YeePayPc();
                    $result = $yee->directTransaction([['id' => $user->yeepayUserNo, 'amount' => $model->amount]], $requestNo, '');

                    if ($result && $result->code == 1) {
                        User::ChangeYeeBalance(
                            $user->id,
                            $model->amount,
                            [
                                'sn' => $requestNo,
                                'type' => Detail::TYPE_INVITE_REDEEM,
                                'remark' =>Detail::$type_list[Detail::TYPE_INVITE_REDEEM]
                            ]
                        );
                    } else {
                        throw new Exception('提现失败-' . var_export($result, true));
                    }
                }

                //连连提现至余额
                if($model->pay_type == InviteRedeem::PAY_TYPE_LLPAY){
                    //改变用户账户金额
                    User::ChangeLlBalance(
                        $user->id,
                        $model->amount,
                        [
                            'sn' => $redeem->id,
                            'type' => Detail::TYPE_INVITE_REDEEM,
                            'remark' => Detail::$type_list[Detail::TYPE_INVITE_REDEEM]
                        ]
                    );
                }
                $transaction->commit();
                return ['status' => 'success', 'data'=>[], 'msg' => '提成提现成功'];
            }catch (Exception $e){
                $transaction->rollBack();
                return ['status' => 'fail', 'data'=>[], 'msg' => '提成提现失败'];
            }
        } else {
            return ['status' => 'fail', 'data'=>[], 'msg' => array_values($model->getFirstErrors())[0]];
        }
    }
}