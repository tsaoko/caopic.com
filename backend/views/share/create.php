<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Share */

$this->title = '新增分享';
$this->params['breadcrumbs'][] = ['label' => '分享管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新增';
?>
<div class="share-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
