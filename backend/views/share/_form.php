<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormGrid;

/* @var $this yii\web\View */
/* @var $model common\models\Share */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="share-form box box-success">
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
                                'user_id' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'title' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'resource_id' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'desc' => ['type'=>Form::INPUT_TEXTAREA, 'options'=>['rows' => 6, 'placeholder'=>'']],
                'source' => ['type'=>Form::INPUT_TEXTAREA, 'options'=>['rows' => 6, 'placeholder'=>'']],
                'site_id' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'created_at' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'updated_at' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'level' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'user_share_group_id' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
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