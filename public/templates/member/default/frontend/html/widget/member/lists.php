{if !empty($list)}
<div class="uk-common-list py-2">
	{volist name="list" id="v"}
	{switch name="$v['item_type']"}
	{case value="question" }
    <dl class="question">
        <dt class="mb-2">
            {if (!$v['answer_info'])}
            {if $v.is_anonymous}
            <a href="javascript:;" class="uk-username"  data-id="{$v['user_info']['uid']}">
                <img src="/static/common/image/default-avatar.svg" class="uk-user-img" alt="匿名用户">匿名用户
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" class="uk-user-name">
                <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
            </a>
            {/if}
            <span>发起了提问</span>
            <label class="float-right">{:date_friendly($v['create_time'])}</label>
            {else/}
            <a href="{$v['answer_info']['user_info']['url']}" class="uk-user-name">
                <img src="{$v['answer_info']['user_info']['avatar']}" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
            </a>
            <span>回复了问题（{$v['answer_count']}回复）</span>
            <label class="float-right">{:date_friendly($v['answer_info']['create_time'])}</label>
            {/if}
            {if $v['topics']}
            <div class="uk-tag d-inline">
                {volist name="$v['topics']" id="topic"}
                <a href="{:url('ask/topic/detail',['id'=>$topic['id']])}" class="uk-topic" data-id="{$topic.id}" target="_blank">{$topic.title}</a>
                {/volist}
            </div>
            {/if}
        </dt>
        <dd class="title">
            <p class="uk-one-line">
                {if $v.set_top}
                <i class="iconfont icon-zhiding text-warning font-12"></i>
                {/if}
                {if $v.question_type=='reward'}
                <i class="iconfont icon-shang text-danger font-12"></i>
                {/if}
                {if (!$v['answer_info'])}
                <a href="{:url('ask/question/detail',['id'=>$v['id']])}">{$v.title}</a>
                {else/}
                <a href="{:url('ask/question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}">{$v.title}</a>
                {/if}
            </p>
        </dd>
        <dd class="content my-2 uk-two-line">
            {$v.detail|raw}
        </dd>
        {if (!$v['answer_info'])}
        <div class="uk-common-footer">
            <label class="mr-2">
                <a href="javascript:;" class="btn btn-primary btn-sm px-3 mr-3 {if $v['has_focus']}active{/if}" data-toggle="popover" title="关注问题" onclick="UK.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? '已关注' : '关注问题'} <span class="focus-count">{$v.focus_count}</span></a>
            </label>
            <label class="mr-2"><i class="icon-eye"></i> {$v.view_count} 浏览</label>
            <label class="mr-2"><i class="icon-comment"></i> {$v['comment_count']} 评论</label>
        </div>
        {else/}
        <div class="uk-common-footer">
            <label class="mr-2">
                <a href="javascript:;" class="{$v['answer_info']['vote_value']==1 ? 'active' : ''}" data-toggle="popover" title="点赞回答" onclick="UK.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="icon-thumb_up"></i> 赞同 <span>{$v['answer_info']['agree_count']}</span></a>
            </label>
            <label class="mr-2">
                <a href="javascript:;" class="{$v['answer_info']['vote_value']==-1 ? 'active' : ''}" data-toggle="popover" title="点赞回答" onclick="UK.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="icon-thumb_down"></i> 反对</a>
            </label>
            <label class="mr-2"> {$v['answer_info']['thanks_count']} 人感谢</label>
            <label class="mr-2"><i class="icon-comment"></i> {$v['answer_info']['comment_count']} 评论</label>
        </div>
        {/if}
    </dl>
	{/case}

	{case value="article" }
    <dl class="article">
        {if $v['cover']}
        <dt class="col-sm-12 px-0">
            <img src="{$v['cover']|default='/static/common/image/default-cover.svg'}" class="rounded uk-cut-img" alt="{$v['title']}">
        </dt>
        {/if}
        <dd class="col-sm-12 m-0 px-0" {if !$v['cover']}style="width:100%"{/if}>
            <h2 class="uk-one-line">
                {if $v.set_top}
                <i class="iconfont icon-zhiding text-warning font-12"></i>
                {/if}
                <a href="{:url('ask/article/detail',['id'=>$v['id']])}">{$v['title']}</a>
            </h2>
            <div class="content uk-two-line">
                {$v.message|raw}
            </div>
            {if $v['topics']}
            <div class="uk-tag">
                {volist name="$v['topics']" id="topic"}
                <a href="{:url('ask/topic/detail',['id'=>$topic['id']])}" class="uk-topic" data-id="{$topic.id}" target="_blank">{$topic.title}</a>
                {/volist}
            </div>
            {/if}
            <div class="uk-common-footer">
                <a href="{$v['user_info']['url']}" class="uk-username avatar" data-id="{$v['user_info']['uid']}">
                    <img src="{$v['user_info']['avatar']}" alt="" class="uk-border-circle uk-user-img" style="width: 22px;height: 22px">
                </a>
                <a href="{$v['user_info']['url']}" class="uk-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a>
                <span> | {:date_friendly($v['create_time'])}</span>
                <div class="float-right">
                    <label><i class="icon-eye"></i> {$v['view_count']}</label>
                </div>
            </div>
        </dd>
        <div class="clear"></div>
    </dl>
	{/case}

	{default /}
	{/switch}
	{/volist}
</div>
{$page|raw}
{else/}
<p class="text-center py-3 text-muted">
    <img src="/static/common/image/empty.svg">
    <span class="d-block  ">暂无内容</span>
</p>
{/if}
