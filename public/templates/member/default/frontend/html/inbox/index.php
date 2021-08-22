<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="uk-mod bg-white px-3 pt-3">
                    <div class="uk-mod-head mb-0">
                        <p class="mod-head-title">对话列表</p>
                        <div class="mod-head-more ">
                            <a href="{:url('member/setting/notify')}" class="text-muted"><i class="icon-settings"></i> 私信设置</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white mb-2 mt-1 rounded p-3">
                    {if isset($list)}
                    {volist name="list" id="v"}
                    <dl class="mb-0 py-3 border-bottom overflow-hidden position-relative">
                        <dt class="float-left">
                            <a href="{$v['user']['url']}">
                                <img src="{$v['user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
                            </a>
                            <span class="uk-online-status {$v['unread']  ? 'unread' : 'read'}"></span>
                        </dt>
                        <dd class="float-right" style="width: calc(100% - 61px)">
                            <p class="text-muted font-9">{$v['user']['name']} · {:date_friendly($v['update_time'])}</p>
                            <p class="uk-one-line cursor-pointer {$v['unread']  ? 'text-primary' : 'text-muted'}" onclick="UK.User.inbox('{$v.user.name}')">{:get_username($v['last_message_uid'])}:{$v['last_message']}</p>
                        </dd>
                        <div class="font-8 position-absolute" style="top: 1rem;right: 0">
                            <a href="javascript:;" class="text-primary" onclick="UK.User.inbox('{$v.user.name}')">共{$v.count}条对话</a>
                            <a href="javascript:;" onclick="deleteNotify(this,{$v.id})" class="ml-2 text-color-info"><i class="icon-delete"></i> 删除</a>
                        </div>
                    </dl>
                    {/volist}
                    {$page}
                    {else/}
                    <p class="text-center mt-4 text-meta">
                        <img src="/static/common/image/empty.svg" alt="暂无私信记录">
                        <span class="mt-3 d-block ">暂无私信记录</span>
                    </p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>