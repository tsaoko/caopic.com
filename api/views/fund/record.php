<?php
use yii\grid\GridView;
use common\helpers\Util;

$this->title = "项目类型";

?>
<?php \yii\widgets\Pjax::begin(); ?>
<?php
echo GridView::widget([
    'id' => 'project-grid',
    'dataProvider' => $provider,
    'tableOptions' => ['class' => 'am-table record'],
    'layout'=>'{items}',
    'pager'=>[
        'options'=>['class'=>'hidden']
    ],
    'columns' => [
        ['attribute' => 'user_id', 'value' => function($model) {
                return Util::formatMaskUserName(\common\models\User::getUsername($model->user_id));
            }],
        ['attribute' => 'amount','value'=>function($model){
                return number_format($model->amount,2,'.',',');
            },'contentOptions'=>['style'=>'color:red'] ],
        ['attribute' => 'created_at', 'value' => function($model) {
                return date('Y-m-d H:i:s',$model->created_at);
            }],
        ['attribute' => 'status', ],
    ],
]);
?>
    <div style="background:none;">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
            'options' => [
                'class' => 'am-pagination'
            ],
            'nextPageLabel' => '&gt;',
            'prevPageLabel' => '&lt;',
            'maxButtonCount'=> '5',
        ]);
        ?>
    </div>
<?php \yii\widgets\Pjax::end(); ?>