<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ShareTags */

$this->title = '新增分享标签';
$this->params['breadcrumbs'][] = ['label' => '分享标签管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新增';
?>
<div class="share-tags-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
