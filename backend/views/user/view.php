<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '会员详情 '.$model->id;
$this->params['breadcrumbs'][] = ['label' => '会员', 'url' => ['index']];
$this->params['breadcrumbs'][] = '会员详情';
?>
<div class="user-view box box-success">
    <div class="box-header">
            <h5 class="box-title">会员详情</h5>
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
            'username',
            'password_hash',
            'password_reset_token',
            'auth_key',
            'access_token',
            'status',
            'statusValue',
            'role',
            'created_at',
            'updated_at',
        ],
    ]) ?>

        </div>
    </div>
</div>
