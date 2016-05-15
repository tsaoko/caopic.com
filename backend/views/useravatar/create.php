<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserAvatar */

$this->title = '新增用户头像';
$this->params['breadcrumbs'][] = ['label' => '用户头像管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新增';
?>
<div class="user-avatar-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
