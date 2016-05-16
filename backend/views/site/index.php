<?php

/* @var $this yii\web\View */

$this->title = '仪表盘';

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-user"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">注册会员数（人）</span>
                <span class="info-box-number"><?=number_format($user_count,0)?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                </div>
          <span class="progress-description">

          </span>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-home"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">上传资源（条）</span>
                <span class="info-box-number"><?=$post_count;?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                </div>
          <span class="progress-description">

          </span>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">分享次数（条）</span>
                <span class="info-box-number"><?=$product_count;?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                </div>
          <span class="progress-description">

          </span>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box bg-red">
            <span class="info-box-icon"><i class="fa fa-comments-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">分组（条）</span>
                <span class="info-box-number"><?=$order_count;?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                </div>
          <span class="progress-description">
            已处理记录
          </span>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
</div>


<div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">最新订单</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>下单人名</th>
                        <th>
                            金额
                        </th>
                        <th>支付状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($orders as $item):?>
                    <tr>
                        <td><?=$item->id;?></td>
                        <td><?=$item->realname;?></td>
                        <td><?=$item->order_amount;?></td>
                        <td><?=$item->status;?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>

    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">最新注册会员</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div><!-- /.box-tools -->
            </div><!-- /.box-header -->
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>
                            微信ID
                        </th>
                        <th>注册时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($users as $item):?>
                    <tr>
                        <td><?=$item->id;?></td>
                        <td><?=$item->username;?></td>
                        <td><?=$item->wechat_id;?></td>
                        <td><?=date('Y-m-d H:i:s',$item->created_at);?></td>
                    </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div>
