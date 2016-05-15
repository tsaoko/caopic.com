<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ShareTags */

$this->title = '分享标签详情 '.$model->name;
$this->params['breadcrumbs'][] = ['label' => '分享标签', 'url' => ['index']];
$this->params['breadcrumbs'][] = '分享标签详情';
?>
<div class="share-tags-view box box-success">
    <div class="box-header">
            <h5 class="box-title">分享标签详情</h5>
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
            'name',
            'count',
            'created_at',
            'updated_at',
        ],
    ]) ?>

        </div>
    </div>
</div>
