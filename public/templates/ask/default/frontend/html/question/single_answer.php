{if $update}
<div class="user-details-card pt-0 pb-2">
    <div class="user-details-card-avatar" style="position: relative">
        {if $info.is_anonymous}
        <a href="javascript:;">
            <img src="/static/common/image/default-avatar.svg" alt="匿名用户" data-toggle="popover" title="匿名用户" style="width: 40px;height: 40px">
        </a>
        {else/}
        <a href="{$info['user_info']['url']}" class="uk-username" data-id="{$info.uid}" data-toggle="popover" title="{$info['user_info']['name']}">
            <img src="{$info['user_info']['avatar']}" alt="{$info['user_info']['name']}" style="width: 40px;height: 40px">
        </a>
        {/if}
    </div>
    <div class="user-details-card-name">
        {if $info.is_anonymous}<a href="javascript:;" data-toggle="popover" title="匿名用户">匿名用户</a>{else/}<a href="{$info['user_info']['url']}" data-toggle="popover" title="{$info['user_info']['name']}">{$info['user_info']['name']}</a>{/if} <span class="ml-0"> {:date('Y-m-d H:i',$info['create_time'])} </span>
    </div>
    {if $info['is_best']}
    <div class="uk-answer-best">
        <i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>
    </div>
    {/if}
</div>
<div class="uk-content">
    <div class="uk-answer-content overflow-hidden">
        {:html_entity_decode($info.content)}
    </div>
    {if $info.content}
    <div class="uk-answer-show uk-alpha-hidden" style="display: none">
        <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> 阅读全文</span>
    </div>
    <div class="uk-answer-hide uk-alpha-hidden mt-3" style="display: none;background:none;position: inherit;height: auto">
        <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> 收起全文</span>
    </div>
    {/if}
</div>
<div class="answer-btn-actions mt-3">
    <label class="mr-1">
        <a href="javascript:;" class="uk-ajax-agree {if $info['vote_value']==1}active{/if}"  onclick="UK.User.agree(this,'answer','{$info.id}');">
            <i class="icon-thumb_up"></i> 赞同 <span> {$info.agree_count}</span>
        </a>
    </label>

    <label class="mr-3 ">
        <a href="javascript:;" class="uk-ajax-against {if $info['vote_value']==-1}active{/if}"  onclick="UK.User.against(this,'answer','{{$info.id}}');">
            <i class="icon-thumb_down"></i>
        </a>
    </label>

    <label class="mr-3">
        <a href="javascript:;" class="uk-ajax-open" data-title="评论回答" data-url="{:url('comment/answer?answer_id='.$info['id'])}">
            <i class="icon-chat"></i> {$info.comment_count ? $info.comment_count.'条' : '添加'}评论
        </a>
    </label>

    {if $user_id}
    <label class="mr-3">
        <a href="javascript:;" onclick="UK.User.report(this,'answer','{$info.id}')" ><i class="icon-warning"></i> 举报</a>
    </label>

    <label class="mr-3">
        <a href="javascript:;" onclick="UK.User.favorite(this,'answer','{$info.id}')"><i class="icon-turned_in"></i> 收藏</a>
    </label>

    <label class="mr-3">
        <a href="javascript:;"  {if $info.has_thanks} class="active" {else/}onclick="UK.User.thanks(this,'{$info.id}')"{/if}>
        <i class="icon-favorite"></i> <span>{$info.has_thanks ? '已喜欢' : '喜欢'}</span>
        </a>
    </label>

    {if !$info.has_uninterested}
    <label class="mr-3">
        <a href="javascript:;" onclick="UK.User.uninterested(this,'answer','{$info.id}')">
            <i class="icon-report"></i> 不感兴趣
        </a>
    </label>
    {/if}
    {/if}
    {if $user_id && $setting.reward_enable}
    <label>
        <a href="javascript:;" data-title="打赏回答" class="mr-3 uk-ajax-open" data-url="">
            <i class="iconfont icon-shang font-9"></i> 打赏
        </a>
    </label>
    {/if}

    {if ($user_id && ($info['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)  && !$info['is_best'] && !$best_answer_count)}
    <label class="mr-3">
        <a href="javascript:;"  class="uk-ajax-get" data-confirm="是否把该回答设为最佳?" data-url="{:url('question/set_answer_best?answer_id='.$info['id'])}">
            <i class="iconfont">&#xe6f7;</i> 最佳
        </a>
    </label>
    {/if}
    <div class="mr-3 uk-popover d-inline-block">
        <a href="javascript:;" class="popover-title">
            <i class="icon-share"></i> 分享
        </a>
        <div class="popover-content">
            <div class="text-left d-block py-2" style="min-width: 100px">
                <a href="javascript:;"  class="dropdown-item uk-clipboard" data-clipboard-text="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="iconfont icon-weibo text-warning"></i> 新浪微博</a>
                <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="iconfont icon-QQ text-primary"></i> 腾讯空间</a>
                <div class="uk-qrcode-container" data-share="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}">
                    <a href="javascript:;" class="dropdown-item "><i class="iconfont icon-weixin text-success"></i> 微信扫一扫</a>
                    <div class="uk-qrcode text-center py-2"></div>
                </div>
            </div>
        </div>
    </div>
    <!--问题回答操作栏钩子-->
    {:hook('question_answer_bottom_action',$info)}
    <div class="uk-share clearfix d-inline-block">
        <div class="social-share" data-disabled="google,twitter,facebook,linkedin,douban"></div>
    </div>
    {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$info['uid'])}
    <div class="mr-3 uk-popover d-inline-block">
        <a href="javascript:;" class="popover-title">
            <i class="icon-more-horizontal"></i>
        </a>
        <div class="popover-content">
            <div class="text-center d-block py-2" style="min-width: 100px">
                <a href="javascript:;"  class="dropdown-item uk-answer-editor" data-question-id="{$info.question_id}" data-answer-id="{$info['id']}">编辑</a>
                <a href="javascript:;" data-toggle="popover" title="删除回答" class="dropdown-item uk-ajax-get" data-confirm="是否删除该回答?" data-url="{:url('question/delete_answer?answer_id='.$info['id'])}">删除</a>
            </div>
        </div>
    </div>
    {/if}
</div>
{else/}
<div class="uk-answer-item p-3 mb-1 bg-white" id="question-answer-{$info.id}" data-answer-id="{$info.id}">
    <div class="user-details-card pt-0 pb-2">
        <div class="user-details-card-avatar" style="position: relative">
            {if $info.is_anonymous}
            <a href="javascript:;">
                <img src="/static/common/image/default-avatar.svg" alt="匿名用户" data-toggle="popover" title="匿名用户" style="width: 40px;height: 40px">
            </a>
            {else/}
            <a href="{$info['user_info']['url']}" class="uk-username" data-id="{$info.uid}" data-toggle="popover" title="{$info['user_info']['name']}">
                <img src="{$info['user_info']['avatar']}" alt="{$info['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
            {/if}
        </div>
        <div class="user-details-card-name">
            {if $info.is_anonymous}<a href="javascript:;" data-toggle="popover" title="匿名用户">匿名用户</a>{else/}<a href="{$info['user_info']['url']}" data-toggle="popover" title="{$info['user_info']['name']}">{$info['user_info']['name']}</a>{/if} <span class="ml-0"> {:date('Y-m-d H:i',$info['create_time'])} </span>
        </div>
        {if $info['is_best']}
        <div class="uk-answer-best">
            <i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>
        </div>
        {/if}
    </div>
    <div class="uk-content">
        <div class="uk-answer-content overflow-hidden">
            {:html_entity_decode($info.content)}
        </div>
        {if $info.content}
        <div class="uk-answer-show uk-alpha-hidden" style="display: none">
            <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> 阅读全文</span>
        </div>
        <div class="uk-answer-hide uk-alpha-hidden mt-3" style="display: none;background:none;position: inherit;height: auto">
            <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> 收起全文</span>
        </div>
        {/if}
    </div>
    <div class="answer-btn-actions mt-3">
        <label class="mr-1">
            <a href="javascript:;" class="uk-ajax-agree {if $info['vote_value']==1}active{/if}"  onclick="UK.User.agree(this,'answer','{$info.id}');">
                <i class="icon-thumb_up"></i> 赞同 <span> {$info.agree_count}</span>
            </a>
        </label>

        <label class="mr-3 ">
            <a href="javascript:;" class="uk-ajax-against {if $info['vote_value']==-1}active{/if}"  onclick="UK.User.against(this,'answer','{{$info.id}}');">
                <i class="icon-thumb_down"></i>
            </a>
        </label>

        <label class="mr-3">
            <a href="javascript:;" class="uk-ajax-open" data-title="评论回答" data-url="{:url('comment/answer?answer_id='.$info['id'])}">
                <i class="icon-chat"></i> {$info.comment_count ? $info.comment_count.'条' : '添加'}评论
            </a>
        </label>

        {if $user_id}
        <label class="mr-3">
            <a href="javascript:;" onclick="UK.User.report(this,'answer','{$info.id}')" ><i class="icon-warning"></i> 举报</a>
        </label>

        <label class="mr-3">
            <a href="javascript:;" onclick="UK.User.favorite(this,'answer','{$info.id}')"><i class="icon-turned_in"></i> 收藏</a>
        </label>

        <label class="mr-3">
            <a href="javascript:;"  {if $info.has_thanks} class="active" {else/}onclick="UK.User.thanks(this,'{$info.id}')"{/if}>
            <i class="icon-favorite"></i> <span>{$info.has_thanks ? '已喜欢' : '喜欢'}</span>
            </a>
        </label>

        {if !$info.has_uninterested}
        <label class="mr-3">
            <a href="javascript:;" onclick="UK.User.uninterested(this,'answer','{$info.id}')">
                <i class="icon-report"></i> 不感兴趣
            </a>
        </label>
        {/if}
        {/if}
        {if $user_id && $setting.reward_enable}
        <label>
            <a href="javascript:;" data-title="打赏回答" class="mr-3 uk-ajax-open" data-url="">
                <i class="iconfont icon-shang font-9"></i> 打赏
            </a>
        </label>
        {/if}

        {if ($user_id && ($info['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)  && !$info['is_best'] && !$best_answer_count)}
        <label class="mr-3">
            <a href="javascript:;"  class="uk-ajax-get" data-confirm="是否把该回答设为最佳?" data-url="{:url('question/set_answer_best?answer_id='.$info['id'])}">
                <i class="iconfont">&#xe6f7;</i> 最佳
            </a>
        </label>
        {/if}
        <div class="mr-3 uk-popover d-inline-block">
            <a href="javascript:;" class="popover-title">
                <i class="icon-share"></i> 分享
            </a>
            <div class="popover-content">
                <div class="text-left d-block py-2" style="min-width: 100px">
                    <a href="javascript:;"  class="dropdown-item uk-clipboard" data-clipboard-text="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                    <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="iconfont icon-weibo text-warning"></i> 新浪微博</a>
                    <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="iconfont icon-QQ text-primary"></i> 腾讯空间</a>
                    <div class="uk-qrcode-container" data-share="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}">
                        <a href="javascript:;" class="dropdown-item "><i class="iconfont icon-weixin text-success"></i> 微信扫一扫</a>
                        <div class="uk-qrcode text-center py-2"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--问题回答操作栏钩子-->
        {:hook('question_answer_bottom_action',$info)}
        <div class="uk-share clearfix d-inline-block">
            <div class="social-share" data-disabled="google,twitter,facebook,linkedin,douban"></div>
        </div>
        {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$info['uid'])}
        <div class="mr-3 uk-popover d-inline-block">
            <a href="javascript:;" class="popover-title">
                <i class="icon-more-horizontal"></i>
            </a>
            <div class="popover-content">
                <div class="text-center d-block py-2" style="min-width: 100px">
                    <a href="javascript:;"  class="dropdown-item uk-answer-editor" data-question-id="{$info.question_id}" data-answer-id="{$info['id']}">编辑</a>
                    <a href="javascript:;" data-toggle="popover" title="删除回答" class="dropdown-item uk-ajax-get" data-confirm="是否删除该回答?" data-url="{:url('question/delete_answer?answer_id='.$info['id'])}">删除</a>
                </div>
            </div>
        </div>
        {/if}
    </div>
</div>
{/if}