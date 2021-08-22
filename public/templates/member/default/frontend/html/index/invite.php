<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input(get_setting('show_url'))])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="bg-white mb-2 rounded p-3">
                    <div class="d-flex text-center">
                        <dl class="flex-fill mb-0">
                            <dd>已使用邀请码</dd>
                            <dt>{$invite_user_count}</dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>可用邀请码</dd>
                            <dt>{$user_info.available_invite_count - $invite_user_count}</dt>
                        </dl>
                    </div>
                </div>

                <div class="bg-white">
                    <div class="uk-nav-container py-2 px-3">
                        <ul >
                            <li class="{if $type=='code'}active{/if} mr-3"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/index//invite',['type'=>'code'])}">邀请码</a></li>
                            <li class="{if $type=='user'}active{/if} mr-3"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/index//invite',['type'=>'user'])}">邀请列表</a></li>
                        </ul>
                    </div>
                    <div id="uk-index-main" class="px-3">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>