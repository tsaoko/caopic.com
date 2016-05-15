<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Site */

$this->title = '修改站点' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '站点管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="site-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
