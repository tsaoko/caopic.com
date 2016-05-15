<?php

namespace backend\components;

use yii\web\Controller as WebController;


class Controller extends WebController
{

    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/image/{yyyy}/{mm}{dd}/{time}{rand:6}" //上传保存路径
                ],
            ]
        ];
    }


}
