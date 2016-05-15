<?php
/**
 * Created by PhpStorm.
 * User: yangtanfang
 * Date: 16/2/3
 * Time: 17:11
 */
namespace api\modules\v1\component;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\web\Response;
use yii\filters\VerbFilter;


class MasterController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];


    /**
     * 继承
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'api\modules\v1\action\master\IndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => 'api\modules\v1\actions\master\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'api\modules\v1\actions\master\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'api\modules\v1\actions\master\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'api\modules\v1\actions\master\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }


    /**
     * 权限检查
     *
     * @param string $action
     * @param null $model
     * @param array $params
     */
    public function checkAccess($action, $model = null, $params = [])
    {
    }

}
