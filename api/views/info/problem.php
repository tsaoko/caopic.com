<?php

$this->title = "常见问题";

?>


<div class="content" style="padding:15px 10px;">




    <div class="am-panel-group" id="accordion">

        <?php foreach($data as $index => $item): ?>
            <div class="am-panel am-panel-default">
                <div class="am-panel-hd">
                    <h4 class="am-panel-title" data-am-collapse="{parent: '#accordion', target: '#do-not-say-<?=$index;?>'}">
                        <?=$item->title;?>
                    </h4>
                </div>
                <div id="do-not-say-<?=$index;?>" class="am-panel-collapse am-collapse <?php echo $index ? '' : 'am-in';?>">
                    <div class="am-panel-bd am-article">
                        <?=$item->content;?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>


    </div>

</div>