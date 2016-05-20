<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '站点管理 ';
$this->params['breadcrumbs'][] = '站点管理';
$this->params['smallTitle'] = Html::a('<i class="fa fa-edit"></i> 新增', ['create'],['class'=>'btn btn-success']);
?>

<div class="site-index box box-success">

        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'layout' => '<div class="table-responsive">{items}</div><div class="row"><div class="col-md-7">{pager}</div><div class="col-md-5">{summary}</div></div>',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'url:url',
            'created_at',
            'updated_at',
            // 'logo',
            'is_wall:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    </div>
</div>
