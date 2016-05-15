<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Site */

$this->title = '新增站点';
$this->params['breadcrumbs'][] = ['label' => '站点管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新增';
?>
<div class="site-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
