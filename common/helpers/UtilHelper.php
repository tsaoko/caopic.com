<?php
namespace common\helpers;



class UtilHelper
{
    /**
     * 随机编号
     * @return [type] [description]
     */
    public static function rand()
    {
       list($usec, $sec) = explode(" ", microtime());
       return date('YmdHis').substr(round($usec*100000),0,3).rand(10,99);
    }


    /**
     * 获取客户端ip
     */
    public static function getClientIP()
    {
        $ip = "unknown";
        /*
         * 访问时用localhost访问的，读出来的是“::1”是正常情况。
         * ：：1说明开启了ipv6支持,这是ipv6下的本地回环地址的表示。
         * 使用ip地址访问或者关闭ipv6支持都可以不显示这个。
         * */
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($_SERVER["HTTP_CLIENT_ip"])) {
                $ip = $_SERVER["HTTP_CLIENT_ip"];
            } else {
                $ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_ip')) {
                $ip = getenv('HTTP_CLIENT_ip');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }
        if(trim($ip)=="::1"){
            $ip="127.0.0.1";
        }
        return $ip;
    }


    /**
     * 下载文件
     */
    public static function getFile($url, $save_to, $referer = '')
    {
        if (!strpos($url, '://'))
            return 'Invalid URI';
        $content = '';
        if (function_exists('curl_init'))
        {
            $retry = 0;
            do
            {
                $handle = curl_init();
                $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
                if ($referer)
                {
                    curl_setopt($handle, CURLOPT_REFERER, $referer);
                }
                curl_setopt($handle, CURLOPT_USERAGENT, $user_agent);
                curl_setopt($handle, CURLOPT_URL, $url);
                curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
//                 curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($handle, CURLOPT_ENCODING, 'gzip');
                $content = curl_exec($handle);
                curl_close($handle);
                $retry += 1;
            } while ($retry < 4 && empty($content));

        }
        elseif (function_exists('fsockopen'))
        {
            $urlinfo = parse_url($url);
            $host = $urlinfo['host'];
            $str = explode($host, $url);
            $uri = $str[1];
            unset($urlinfo, $str);
            $content = '';
            $fp = fsockopen($host, 80, $errno, $errstr, 30);
            if (!$fp)
            {
                $content = 'Can Not Open Socket...';
            } else
            {
                stream_set_timeout($fp, 10); //超时时间

                $out = "GET " . $uri . "/  HTTP/1.1\r\n";
                $out.= "Host: $host \r\n";
                $out.= "Accept: */*\r\n";
                $out.= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.1)\r\n)";
                $out.= "Connection: Keep-Alive\r\n\r\n";
                fputs($fp, $out);
                while (!feof($fp))
                {
                    $status = stream_get_meta_data($fp);
                    //读取数据超时
                    if ($status['timed_out'])
                        break;
                    $content .= fgets($fp, 4069);
                }
                fclose($fp);
            }
        }
        if (empty($content))
            return false;

        return @file_put_contents($save_to, $content);
    }


    /**
     * 将指定时间截转换成ISO8601标准时间
     *
     */
    public static function gmtIso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }


}
