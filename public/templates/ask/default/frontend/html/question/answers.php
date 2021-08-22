{if !empty($list)}
<link rel="stylesheet" href="/static/share/css/share.min.css?v={$version|default='1.0.0'}">
<script src="/static/share/js/social-share.min.js?v={$version|default='1.0.0'}"></script>
{volist name="list" id="v"}
<div class="uk-answer-item p-3 mb-1 bg-white" id="question-answer-{$v.id}" data-answer-id="{$v.id}" data-count="{$v.comment_count}">
    <div class="user-details-card pt-0 pb-2">
        <div class="user-details-card-avatar" style="position: relative">
            {if $v.is_anonymous}
            <a href="javascript:;">
                <img src="/static/common/image/default-avatar.svg" alt="匿名用户" data-toggle="popover" title="匿名用户" style="width: 40px;height: 40px">
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" class="uk-username" data-id="{$v.uid}" data-toggle="popover" title="{$v['user_info']['name']}">
                <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
            {/if}
        </div>
        <div class="user-details-card-name">
            {if $v.is_anonymous}<a href="javascript:;" data-toggle="popover" title="匿名用户">匿名用户</a>{else/}<a href="{$v['user_info']['url']}" data-toggle="popover" title="{$v['user_info']['name']}">{$v['user_info']['name']}</a>{/if} <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
        </div>
        {if $v['is_best']}
        <div class="uk-answer-best">
            <i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>
        </div>
        {/if}
    </div>
    <p>{:html_entity_decode($v.content)}</p>
    <div class="answer-btn-actions mt-3">
        <label class="mr-3 ">
            <a href="javascript:;" class="{if $v['vote_value']==1}active{/if}"  onclick="UK.User.agree(this,'answer','{$v.id}');">
                <i class="icon-thumb_up"></i> 赞同 <span> {$v.agree_count}</span>
            </a>
        </label>
        <label class="mr-3">
            <a href="javascript:;" class="uk-ajax-open" data-title="评论回答" data-url="{:url('comment/answer?answer_id='.$v['id'])}">
                <i class="icon-comment"></i> 评论 <span> {$v.comment_count}</span>
            </a>
        </label>
        {if ($user_id && ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2))}
        <label class="mr-3">
            <a href="javascript:;"  class="uk-answer-editor" data-question-id="{$v.question_id}" data-answer-id="{$v['id']}" data-toggle="popover" title="编辑回答">
                <i class="icon-edit"></i> 编辑
            </a>
        </label>
        {/if}
        {if ($user_id && ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2))}
        <label class="mr-3">
            <a href="javascript:;" data-toggle="popover" title="删除回答" class="uk-ajax-get" data-confirm="是否删除该回答?" data-url="{:url('question/delete_answer?answer_id='.$v['id'])}">
                <i class="icon-delete"></i> 删除
            </a>
        </label>
        {/if}
        {if ($user_id && ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)  && !$v['is_best'] && !$best_answer_count)}
        <label class="mr-3">
            <a href="javascript:;"  class="uk-ajax-get" data-confirm="是否把该回答设为最佳?" data-url="{:url('question/set_answer_best?answer_id='.$v['id'])}">
                <i class="icon-check"></i> 最佳
            </a>
        </label>
        {/if}
        <!--问题回答操作栏钩子-->
        {:hook('question_answer_bottom_action',$v)}
        <div class="uk-share clearfix d-inline-block">
            <div class="social-share" data-disabled="google,twitter,facebook,linkedin,douban"></div>
        </div>
    </div>
</div>
{/volist}
{else/}
<p class="text-center text-muted p-3 bg-white">
    <img src="/static/common/image/empty.svg">
    <span class="d-block">暂无回答</span>
</p>
{/if}
