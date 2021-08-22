<ul class="sidebar-user-list bg-white p-3 uk-pjax-tab">
    <li class="{if $thisController=='manager' && $thisAction=='index'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/manager/index')}">
            <p><i class="icon-compass"></i>个人主页</p>
        </a>
    </li>
    <li class="{if $thisController=='focus'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/focus/index',['uid'=>$user['uid']])}">
            <p><i class="icon-heart"></i>{$user['uid']==$user_id ? '我的关注' : 'Ta的关注'}</p>
        </a>
    </li>
    {if $user['uid']==$user_id}
    <li class="{if $thisController=='notify' && $thisAction=='index'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/notify/index')}">
            <p><i class="icon-bell"></i>我的消息</p>
            <em>{$user['notify_unread']}</em>
        </a>
    </li>
    <li class="{if $thisController=='inbox' && $thisAction=='index'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/inbox/index')}">
            <p><i class="icon-typing"></i>我的私信</p>
            <em>{$user['inbox_unread']}</em>
        </a>
    </li>
    <li class="{if $thisController=='score' && $thisAction=='index'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/score/index')}">
            <p><i class="icon-database"></i>我的积分</p>
            <em>{:num2string($user['score'])}</em>
        </a>
    </li>
    <li class="{if $thisController=='favorite'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/favorite/index')}">
            <p><i class="icon-favorite"></i>我的收藏</p>
            <em>{:num2string($user.favorite_count)}</em>
        </a>
    </li>
    <li class="{if $thisController=='draft'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/draft/index')}">
            <p><i class="icon-drafts"></i>我的草稿</p>
            <em>{:num2string($user.draft_count)}</em>
        </a>
    </li>
    <li class="{if $thisController=='setting'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/setting/profile')}">
            <p><i class="icon-settings"></i>资料设置</p>
        </a>
    </li>
    {if $setting.register_type =='invite'}
    <li class="{if $thisController=='people' && $thisAction=='invite'}active{/if}">
        <a data-pjax="uk-center-main" href="{:url('member/index/invite')}">
            <p><i class="icon-attachment1"></i>邀请管理</p>
        </a>
    </li>
    {/if}
    {/if}
</ul>