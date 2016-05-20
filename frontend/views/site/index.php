<?php
/* @var $this yii\web\View */
$this->title = Yii::$app->name.' - '.Yii::$app->params['slogo'];

$this->registerJsFile('/lib/plupload-2.1.2/js/plupload.full.min.js');
$this->registerJsFile('/upload.js');
?>

<p>
    弄个完整表单做分享
</p>
