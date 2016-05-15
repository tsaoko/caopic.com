<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserAccount */

$this->title = '修改用户第三方账号' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '用户第三方账号管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="user-account-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
