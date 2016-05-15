<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ShareGroup */

$this->title = '新增分享小组';
$this->params['breadcrumbs'][] = ['label' => '分享小组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新增';
?>
<div class="share-group-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
