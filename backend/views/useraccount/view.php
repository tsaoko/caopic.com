<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserAccount */

$this->title = '用户第三方账号详情 '.$model->id;
$this->params['breadcrumbs'][] = ['label' => '用户第三方账号', 'url' => ['index']];
$this->params['breadcrumbs'][] = '用户第三方账号详情';
?>
<div class="user-account-view box box-success">
    <div class="box-header">
            <h5 class="box-title">用户第三方账号详情</h5>
            <div class="box-tools pull-right">
                <?= Html::a('<i class="fa fa-edit"></i> 修改', ['update', 'id' => $model->id]) ?>
                <?= Html::a('<i class="fa fa-trash-o"></i> 删除', ['delete', 'id' => $model->id], [
                    'data' => [
                        'confirm' => '您确定要删除这个项目吗？',
                        'method' => 'post',
                    ]
                ]) ?>
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
            'provider',
            'client_id',
            'data:ntext',
            'created_at',
        ],
    ]) ?>

        </div>
    </div>
</div>
