<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace api\modules\v3\action\master;
use api\modules\v3\models\UserAgent;
use common\models\Invite;
use common\models\Percentage;
use common\models\Project;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\rest\Action;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ListAction extends Action
{
    /**
     * @var callable a PHP callable that will be called to prepare a data provider that
     * should return a collection of the models. If not set, [[prepareDataProvider()]] will be used instead.
     * The signature of the callable should be:
     *
     * ```php
     * function ($action) {
     *     // $action is the action object currently running
     * }
     * ```
     *
     * The callable should return an instance of [[ActiveDataProvider]].
     */
    public $prepareDataProvider;
    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }
        $params = Yii::$app->getRequest()->get('params');
        $sort = Yii::$app->getRequest()->get('sort');
        $size = Yii::$app->getRequest()->get('size')?Yii::$app->getRequest()->get('size'):10;
        return $this->prepareDataProvider($params,$sort,$size);
    }
    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider($params,$sort,$size)
    {
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }
        /**
         * @var \yii\db\BaseActiveRecord $modelClass
         */
        $modelClass = $this->modelClass;
        $params = json_decode($params);

        $sort = json_decode($sort);
        $query = $modelClass::find();

        $url = Yii::$app->requestedRoute;
        $user = Yii::$app->user->identity;
        switch($url){
            case 'v3/repay/list':
                $type = isset($_GET['type'])?$_GET['type']:'to';//from借款还款；to投资还款
                $query->andWhere([$type.'_user_id'=>$user->id]);
                break;
            case 'v3/repayment/list':
                $arr = Project::find()->select('*,(select min(dateline) from repayment_plan where project_id = project.id and status = 0 group by project_id) as repayment_date')->andWhere(['user_id' => $user->id,'status'=>[Project::STATUS_REPAYMENT,Project::STATUS_FINISHED]])->asArray()->all();
                return new ArrayDataProvider([
                    'allModels' => $arr,
                    'pagination' => [
                        'pageSize' => $size,
                    ],
                ]);
            case 'v3/invite/list':
                $level = Yii::$app->request->get('level');
                if($level == 'level')
                {
                    $userId = [];
                    $users = Invite::find()->where(['from_user_id'=>$user->id])->all();
                    foreach($users as $item)
                    {
                        $userId[] = $item->user_id;
                    }
                    $query->andWhere(['from_user_id'=>$userId]);
                }else{
                    $query->andWhere(['from_user_id'=>$user->id]);
                }
                break;
            case 'v3/percentage/list':
                $type = isset($_GET['type'])?$_GET['type']:'user';
                switch($type) {
                    case 'user':
                        $query = Invite::find()->select(['u.username,(select sum(amount) from percentage where invest_user_id = invite.user_id and level=1) as total_amount,(select sum(percentage) from percentage where invest_user_id = invite.user_id and level=1) as total_percent'])->leftJoin('user u','u.id=invite.user_id')->andWhere(['invite.from_user_id'=>$user->id]);
                        break;
                    case 'month':
                        $query = $query->select(['FROM_UNIXTIME(created_at,\'%Y-%m\') as dateline,sum(amount) as total_amount,sum(percentage) as total_percent'])->andWhere(['user_id'=>$user->id])->groupBy(['FROM_UNIXTIME(created_at,\'%Y-%m\')']);
                        break;
                    case 'all':
                    default:
                        break;
                }
                $arr = $query->asArray()->all();

                foreach($arr as $key=>$item){
                    if($type=='user'||$type=='month'){
                        $item['total_amount'] = number_format($item['total_amount'],2);
                        $item['total_percent'] = number_format($item['total_percent'],2);
                    } else {
                        $item['amount'] = number_format($item['amount'],2);
                        $item['interest'] = number_format($item['interest'],2);
                        $item['percentage'] = number_format($item['percentage'],2);
                        $item['status'] = Percentage::$status_list[$item['status']];
                        $item['type'] = Percentage::$type_list[$item['type']];
                        $item['created_at'] = date('Y-m-d H:i:s',$item['created_at']);
                    }
                    $arr[$key] = $item;
                }

                return new ArrayDataProvider([
                    'allModels' => $arr,
                    'pagination' => [
                        'pageSize' => $size,
                    ],
                ]);
            case 'v3/recharge/list':
            case 'v3/withdraw/list':
            case 'v3/message/list':
            case 'v3/invest/list':
            case 'v3/detail/list':
            case 'v3/score/list':
            case 'v3/coupon/list':
                $query->andWhere(['user_id'=>$user->id]);
                break;
            default:
                break;
        }

        if($params){
            foreach($params as $key=>$value){
                $query->andWhere([$key=>$value]);
            }
        }

        if($sort){
            $str = '';
            foreach($sort as $key=>$value){
                $str .= $key.' '.$value.',';
            }
            $str = rtrim($str,',');
            $query->orderBy($str);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $size,
            ],
        ]);
    }
}