<aside class="main-sidebar">

    <section class="sidebar">

        <?php

        $headerBars = [
            'system' => '系统',
            'people' => '',
            'content' => '内容',
            'config' => '配置',

        ];

        $slidebars = [
            'system' => [
                ['label' => '仪表盘','icon' => 'fa fa-tachometer', 'url' => ['site/index']],
            ],

            'people' => [
                [
                    'label' => '用户管理',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/user/index'],
                    'items' => [
                        ['label' => '用户列表','icon' => 'fa fa-users', 'url' => ['/user/index']],
                        ['label' => '用户信息列表','icon' => 'fa fa-info-circle', 'url' => ['/userinfo/index']],
                        ['label' => '第三方账号列表','icon' => 'fa fa-user', 'url' => ['/useraccount/index']],
                        ['label' => '用户头像列表','icon' => 'fa fa-user', 'url' => ['/useravatar/index']],
                    ]
                ],
                [
                    'label' => '分享管理',
                    'icon' => 'fa fa-circle-o',
                    'url' => ['/share/index'],
                    'items' => [
                        ['label' => '分享列表','icon' => 'fa fa-users', 'url' => ['/share/index']],
                        ['label' => '优质站点列表','icon' => 'fa fa-info-circle', 'url' => ['/website/index']],
                    ]
                ],

            ],

            'content' => [

            ],


            'config' => [
                ['label' => '自定义配置','icon' => 'fa fa-cog', 'url' => ['/setting']],

            ],

        ];



foreach($headerBars as $key => $val):
        if($val):
        ?>
        <ul class="sidebar-menu">
            <li class="header"><span><?=$val;?></span></li></ul>
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $slidebars[$key]
            ]
        ) ?>
        <?php
    else:
        echo dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $slidebars[$key]
            ]
        );

    endif;
endforeach;
        ?>


    </section>

</aside>
