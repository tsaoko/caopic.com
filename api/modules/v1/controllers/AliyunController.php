<?php

namespace api\modules\v1\controllers;


use Yii;
use api\modules\v1\component\ActiveController;
use common\models\User;
use common\models\Resource;
use common\helpers\UtilHelper;
use yii\helpers\Url;

class AliyunController extends ActiveController
{

    public $modelClass;

    public $allowActions = [
        'signature',
        'osscallback',
    ];


    public function init()
    {
        $this->modelClass = Resource::className();
    }


    /**
     * 获取直传资源的签名与回调
     */
    public function actionSignature()
    {
        $oss = \Yii::$app->oss;
        $callbackUrl = Url::to(['/aliyun/osscallback'],true);
        $response = $oss->getSignature('user-dir/', $callbackUrl,300);
        return $response;
    }


    /**
     * 资源上传成功回调动作
     *
     * 验证是否为阿里云oss发送过来的数据
     * 效验数据
     * 写入资源表
     * TODO 判断资源是否重复
     */
    public function actionOsscallback()
    {
        $authorizationBase64 = '';
        $pubKeyUrlBase64 = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION']))
        {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL']))
        {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        // 没有拿到授权
        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '')
        {
            Yii::$app->end(403);
        }

        // 2.获取OSS的签名
        $authorization = base64_decode($authorizationBase64);

        // 3.获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);
        if ($pubKey == "")
        {
            Yii::$app->end(403);
        }

        // 4.获取回调body
        $body = file_get_contents('php://input');
        $post = Yii::$app->request->post();

        // 5.拼接待签名字符串
        $authStr = '';
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if ($pos === false)
        {
            $authStr = urldecode($path)."\n".$body;
        }
        else
        {
            $authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
        }

        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1)
        {

            // TODO 使用etag做排重工作存地的时候，删除当前上传的内容

            // 写入数据表
            $model = new Resource;
            $model->etag = $post['etag'];
            $model->filename = $post['filename'];
            $model->size = $post['size'];
            $model->type = $post['mimeType'];
            $model->name = $post['filename'];
            $model->provider = 'aliyun';
            $model->height = intval($post['height']);
            $model->width = intval($post['width']);
            $model->format = $post['format'];
            $model->bucket = $post['bucket'];
            $model->save(false);

            $data = ["Status"=>"Ok"];
            echo $data;
        }
        else
        {
            Yii::$app->end(403);
        }
    }


    public function actionCollect()
    {

    }


}
