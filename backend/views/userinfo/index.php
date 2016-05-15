<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户信息管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-info-index box box-success">

        <div class="box-body">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class'=>'table table-striped'],
        'layout' => '<div class="table-responsive">{items}</div><div class="row"><div class="col-md-7">{pager}</div><div class="col-md-5">{summary}</div></div>',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'user.username',
            'email:email',
            'is_email_check:email',
            'mobile',
            'is_mobile_cechk',
            'realname',
            // 'info:ntext',
            // 'prev_login_time:datetime',
            // 'prev_login_ip',
            // 'prev_login_device',
            // 'last_login_time:datetime',
            // 'last_login_ip',
            // 'last_login_device',
            // 'created_at',
            // 'updated_at',
            'sex',

            ['class' => 'yii\grid\ActionColumn','template' => '{view}  &nbsp;  {update}'],
        ],
    ]); ?>

    </div>
</div>
