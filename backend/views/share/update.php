<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Share */

$this->title = '修改分享' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '分享管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="share-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
