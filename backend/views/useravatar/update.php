<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserAvatar */

$this->title = '修改用户头像' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '用户头像管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="user-avatar-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
