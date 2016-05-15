<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分享标签管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="share-tags-index box box-success">
<div class="box-header with-border">

    <?php echo Html::a('<i class="fa fa-edit"></i> 新增', ['create'],['class'=>'btn btn-success']); ?>

    <div class="box-tools pull-right">
        <?php echo Html::a('<i class="fa fa-search"></i> 高级搜索', '#',['class'=>'btn']) ?>
    </div>
</div>

        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'layout' => '<div class="table-responsive">{items}</div><div class="row"><div class="col-md-7">{pager}</div><div class="col-md-5">{summary}</div></div>',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'count',
            'created_at',
            'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    </div>
</div>
