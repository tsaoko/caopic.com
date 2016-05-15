<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Resource */

$this->title = '修改资源' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '资源管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="resource-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
