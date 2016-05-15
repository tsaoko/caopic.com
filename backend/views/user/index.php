<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index box box-success">
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
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            // 'password_hash',
            // 'password_reset_token',
            // 'auth_key',
            // 'access_token',
            'status',
            'role',
             'created_at',
             'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    </div>
</div>
