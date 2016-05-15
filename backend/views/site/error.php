<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->params['breadcrumbs'][] = '出错提示';
?>
<!-- Main content -->
<section class="content">

<div class="box box-solid box-default">

    <div class="box-header">
        出错提示：
    </div>
    <div class="box-body">
        <div class="error-page">
            <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

            <div class="error-content">
                <h3><?= $name ?></h3>

                <p>
                    <?= nl2br(Html::encode($message)) ?>
                </p>

                <p>
                    点击<a href='<?= Yii::$app->homeUrl ?>'>返回首页</a>
                </p>

                <!--form class='search-form'>
                    <div class='input-group'>
                        <input type="text" name="search" class='form-control' placeholder="Search"/>

                        <div class="input-group-btn">
                            <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form-->

            </div>
        </div>
    </div>

</div>



</section>
