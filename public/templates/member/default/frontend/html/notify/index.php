<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="uk-mod bg-white px-3 pt-3">
                    <div class="uk-mod-head mb-0">
                        <p class="mod-head-title">通知中心</p>
                        <div class="mod-head-more ">
                            <a class="text-muted mr-4" href="javascript:;" onclick="UK.User.readAll()">全部已读</a>
                            <a href="{:url('member/setting/notify')}" class="text-muted"><i class="icon-settings"></i> 通知设置</a>
                        </div>
                    </div>
                </div>
                <div class="px-3 bg-white pb-3 uk-notify-pjax">
                    <a href="{:url('member/notify/index')}" data-pjax="uk-index-main" class="mt-3 btn btn-sm px-3 mx-1 {$type=='' ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">全部通知</a>
                    {volist name="notify" id="v"}
                    <a href="{:url('member/notify/index',['type'=>$key])}" data-pjax="uk-index-main" class="mt-3 btn btn-sm px-3 mx-1 {$type==$key ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">{$v.title}</a>
                    {/volist}
                </div>
                <div class="bg-white mb-2 mt-1 rounded p-3"  id="uk-index-main">
                    {if $list}
                    {volist name="list" id="v"}
                    <dl class="mb-0 py-3 border-bottom overflow-hidden position-relative">
                        <dt class="float-left">
                            {if $v['recipient_user']}
                            <a href="javascript:;" class="badge badge-warning text-white" style="width: 46px;height: 46px;line-height: 46px;">
                                <i class="icon-bell" style="font-size: 24px;line-height: 40px"></i>
                            </a>
                            {else/}
                            <a href="{$v['recipient_user']['url']}">
                                <img src="{$v['recipient_user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
                            </a>
                            {/if}
                            <span class="uk-online-status uk-notify-status {$v['read_flag'] ? 'read' : 'unread'}"></span>
                        </dt>
                        <dd class="float-right" style="width: calc(100% - 61px)">
                            <p class="text-muted font-9 {if !$v['read_flag']}font-weight-bold{/if}">{$v['subject']} · {:date_friendly($v['create_time'])}</p>
                            <p class="font-9 mt-1 text-color-info">{$v.content.message|default=''} · <a href="{$v.content.url|default=''}">{$v.content.title|default=''}</a></p>
                        </dd>
                        <div class="font-8 position-absolute" style="{$isMobile ? 'bottom: 0.5rem;right: 0' : 'top: 1rem;right: 0'}">
                            {if !$v['read_flag']}
                            <a href="javascript:;" onclick="UK.User.readNotify(this,{$v.id})" class="text-color-info">标记已读</a>
                            {/if}
                            <a href="javascript:;" onclick="UK.User.deleteNotify(this,{$v.id})" class="ml-2 text-color-info"><i class="icon-delete"></i> 删除</a>
                        </div>
                    </dl>
                    {/volist}
                    {$page|raw}

                    {else/}
                    <p class="text-center mt-4 text-meta">
                        <img src="/static/common/image/empty.svg" alt="暂无消息">
                        <span class="mt-3 d-block ">暂无消息</span>
                    </p>
                    {/if}
                </div>
			</div>
		</div>
	</div>
</div>