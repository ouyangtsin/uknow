<div class="uk-nav-container clearfix bg-white px-3">
    <ul class="uk-pjax-tab">
        <li class="{if $thisAction=='profile'}active {/if}mr-3"><a data-pjax="uk-index-main" href="{:url('member/setting/profile')}">账号设置</a></li>
        <li class="{if $thisAction=='notify'}active {/if}mr-3"><a data-pjax="uk-index-main" href="{:url('member/setting/notify')}">消息通知</a></li>
        <li class="{if $thisAction=='security'}active {/if}mr-3"><a data-pjax="uk-index-main" href="{:url('member/setting/security')}">隐私设置</a></li>
        <li class="{if $thisAction=='openid'}active {/if}"><a data-pjax="uk-index-main" href="{:url('member/setting/openid')}">账号绑定</a></li>
        <li class="{if $thisAction=='verified'}active {/if}"><a data-pjax="uk-index-main" href="{:url('member/setting/verified')}">用户认证</a></li>
        <!--用户设置导航-->
        {:hook('user_setting_nav')}
    </ul>
</div>