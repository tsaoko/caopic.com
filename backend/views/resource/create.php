<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Resource */

$this->title = '新增资源';
$this->params['breadcrumbs'][] = ['label' => '资源管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新增';
?>
<div class="resource-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
