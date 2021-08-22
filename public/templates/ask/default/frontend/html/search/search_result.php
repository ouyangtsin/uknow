{if !empty($list)}
<div class="uk-common-list py-2">
    {volist name="$list" id="v"}
    {switch name="$v['search_type']"}
    {case value="question" }
    <dl class="question">
        <dt class="mb-2">
            {if (!$v['answer_info'])}
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
            {if (!$v['answer_info'])}
            {$v.detail|raw}
            {else/}
            <a href="{$v['answer_info']['user_info']['url']}" class="uk-user-name">{$v['answer_info']['user_info']['name']}</a> : {$v['answer_info']['content']|raw}
            {/if}
        </dd>
        {if (!$v['answer_info'])}
        <div class="uk-common-footer">
            <label class="mr-2">
                <a href="javascript:;" class="{$v['vote_value']==1 ? 'active' : ''}" data-toggle="popover" title="点赞问题" onclick="UK.User.agree(this,'question','{$v.id}');"><i class="icon-thumb_up"></i> 赞同 <span>{$v['agree_count']}</span></a>
            </label>
            <label class="mr-2"><i class="icon-eye"></i> {$v.view_count} 浏览</label>
            <label class="mr-2"><i></i> {$v.focus_count} 关注</label>
            <label class="mr-2"><i class="icon-comment"></i> {$v['comment_count']} 评论</label>
        </div>
        {else/}
        <div class="uk-common-footer">
            <label class="mr-2">
                <a href="javascript:;" class="{$v['answer_info']['vote_value']==1 ? 'active' : ''}" data-toggle="popover" title="点赞回答" onclick="UK.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="icon-thumb_up"></i> 赞同 <span>{$v['answer_info']['agree_count']}</span></a>
            </label>
            <label class="mr-2"><i class="icon-eye"></i> {$v.view_count} 浏览</label>
            <label class="mr-2"><i></i> {$v.focus_count} 关注</label>
            <label class="mr-2"><i class="icon-comment"></i> {$v['comment_count']} 评论</label>
        </div>
        {/if}
    </dl>
    {/case}

    {case value="article" }
    <dl class="article">
        <dt class="col-sm-12">
            <a href="{:url('article/detail',['id'=>$v['id']])}">
                <img src="{$v['cover']|default='/static/common/image/default-cover.svg'}" alt="{$v['title']}">
            </a>
        </dt>
        <dd class="col-sm-12">
            <h2>
                <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']}</a>
            </h2>
            <div class="content uk-two-line">
                {$v.message|raw}
            </div>
            {if $v['topics']}
            <div class="uk-tag">
                {volist name="$v['topics']" id="topic"}
                <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                {/volist}
            </div>
            {/if}
            <div class="uk-common-footer">
                <a href="{$v['user_info']['url']}" class="uk-user-name avatar">
                    <img src="{$v['user_info']['avatar']}" alt="" class="uk-border-circle" style="width: 22px;height: 22px">
                </a>
                <a href="{$v['user_info']['url']}" class="uk-user-name name">{$v['user_info']['name']}</a>
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
{/if}