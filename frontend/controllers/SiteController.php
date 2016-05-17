<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Url;
use common\helpers\UtilHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $oss = \Yii::$app->oss;
        $fh = Yii::getAlias('@frontend/web/1.pptx');

        // 上传一个文件
        //$oss->upload('1.pptx', $fh); // 上传一个文件

        // 删除一个文件
        //$oss->delete('index.php');

        // 获取100个文件名
        // $files = $oss->getAllObject();


        return $this->render('index');
    }


    /**
     * 获取上传签名
     */
    public function actionGet()
    {
        $oss = \Yii::$app->oss;
        $callbackUrl = Url::to(['/site/callback'],true);
        $response = $oss->getSignature('user-dir/', $callbackUrl,30);
        echo $response;
    }


    /**
     * 回调数据保存
     */
    public function actionCallback()
    {
        $this->enableCsrfValidation = false;

        Yii::info('callback:'.var_dump(Yii::$app->request->post,true));
    }


}
