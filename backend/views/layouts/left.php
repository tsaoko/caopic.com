<aside class="main-sidebar">

    <section class="sidebar">

        <?php

        $headerBars = [
            'system' => '系统',
            'people' => '',
        ];

        $slidebars = [
            'system' => [
                ['label' => '仪表盘','icon' => 'fa fa-tachometer', 'url' => ['site/index']],
            ],

            'people' => [
                [
                    'label' => '用户管理',
                    'icon' => 'fa fa-users',
                    'url' => ['/user/index'],
                    'items' => [
                        ['label' => '用户列表','icon' => 'fa fa-users', 'url' => ['/user/index']],
                        ['label' => '用户信息列表','icon' => 'fa fa-file-text-o', 'url' => ['/userinfo/index']],
                        ['label' => '第三方账号列表','icon' => 'fa fa-list-ol', 'url' => ['/useraccount/index']],
                        ['label' => '用户头像列表','icon' => 'fa fa-image', 'url' => ['/useravatar/index']],
                    ]
                ],
                ['label' => '资源列表','icon' => 'fa fa-cube', 'url' => ['/resource/index']],
                [
                    'label' => '分享管理',
                    'icon' => 'fa fa-share-alt',
                    'url' => ['/share/index'],
                    'items' => [
                        ['label' => '分享列表','icon' => 'fa fa-share-alt', 'url' => ['/share/index']],
                        ['label' => '分享分组列表','icon' => 'fa fa-object-group', 'url' => ['/sharegroup/index']],
                        ['label' => '分享标签列表','icon' => 'fa fa-tags', 'url' => ['/sharetags/index']],
                        ['label' => '优质站点列表','icon' => 'fa fa-sitemap', 'url' => ['/website/index']],
                    ]
                ],

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
