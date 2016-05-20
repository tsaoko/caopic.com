<?php

/* @var $this yii\web\View */

$this->title = Yii::$app->name.' - '.Yii::$app->params['slogo'];

$this->registerJsFile('/lib/plupload-2.1.2/js/plupload.full.min.js');
$this->registerJsFile('/upload.js');
?>
<style media="screen">

	.progress{
		margin-top:2px;
	    width: 200px;
	    height: 14px;
	    margin-bottom: 10px;
	    overflow: hidden;
	    background-color: #f5f5f5;
	    border-radius: 4px;
	    -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
	    box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
	}
	.progress-bar{
		background-color: rgb(92, 184, 92);
		background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.14902) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.14902) 50%, rgba(255, 255, 255, 0.14902) 75%, transparent 75%, transparent);
		background-size: 40px 40px;
		box-shadow: rgba(0, 0, 0, 0.14902) 0px -1px 0px 0px inset;
		box-sizing: border-box;
		color: rgb(255, 255, 255);
		display: block;
		float: left;
		font-size: 12px;
		height: 20px;
		line-height: 20px;
		text-align: center;
		transition-delay: 0s;
		transition-duration: 0.6s;
		transition-property: width;
		transition-timing-function: ease;
		width: 266.188px;
	}

</style>

这里不要看了，要做vue + api 展示的。

<p>

    目前暂留这个栏目是做各种功能的测试展示。
</p>
<form name=theform>
<input type="radio" name="myradio" value="local_name" checked=true/> 上传文件名字保持本地文件名字
<input type="radio" name="myradio" value="random_name" /> 上传文件名字是随机文件名, 后缀保留
</form>

<h4>您所选择的文件列表：</h4>
<div id="ossfile">你的浏览器不支持flash,Silverlight或者HTML5！</div>

<br/>


<div id="container">
	<a id="selectfiles" href="javascript:void(0);" class='btn'>选择文件</a>
	<a id="postfiles" href="javascript:void(0);" class='btn'>开始上传</a>
</div>

<pre id="console"></pre>
