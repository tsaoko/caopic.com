<aside class="main-sidebar">

    <section class="sidebar">

        <?php

        $headerBars = [
            'system' => '系统',
            'people' => '会员管理',
            'content' => '内容',
            'config' => '配置',

        ];

        $slidebars = [
            'system' => [
                ['label' => '仪表盘','icon' => 'fa fa-tachometer', 'url' => ['site/index']],
            ],

            'people' => [
                ['label' => '用户列表','icon' => 'fa fa-user', 'url' => ['/user/index']],
                ['label' => '用户信息列表','icon' => 'fa fa-user', 'url' => ['/userinfo/index']],
                ['label' => '第三方账号列表','icon' => 'fa fa-user', 'url' => ['/useraccount/index']],
            ],

            'content' => [
                ['label' => '页面','icon' => 'fa fa-user', 'url' => ['page/index']],
                ['label' => '文章','icon' => 'fa fa-key', 'url' => ['post/index']],
            ],
            'config' => [
                ['label' => '自定义配置','icon' => 'fa fa-cog', 'url' => ['/setting']],

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
