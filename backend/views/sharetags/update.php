<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ShareTags */

$this->title = '修改分享标签' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '分享标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="share-tags-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
