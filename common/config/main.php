<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Asia/Shanghai',
    'language' => 'zh-CN',
    'name' => '草皮网',

    'components' => [

        'formatter' => [ //for the showing of date datetime
            'dateFormat' => 'yyyy-MM-dd',
            'locale' => 'zh-CN',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
        ],

        'db' => dirname(__DIR__).'/db.php',

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'oss' => [
            'class' => 'yiier\AliyunOSS\OSS',
            'accessKeyId' => 'cNzkVIqdOC853bdw', // 阿里云OSS AccessKeyID
            'accessKeySecret' => '9QnCIR7eR31h1g6ANEDHOQJdJsv6mL', // 阿里云OSS AccessKeySecret
            'bucket' => 'utan', // 阿里云的bucket空间
            'lanDomain' => 'oss-cn-hangzhou-internal.aliyuncs.com', // OSS内网地址
            'wanDomain' => 'oss-cn-hangzhou.aliyuncs.com', //OSS外网地址
            'isInternal' => false // 上传文件是否使用内网，免流量费（选填，默认 false 是外网）
        ],



    ],
];
