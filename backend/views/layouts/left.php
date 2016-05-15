<aside class="main-sidebar">

    <section class="sidebar">

        <?php

        $headerBars = [
            'system' => '系统',
            'content' => '内容',
            'product' => '产品',
            'structure' => '结构',
            'config' => '配置',
            'people' => '用户'
        ];

        $slidebars = [
            'system' => [
                ['label' => '控制面板','icon' => 'fa fa-user', 'url' => ['site/index']],
            ],
            'content' => [
                ['label' => '页面','icon' => 'fa fa-user', 'url' => ['page/index']],
                ['label' => '文章','icon' => 'fa fa-key', 'url' => ['post/index']],
            ],
            'product' => [
                ['label' => '产品','icon' => 'fa fa-user', 'url' => ['product/index']],
                ['label' => '订单','icon' => 'fa fa-key', 'url' => ['order/index']],
            ],
            'structure' => [
                ['label' => '幻灯片','icon' => 'fa fa-key', 'url' => ['slide/index']],
                ['label' => '菜单','icon' => 'fa fa-key', 'url' => ['menu/index','menu-group'=>'structure']],
            ],
            'config' => [
                ['label' => '自定义配置','icon' => 'fa fa-cog', 'url' => ['/setting']],

            ],
            'module' => [
                ['label' => '模块管理','icon' => 'fa fa-user', 'url' => ['page/index']],
                ['label' => '灯片管理','icon' => 'fa fa-key', 'url' => ['post/index']],
            ],
            'people' => [
                ['label' => '用户','icon' => 'fa fa-key', 'url' => ['/wx-user/index']],
            ],

        ];



        foreach($headerBars as $key => $val):
        ?>
        <ul class="sidebar-menu"><li class="header"><span><?=$val;?></span></li></ul>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $slidebars[$key]
            ]
        ) ?>

        <?php
        endforeach;
        ?>


    </section>

</aside>
