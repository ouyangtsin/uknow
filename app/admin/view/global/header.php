<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{:get_setting('site_name')} - 后台管理</title>
    {include file="global/header_meta" /}
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed pace-primary text-sm">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav js_left_menu top-nav" id="topNav">
            {$_nav|raw}
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="{$user_info.avatar}" class="user-image">
                    <span class="d-none d-lg-block">{$user_info.name}</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="user-header">
                        <img src="{$user_info.avatar}" class="img-circle">
                        <h5>上次登录时间：{:date('Y-m-d H:i:s',$user_info.last_login_time)}</h5>
                        <h5>上次登录IP：{$user_info.last_login_ip}</h5>
                    </li>
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="{:url('admin.Admin/edit',['id'=>session('admin.id')])}" class="btn btn-default btn-flat">资料</a>
                        </div>
                        <div class="pull-right">
                            <a href="{:url('admin/Index/logout')}" class="btn btn-default btn-flat">退出</a>
                        </div>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle position-relative" data-toggle="dropdown" aria-expanded="false">
                    <i class="icon-bell"></i>
                    {if $notify_count}
                    <span class="admin-notify-count position-absolute badge badge-danger font-8">{$notify_count}</span>
                    {/if}
                </a>
                <ul class="dropdown-menu px-3">
                    {volist name="notify_list" id="v"}
                    <li><a href="{$v.url}">{$v.text}</a></li>
                    {/volist}
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="全屏">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link js_clear_cash" href="javascript:;" title="清空缓存">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/" target="_blank" title="前台首页">
                    <i class="fas fa-home"></i>
                </a>
            </li>
        </ul>
    </nav>