<div class="uk-header" id="uk-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-dark position-relative">
        <div class="container">
            <a class="navbar-brand uk-logo" href="{$baseUrl}">
                {if $setting.site_logo && !$setting.logo_type}
                <img src="{$setting.site_logo|default='/static/common/image/logo.png'}?rand={:time()}}">
                {else/}
                <span class="text-primary font-weight-bolder">{$setting.site_name}</span>
                {/if}
            </a>
            <button class="navbar-toggler  border-white" type="button" data-toggle="collapse" data-target="#uk-nav" aria-controls="uk-nav" aria-expanded="false" aria-label="菜单">
                <span class="icon-menu text-white"></span>
            </button>
            <div class="collapse navbar-collapse uk-nav position-relative" id="uk-nav">
                <ul class="uk-menu__nav flex-fill">
                    {volist name="nav_list" id="nav"}
                    {if $key<=2}
                    <li class="nav-item">
                        <a class="nav-link {if $nav['active']}active{/if}"  data-pjax="uk-wrap" href="{$nav['url_link']}">{if $nav['icon']}<i class="{$nav['icon']}"></i> {/if}{$nav['title']}</a>
                        {if isset($nav.childs)}
                        <ul class="uk-menu__nav__child">
                            {volist name="$nav['childs']" id="nav1"}
                            <li><a href="{:url($nav1['module'].'/'.$nav1['controller'].'/'.$nav1['action'])}">{$nav1['title']}</a></li>
                            {/volist}
                        </ul>
                        {/if}
                    </li>
                    {/if}
                    {/volist}

                    <li class="nav-item">
                        <a class="nav-link {if $nav['active']}active{/if}"  data-pjax="uk-wrap" href="javascript:;"><i class="icon-more-horizontal"></i> 更多</a>
                        <ul class="uk-menu__nav__child">
                            {volist name="nav_list" id="nav"}
                            {if $key>2}
                            <li><a href="{:url($nav['module'].'/'.$nav['controller'].'/'.$nav['action'])}">{$nav['title']}</a></li>
                            {/if}
                            {/volist}
                        </ul>
                    </li>
                </ul>
                <form class="form-inline my-2 ml-3" id="uk-global-search" method="get" action="{:url('search/index')}">
                    <input class="form-control-sm mr-sm-2 uk-search-input" value="{:input('keywords','')}"  name="keywords" type="text" placeholder="搜索视频、用户、问题、文章、商品" aria-label="搜索">
                    <button type="submit"><i class="icon-search1"></i></button>
                    <div class="search-dropdown" style="display: none"></div>
                </form>
                {if $user_id}
                <div class="uk-popover d-inline-block uk-top-publish position-relative ml-2">
                    <a href="javascript:;" class="popover-title btn btn-primary px-3 my-2 my-sm-0 uk-top-publish btn-sm">发表</a>
                    <div class="popover-content">
                        <div class="text-center d-block py-2" style="min-width: 100px">
                            <a href="{:url('ask/question/publish')}" class="dropdown-item">提问题</a>
                            <a href="{:url('ask/article/publish')}" class="dropdown-item">写文章</a>
                            {:hook('top_publish_btn')}
                        </div>
                    </div>
                </div>
                {/if}
                <div class="uk-user-nav navbar-right ml-5 text-right position-relative" style="right: 0">
                    {if !$user_id}
                    <a href="{:url('member/account/login')}" class="btn btn-primary btn-sm text-white px-3 nav-login">登录</a>
                    <span class="or">or</span>
                    <a href="{:url('member/account/register')}" class="btn btn-secondary text-white btn-sm nav-register px-3">注册</a>
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
                                <a href="{:get_user_url($user_id)}" class="dropdown-item"> <i class="uil-user"></i> 我的主页 </a>
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

{if $user_id && !$user_info['is_valid_email'] && $user_info['group_id']!=1 && $user_info['group_id']!=2}
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <div class="container text-center">
        <a href="javascript:;" class="uk-ajax-get" data-url="{:url('member/account/send_valid_mail')}">你的邮箱 {$user_info['email']} 还未验证, 点击这里重新发送验证邮件</a>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
{/if}