<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2015/9/29
 * Time: 14:28
 */
namespace api\components;

use common\models\User;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Application;

class Oauth{

    public $client_id;
    public $client_secret;

    public function __construct()
    {
        $this->client_id = 'testclient';
        $this->client_secret = 'testpass';
    }

    //oauth2 获取access_token
    public function getAccessToken($username, $password, $grant_type='password'){
        $params = [
            'grant_type'=>$grant_type,
            'client_id' =>$this->client_id,
            'client_secret'=>$this->client_secret,
            'username'=>$username,
            'password'=>$password
        ];
        $url = Url::toRoute(['/oauth2/token'],true);
        $result = $this->getHttpResponsePOST($url,$params);
        return Json::decode($result,true);
    }

    //oauth2 重新获取access_token
    public function refreshAccessToken($refresh_token){
        $params = [
            'grant_type'=>'refresh_token',
            'client_id' =>$this->client_id,
            'client_secret'=>$this->client_secret,
            'refresh_token'=>$refresh_token,
        ];
        $url = Url::toRoute(['/oauth2/token'],true);
        $result = $this->getHttpResponsePOST($url,$params);
        return Json::decode($result,true);
    }

    function getHttpResponsePOST($url, $para, $header=0, $input_charset = '') {
        $para = $this->buildRequestPara($para);
        if (trim($input_charset) != '') {
            $url = $url."_input_charset=".$input_charset;
        }
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
        curl_setopt($curl, CURLOPT_HEADER, $header); // 过滤HTTP头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
        curl_setopt($curl,CURLOPT_POST,true); // post传输数据
        curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
        $responseText = curl_exec($curl);
        curl_close($curl);

        return $responseText;
    }

    private function buildRequestPara($para_temp) {
        $para_sort = $this->paraFilter($para_temp);
        foreach ($para_sort as $key => $value) {
            $para_sort[$key] = $value;
        }
        return $para_sort;
    }

    function paraFilter($para) {
        $para_filter = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "")continue;
            else	$para_filter[$key] = $para[$key];
        }
        return $para_filter;
    }
}
