<div class="uk-main-wrap mt-2">
    <div class="container px-0">
        <div class="uk-nav-container clearfix bg-white px-3 mx-1">
            <h2 class="float-left"><i class="icon-explore"></i> 大咖列表</h2>
            <ul class="float-right">
                <li class="{if $sort=='default'}active{/if}"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/index/lists',['sort'=>'default'])}"> 默认</a></li>
                <li class="{if $sort=='power'}active{/if}"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/index/lists',['sort'=>'power'])}"> {$setting.power_unit} </a></li>
                <li class="{if $sort=='score'}active{/if}"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/index/lists',['sort'=>'score'])}"> {$setting.score_unit} </a></li>
                <li class="{if $sort=='verify'}active{/if}"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/index/lists',['sort'=>'verify'])}"> 认证 </a></li>
            </ul>
        </div>
        <div id="uk-index-main">
            <div class="row no-gutters mt-2">
                {volist name="list" id="v"}
                <div class="col-md-3">
                    <div class="mx-1 p-3 bg-white my-1 rounded">
                        <div class="text-center">
                            <a href="{$v.url}" class="uk-username rounded d-block">
                                <img src="{$v.avatar}" alt="{$v.name}" width="80" height="80" style="border-radius: 50%">
                            </a>
                            {if $v['is_online']}
                            <span class="online-dot"></span>
                            {else/}
                            <span class="offline-dot"></span>
                            {/if}
                        </div>
                        <div class="text-center">
                            <h3 class="mb-1">
                                <a href="{$v.url}" class="uk-username font-12">{$v.name}</a>
                                <img src="{$v.group_icon}" style="width: auto;height: 22px;border-radius:0" data-uk-tooltip="{$v.user_group_name}">
                            </h3>
                            <p class="text-color-info font-9 uk-one-line">{$v['signature']|default='这家伙还没有留下自我介绍～'}</p>
                            <div class="text-color-info mt-1 font-9">
                                <label class="mr-2">{$setting.score_unit}: {$v.score}</label> |
                                <label class="mr-2">{$setting.power_unit}: {$v.power}</label> |
                                <label>获赞: {$v.agree_count}</label>
                            </div>
                        </div>
                        {if $user_id && $v['uid']!=$user_id}
                        <div class="d-flex">
                            <button class="flex-fill btn btn-primary btn-sm px-3 {if $v.has_focus}active{/if} mr-1" onclick="UK.User.focus(this,'user','{$v.uid}')">{if $v.has_focus}已关注{else}关注{/if}</button>
                            <button class="flex-fill btn btn-outline-primary px-3 btn-sm mx-1" onclick="UK.User.inbox('{$v.user_name}')">私信</button>
                        </div>
                        {else/}
                        <div class="d-flex">
                            <a href="{$v.url}" class="flex-fill btn btn-primary btn-sm px-3">查看详情</a>
                        </div>
                        {/if}
                    </div>
                </div>
                {/volist}
                {$page|raw}
            </div>
        </div>
    </div>
</div>