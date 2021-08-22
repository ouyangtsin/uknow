{if $_ajax}
{__CONTENT__}
{elseif $_ajax_open}
{include file="global/head" /}
<body>
<div class="uk-overflow-auto" style="max-height: 100vh !important;">
    {__CONTENT__}
    {include file="global/footer_meta" /}
</div>
</body>
</html>
{else}
{include file="global/head" /}
<body class="uk-white-theme">
    <div class="uk-header" id="uk-header">
        <nav class="navbar bg-dark position-relative">
            <div class="container">
                <a class="navbar-brand uk-logo" href="{$baseUrl}">
                    {if $setting.site_logo && !$setting.logo_type}
                    <img src="{$setting.site_logo|default='/static/common/image/logo.png'}?rand={:time()}}">
                    {else/}
                    <span class="text-primary font-weight-bolder">{$setting.site_name}</span>
                    {/if}
                    <span class="text-white">用户管理中心</span>
                </a>
                <div class="uk-nav position-relative" id="uk-nav">
                    <div class="uk-user-nav navbar-right ml-5 text-right position-relative" style="right: 0">
                        {if !$user_id}
                        <a href="{:url('member/account/login')}" class="btn btn-primary btn-sm mr-2 text-white px-3">登录</a>
                        <a href="{:url('member/account/register')}" class="btn btn-outline-primary text-primary btn-sm px-3">注册</a>
                        {else/}
                        <div class="uk-popover uk-header-notify d-inline-block mr-3 position-relative">
                            <a href="javascript:;" class="popover-title d-block ">
                                <i class="icon-bell font-14"></i>
                            </a>
                            {if $user_info['notify_unread']}
                            <span class="header-notify-count position-absolute">{$user_info['notify_unread']>100 ? '99+' : $user_info['notify_unread']}</span>
                            {/if}
                            <div class="popover-content">
                                <div class="text-center d-block py-2" style="min-width: 250px">
                                    <div class="header-notify-list">
                                        {if isset($notify_list) && $notify_list}
                                        {volist name="notify_list" id="v"}
                                        <div class="mb-0 py-2 px-3 header-inbox-item overflow-hidden position-relative cursor-pointer text-left">
                                            <p class="text-primary">{$v['subject']}</p>
                                            <p class="text-color-info font-9 mt-1 uk-two-line">{$v.message|raw}</p>
                                            <p class="font-8">
                                                {if !$v['read_flag']}
                                                <a href="javascript:;" onclick="UK.User.readNotify(this,{$v.id})" class="text-color-info">标记已读</a>
                                                {/if}
                                                <a href="javascript:;" onclick="UK.User.deleteNotify(this,{$v.id})" class="ml-2 text-color-info"><i class="icon-delete"></i> 删除</a>
                                            </p>
                                        </div>
                                        {/volist}
                                        {else/}
                                        <p class="p-3">暂无通知</p>
                                        {/if}
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <div class="font-9 text-center pt-2 px-3 clearfix border-top">
                                        <a href="javascript:;" onclick="UK.User.readAll();" class="float-left">全部已读</a>
                                        <a href="{:url('member/notify/index')}" class="float-right">查看全部通知</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-popover uk-header-inbox d-inline-block mr-4 position-relative">
                            <a href="javascript:;" class="popover-title d-block ">
                                <i class="icon-typing font-14"></i>
                            </a>
                            {if $user_info['inbox_unread']}
                            <span class="header-inbox-count position-absolute">{$user_info['inbox_unread']>100 ? '99+' : $user_info['inbox_unread']}</span>
                            {/if}
                            <div class="popover-content">
                                <div class="text-center d-block py-2" style="min-width: 250px">
                                    <div class="header-inbox-list">
                                        {if isset($inbox_list) && $inbox_list}
                                        {volist name="inbox_list" id="v"}
                                        <dl class="mb-0 p-2 header-inbox-item overflow-hidden position-relative cursor-pointer">
                                            <dt class="float-left">
                                                <a href="{$v['user']['url']}">
                                                    <img src="{$v['user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
                                                </a>
                                            </dt>
                                            <dd class="float-right mb-0" style="width: calc(100% - 56px)">
                                                <p class="text-muted text-left">{$v['user']['name']}</p>
                                                <p class="uk-one-line font-9 cursor-pointer text-left text-muted" onclick="UK.User.inbox('{$v.user.name}')">{:get_username($v['last_message_uid'])}:{$v['last_message']}</p>
                                            </dd>
                                        </dl>
                                        {/volist}
                                        {else/}
                                        <p class="p-3">暂无私信</p>
                                        {/if}
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <div class="font-9 text-center pt-2 px-3 border-top">
                                        <a href="{:url('member/inbox/index')}">查看全部私信</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-popover d-inline-block position-relative">
                            <a class="popover-title text-white" href="#" id="uk-user-nav-btn">
                                <img src="{$user_info.avatar}" class="uk-avatar mr-1" alt="{$user_info.user_name}"> {$user_info.nick_name}
                            </a>
                            {if $user_info['inbox_unread']}
                            <span class="header-inbox-count position-absolute">{$user_info['inbox_unread']>100 ? '99+' : $user_info['inbox_unread']}</span>
                            {/if}
                            <div class="popover-content">
                                <div class="text-center d-block py-2" style="min-width: 150px">
                                    <a href="{$user_info.url}" class="dropdown-item"> <i class="uil-user"></i> 我的主页 </a>
                                    <a href="{:url('member/manager/index')}" class="dropdown-item"> <i class="uil-thumbs-up"></i> 用户中心 </a>
                                    {if $user_info['group_id']==1 || $user_info['group_id']==2}
                                    <a href="/admin.php" target="_blank" class="dropdown-item"> <i class="uil-thumbs-up"></i> 管理后台 </a>
                                    {/if}
                                    <a href="{:url('member/setting/profile')}" class="dropdown-item"> <i class="uil-cog"></i> 账号设置</a>
                                    <a href="{:url('member/account/logout')}" class="dropdown-item"> <i class="uil-sign-out-alt"></i> 退出登陆</a>
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="uk-wrap" id="uk-wrap">
        {__CONTENT__}
    </div>
    {include file="global/footer" /}
    {include file="global/footer_meta" /}
    {$theme_config['footer_js']|raw|htmlspecialchars_decode}
</body>
</html>
{/if}