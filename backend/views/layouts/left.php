<aside class="main-sidebar">

    <section class="sidebar">

        <?php
        $slidebars = [
            ['label' => '仪表盘','icon' => 'fa fa-tachometer', 'url' => ['site/index']],

            [
                'label' => '用户管理',
                'icon' => 'fa fa-users',
                'url' => ['/user/index'],
                'items' => [
                    ['label' => '用户列表','icon' => 'fa fa-users', 'url' => ['/user/index'],'active'=>$this->context->id == 'user'],
                    ['label' => '用户信息列表','icon' => 'fa fa-file-text-o', 'url' => ['/userinfo/index'],'active'=>$this->context->id == 'userinfo'],
                    ['label' => '第三方账号列表','icon' => 'fa fa-list-ol', 'url' => ['/useraccount/index'],'active'=>$this->context->id == 'useraccount'],
                    ['label' => '用户头像列表','icon' => 'fa fa-image', 'url' => ['/useravatar/index'],'active'=>$this->context->id == 'useravatar'],
                ]
            ],
            ['label' => '资源列表','icon' => 'fa fa-cube', 'url' => ['/resource/index']],
            [
                'label' => '分享管理',
                'icon' => 'fa fa-share-alt',
                'url' => ['/share/index'],
                'items' => [
                    ['label' => '分享列表','icon' => 'fa fa-share-alt', 'url' => ['/share/index'],'active'=>$this->context->id == 'share'],
                    ['label' => '分享分组列表','icon' => 'fa fa-object-group', 'url' => ['/sharegroup/index'],'active'=>$this->context->id == 'sharegroup'],
                    ['label' => '分享标签列表','icon' => 'fa fa-tags', 'url' => ['/sharetags/index'],'active'=>$this->context->id == 'sharetags'],
                    ['label' => '优质站点列表','icon' => 'fa fa-sitemap', 'url' => ['/website/index'],'active'=>$this->context->id == 'website'],
                ]
            ],
        ];




        echo dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => $slidebars
            ]
        );
        ?>


    </section>

</aside>
