<?php

$this->title = "项目类型";
$cate=array('0'=>"金羊定期",1=>"新手专享");
?>
<div class="am-tabs" data-am-tabs="{noSwipe: 1}" id="doc-tab-demo-1">
    <ul class="am-tabs-nav am-nav am-nav-tabs am-nav-justify">
        <li class="am-active"><a href="javascript: void(0)">项目介绍</a></li>
        <li><a href="javascript: void(0)">企业介绍</a></li>
        <li><a href="javascript: void(0)">担保详情</a></li>
    </ul>

    <div class="am-tabs-bd art">
        <div class="am-tab-panel am-active">
            <div>
                <h5>项目名称</h5>
                <p><?=$model->name?></p>
            </div>
            <div>
                <h5>项目描述</h5>
                <p><?= $model->content?></p>
            </div>
            <?php if($model->zhaiquan):?>
                <div>
                    <h5>保障机构</h5>
                    <p><?= \common\models\Company::getName($model->zhaiquan->company->id)?></p>
                </div>
                <div>
                    <h5>保障措施</h5>
                    <p><?= $model->zhaiquan->company->insurance?></p>
                </div>
                <div>
                    <h5>还款来源</h5>
                    <p><?= $model->zhaiquan->repay_source?></p>
                </div>
            <?php endif?>
            <div>
                <h5>投资条件</h5>
                <p><?=$model->start_amount;?>元起投，按<?=$model->start_amount;?>元的整数倍递增 </p>
            </div>
            <div>
                <h5>计息方式</h5>
                <p>满标起息，按日计息</p>
            </div>
            <div>
                <h5>还款方式</h5>
                <p>按月付息，到期还本</p>
            </div>
            <div>
                <h5>相关费用</h5>
                <p>无认购费、管理费</p>
            </div>
            <div>
                <h5>保障类型</h5>
                <p>合作机构提供100%本息保障</p>
            </div>
        </div>
        <div class="am-tab-panel">
            <?php if($model->zhaiquan):?>
                <div>
                    <h5>企业介绍</h5>
                    <p><?= $model->zhaiquan->background?></p>
                </div>
                <div>
                    <h5>营业范围</h5>
                    <p><?= $model->zhaiquan->business_scope?></p>
                </div>
                <div>
                    <h5>经营状况</h5>
                    <p><?= $model->zhaiquan->operation_condition?></p>
                </div>
            <?php endif?>
        </div>
        <div class="am-tab-panel">
            <?php if($model->zhaiquan):?>
                <div class="item">
                    <h5>担保机构</h5>
                    <p><?= \common\models\Company::getName($model->zhaiquan->company->id)?></p>
                </div>
                <div class="item">
                    <h5>担保机构简介</h5>
                    <p><?= $model->zhaiquan->company->content?></p>
                </div>
                <div class="item">
                    <h5>抵质押物信息</h5>
                    <p><?= $model->zhaiquan->pledge?></p>
                </div>

                <div class="item">
                    <h5>风险控制措施</h5>
                    <p><?= $model->zhaiquan->risk_control?></p>
                </div>

                <div class="item">
                    <h5>涉诉信息</h5>
                    <p><?= $model->zhaiquan->legal?></p>
                </div>

                <div class="item">
                    <h5>担保机构意见</h5>
                    <p><?= $model->zhaiquan->guarantor_opinion?></p>
                </div>
            <?php endif?>
        </div>
    </div>
</div>
<h3 style="text-align: center">企业相关照片</h3>
<ul data-am-widget="gallery" class="am-gallery am-avg-sm-2 am-gallery-imgbordered" data-am-gallery="{pureview: 1}">
    <?php foreach($imgCompanys as $item):?>
        <li>
            <div class="am-gallery-item">
                <img src="<?=$item['min'];?>" alt="<?=$item['title'];?>" data-rel="<?=$item['max'];?>"/>
            </div>
        </li>
    <?php endforeach;?>
</ul>

<h3 style="text-align: center">担保相关照片</h3>
<ul data-am-widget="gallery" class="am-gallery am-avg-sm-2 am-gallery-imgbordered" data-am-gallery="{pureview: 1}">
    <?php foreach($imgDanbaos as $item):?>
        <li>
            <div class="am-gallery-item">
                <img src="<?=$item['min'];?>" alt="<?=$item['title'];?>" data-rel="<?=$item['max'];?>"/>
            </div>
        </li>
    <?php endforeach;?>
</ul>