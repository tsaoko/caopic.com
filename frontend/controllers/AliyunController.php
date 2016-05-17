<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Url;
use common\helpers\UtilHelper;
use common\models\Resource;

/**
 * Site controller
 */
class AliyunController extends Controller
{

    public $enableCsrfValidation = false;


    public function actionOss()
    {
        // 1.获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";
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


            header("Content-Type: application/json");
            $data = array("Status"=>"Ok");
            echo json_encode($data);
        }
        else
        {
            Yii::$app->end(403);
        }

    }


}
