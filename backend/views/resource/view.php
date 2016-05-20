<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Resource */

$this->title = '资源详情 '.$model->name;
$this->params['breadcrumbs'][] = ['label' => '资源', 'url' => ['index']];
$this->params['breadcrumbs'][] = '资源详情';
?>
<div class="resource-view box box-success">
    <div class="box-header">
            <h5 class="box-title">资源详情</h5>
            <div class="box-tools pull-right">
                <a class="close-link" href="<?=  Url::toRoute(['index']) ?>">
                    <i class="fa fa-undo"></i> 返回
                </a>
            </div>
        </div>
        <div class="box-body">

    <?= DetailView::widget([
        'model' => $model,
        'template' => '<tr><th width="20%">{label}</th><td>{value}</td></tr>',
        'attributes' => [
            'id',
            'provider',
            'name',
            'filename',
            'size',
            'type',
        ],
    ]) ?>

        </div>
    </div>
</div>
