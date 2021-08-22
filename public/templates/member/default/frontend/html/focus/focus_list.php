{if $list}
<div class="uk-common-list py-2">
{volist name="list" id="v"}
    {if $type=='question'}
    <dl class="question">
        <dt class="mb-2">
            {if $v.is_anonymous}
            <a href="javascript:;" class="uk-user-name">
                <img src="/static/common/image/default-avatar.svg" alt="匿名用户">匿名用户
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" class="uk-user-name">
                <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
            </a>
            {/if}
            <span>发起了提问</span>
            <label class="float-right">{:date_friendly($v['create_time'])}</label>
            {if $v['topics']}
            <div class="uk-tag d-inline">
                {volist name="$v['topics']" id="topic"}
                <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                {/volist}
            </div>
            {/if}
        </dt>
        <dd class="title">
            <p class="bold">
                <em class="ding"></em>
                <a href="{:url('question/detail',['id'=>$v['id']])}" class="uk-one-line">{$v.title}</a>
            </p>
        </dd>
        <dd class="content my-2 uk-two-line">
            {$v.detail|raw}
        </dd>
        <div class="uk-common-footer">
            <label class="mr-2">
                <a href="javascript:;" class="{$v['vote_value']==1 ? 'active' : ''}" data-toggle="popover" title="点赞问题" onclick="UK.User.agree(this,'question','{$v.id}');"><i class="icon-thumb_up"></i> 赞同 <span>{$v['agree_count']}</span></a>
            </label>
            <label class="mr-2"><i class="icon-eye"></i> {$v.view_count} 浏览</label>
            <label class="mr-2"><i></i> {$v.focus_count} 关注</label>
            <label class="mr-2"><i class="icon-comment"></i> {$v['comment_count']} 评论</label>
        </div>
    </dl>
    {/if}

    {if $type=='friend'}
    <div class="p-3 bg-white mb-1 border-bottom">
        <div class="overflow-hidden position-relative">
            <div class="float-left">
                <a href="{$v.url}" class="uk-username rounded d-block">
                    <img src="{$v.avatar}" alt="{$v.name}" width="80" height="80">
                </a>
                {if $v['is_online']}
                <span class="online-dot"></span>
                {else/}
                <span class="offline-dot"></span>
                {/if}
            </div>

            <div class="float-right" style="width: calc(100% - 95px)">
                <h3 class="mb-1">
                    <a href="{$v.url}" class="uk-username font-12">{$v.name}</a>
                    <img src="{$v.group_icon}" style="width: auto;height: 22px;border-radius:0" data-uk-tooltip="{$v.user_group_name}">
                </h3>
                <p class="text-muted font-9 uk-one-line">{$v['signature']|default='这家伙还没有留下自我介绍～'}</p>
                <div class="text-muted mt-1">
                    <label>积分:{$v.score}</label>
                </div>
            </div>

            {if $user_id && $v['uid']!=$user_id}
            <div class="position-absolute" style="right: 0;bottom: 0">
                <button class="btn btn-primary btn-sm px-3 {if $v.has_focus}active{/if} mr-3" onclick="UK.User.focus(this,'user','{$v.uid}')">{if $v.has_focus}已关注{else}关注{/if}</button>
                <button class="btn btn-outline-primary px-3 btn-sm" onclick="UK.User.inbox('{$v.user_name}')">私信</button>
            </div>
            {/if}
        </div>
    </div>
    {/if}

    {if $type=='fans'}
    <div class="p-3 bg-white mb-1 border-bottom">
        <div class="overflow-hidden position-relative">
            <div class="float-left">
                <a href="{$v.url}" class="uk-username rounded d-block">
                    <img src="{$v.avatar}" alt="{$v.name}" width="80" height="80">
                </a>
                {if $v['is_online']}
                <span class="online-dot"></span>
                {else/}
                <span class="offline-dot"></span>
                {/if}
            </div>

            <div class="float-right" style="width: calc(100% - 95px)">
                <h3 class="mb-1">
                    <a href="{$v.url}" class="uk-username font-12">{$v.name}</a>
                    <img src="{$v.group_icon}" style="width: auto;height: 22px;border-radius:0" data-uk-tooltip="{$v.user_group_name}">
                </h3>
                <p class="text-muted font-9 uk-one-line">{$v['signature']|default='这家伙还没有留下自我介绍～'}</p>
                <div class="text-muted mt-1">
                    <label>积分:{$v.score}</label>
                </div>
            </div>

            {if $user_id && $v['uid']!=$user_id}
            <div class="position-absolute" style="right: 0;bottom: 0">
                <button class="btn btn-primary btn-sm px-3 {if $v.has_focus}active{/if} mr-3" onclick="UK.User.focus(this,'user','{$v.uid}')">{if $v.has_focus}已关注{else}关注{/if}</button>
                <button class="btn btn-outline-primary px-3 btn-sm" onclick="UK.User.inbox('{$v.user_name}')">私信</button>
            </div>
            {/if}
        </div>
    </div>
    {/if}

    {if $type=='column'}
    <div class="p-3 bg-white border-bottom">
        <div class="mt-1">
            <h3 class="uk-one-line font-12"><a href="{:url('column/detail',['id'=>$v['id']])}">{$v.name}</a></h3>
            <p class="text-muted my-1 uk-two-line">{$v.description}</p>
            <a href="javascript:;" class="text-color-info mr-2 font-9">{$v.join_count|num2string} 用户</a>
            <a href="javascript:;" class="text-color-info mr-2 font-9">{$v.post_count|num2string} 内容 </a>
            <a href="javascript:;" class="text-color-info font-9">{$v.focus_count|num2string} 关注 </a>
        </div>
    </div>
    {/if}

    {if $type=='topic'}
        <div class="px-3 py-1 rounded">
            <dl class="overflow-hidden mb-0">
                <dt class="float-left">
                    <a href="{:url('topic/detail',['id'=>$v['id']])}">
                        <img src="{$v['pic']|default='/static/common/image/topic.svg'}" height="65" width="65">
                    </a>
                </dt>
                <dd class="float-right mb-0" style="width: calc(100% - 75px)">
                    <a href="{:url('topic/detail',['id'=>$v['id']])}">{$v.title}</a>
                    <p class="mb-0 font-9 uk-one-line text-muted">{$v.description}</p>
                    <p class="ont-9 text-color-info">
                        <span class="mr-2">{$v.discuss}个内容</span>
                        <span class="mr-2"><span>{$v.focus}</span>人关注</span>
                    </p>
                </dd>
            </dl>
        </div>
    {/if}
{/volist}
</div>
{/if}
