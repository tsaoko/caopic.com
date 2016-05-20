<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分享管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="share-index box box-success">

        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'layout' => '<div class="table-responsive">{items}</div><div class="row"><div class="col-md-7">{pager}</div><div class="col-md-5">{summary}</div></div>',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'title',
            'resource_id',
            'desc:ntext',
            // 'source:ntext',
            // 'site_id',
             'created_at:datetime',
            // 'updated_at',
             'level',
            // 'user_share_group_id',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} &nbsp; &nbsp; {update}'],
        ],
    ]); ?>

    </div>
</div>
