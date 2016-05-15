<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户头像管理  &nbsp; '.Html::a('<i class="fa fa-edit"></i> 新增', ['create'],['class'=>'btn btn-success']);
$this->params['breadcrumbs'][] = '用户头像管理';
?>

<div class="user-avatar-index box box-success">

        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'layout' => '<div class="table-responsive">{items}</div><div class="row"><div class="col-md-7">{pager}</div><div class="col-md-5">{summary}</div></div>',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'image',
            'is_default:boolean',
            'created_at:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    </div>
</div>
