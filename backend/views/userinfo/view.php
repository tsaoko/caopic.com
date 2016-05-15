<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserInfo */

$this->title = '用户信息详情 '.$model->id;
$this->params['breadcrumbs'][] = ['label' => '用户信息', 'url' => ['index']];
$this->params['breadcrumbs'][] = '用户信息详情';
?>
<div class="user-info-view box box-success">
    <div class="box-header">
            <h5 class="box-title">用户信息详情</h5>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-edit"></i> 修改', ['update', 'id' => $model->id]) ?>

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
            'user_id',
            'user.username',
            'email:email',
            'is_email_check:email',
            'mobile',
            'is_mobile_cechk',
            'realname',
            'info:ntext',
            'prev_login_time:datetime',
            'prev_login_ip',
            'prev_login_device',
            'last_login_time:datetime',
            'last_login_ip',
            'last_login_device',
            'created_at',
            'updated_at',
            'sex',
        ],
    ]) ?>

        </div>
    </div>
</div>
