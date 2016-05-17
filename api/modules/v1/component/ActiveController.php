<?php
namespace api\modules\v1\component;

use Yii;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\filters\Cors;
use yii\rest\Serializer;
use yii\filters\ContentNegotiator;

use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;


class ActiveController extends \yii\rest\ActiveController
{

    public $allowActions = [];

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    //header传参：key=>Authorization,value=>Bearer (access_token)
    //或GET传参：key=>access_token,value=>(access_token)
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(),
        [
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET','HEAD','POST'],
                    'Access-Control-Allow-Credentials' => true,
                ]
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    ['class' => HttpBasicAuth::className()],
                    ['class' => HttpBearerAuth::className()],
                    ['class' => QueryParamAuth::className(), 'tokenParam' => 'access_token'],
                ]
            ],
        ]);


        // 过虑不需要权限的控制器
        if( in_array($this->action->id,$this->allowActions) )
        {
            unset($behaviors['authenticator']);
        }

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::className(),
            'actions' => $this->verbs(),
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
        ];
        $behaviors['exceptionFilter'] = [
            'class' => ErrorToExceptionFilter::className()
        ];
        return $behaviors;
    }


    public function actions()
    {
        $actions = parent::actions();
        //注销系统自带的实现方法
        unset($actions['index'], $actions['update'], $actions['create'], $actions['delete'], $actions['view']);
        $actions['index'] = [
            'class'=>'api\modules\v1\action\master\ListAction',
            'modelClass'=>$this->modelClass
        ];
        $actions['view'] = [
            'class'=>'api\modules\v1\action\master\ViewAction',
            'modelClass'=>$this->modelClass
        ];
        return $actions;
    }

}
