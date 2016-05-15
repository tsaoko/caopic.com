<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use kartik\builder\FormGrid;

/* @var $this yii\web\View */
/* @var $model common\models\UserInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-info-form box box-success">
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
                'email' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'is_email_check' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'mobile' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'is_mobile_cechk' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'realname' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'info' => ['type'=>Form::INPUT_TEXTAREA, 'options'=>['rows' => 6, 'placeholder'=>'']],
                'prev_login_time' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'prev_login_ip' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'prev_login_device' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'last_login_time' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'last_login_ip' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'last_login_device' => ['type'=>Form::INPUT_TEXT, 'options'=>['maxlength' => true, 'placeholder'=>'']],
                'created_at' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'updated_at' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
                'sex' => ['type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'']],
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
