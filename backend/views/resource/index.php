<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '资源管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="resource-index box box-success">
        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'layout' => '<div class="table-responsive">{items}</div><div class="row"><div class="col-md-7">{pager}</div><div class="col-md-5">{summary}</div></div>',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'provider',
            'name',
            'filename',
            'size',
            'type',

            ['class' => 'yii\grid\ActionColumn','template' => ' {view} &nbsp; &nbsp; {update}'],
        ],
    ]); ?>

    </div>
</div>
