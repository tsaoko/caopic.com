<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserInfo */

$this->title = '修改用户信息' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '用户信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="user-info-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
