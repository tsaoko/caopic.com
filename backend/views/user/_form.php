<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormGrid;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form box box-success">
    <div class="box-header">
        <h5 class="box-title"><?php  echo $this->title ?></h5>
        <div class="box-tools pull-right">
            <a class="close-link" href="<?php echo Url::toRoute(['index']) ?>">
                <i class="fa fa-undo"></i> 返回
            </a>
        </div>
    </div>

    <div class="box-body">
<?php   
$form = ActiveForm::begin(['options'=>['enctype'=>'multipart/form-data']]);
echo FormGrid::widget([
    'model' => $model,
    'form' => $form,
    'columns'=>2,
    'autoGenerateColumns' => false,
    'rows' => [
        [
            'attributes' => [
                                'status' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
            ],
        ],
    ]
]);
?>
    </div>
    <div class="box-footer">
        <div class="pull-right">
            <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
<?php ActiveForm::end();
?>
</div>
