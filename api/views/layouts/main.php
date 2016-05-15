<?php
use api\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?> - <?= \Yii::$app->setting->get('siteTitle') ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>
<div>
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>