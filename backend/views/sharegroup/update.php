<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ShareGroup */

$this->title = '修改分享小组' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '分享小组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="share-group-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
