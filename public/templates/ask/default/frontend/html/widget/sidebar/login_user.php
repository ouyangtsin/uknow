{if $user_id}
<div class="uk-card uk-card-default uk-card-small uk-padding-small mb-3">
    <div class="sidebar-user pt-0">
        <dl class="uk-overflow-hidden">
            <dt class="uk-float-left mr-3">
                <a href="{$user_info.url}"><img src="{$user_info.avatar}" alt="{$user_info.name}" class="circle" style="width: 48px;height: 48px"></a>
            </dt>
            <dd class="uk-float-left">
                <a href="{$user_info.url}">{$user_info.name}</a>
                <p class="uk-text-meta">{$user_info.signature|default="还没有完善签名哦..."}</p>
            </dd>
        </dl>
    </div>
    <ul class="sidebar-user-list">
        <li>
            <a href="{:url('member/index/explore',['uid'=>$user_info['uid'],'type'=>'question'])}">
                <p><i class="icon-help-with-circle"></i>我的提问</p>
                <em>{$user_info.question_count}</em>
            </a>
        </li>
        <li>
            <a href="{:url('member/index/explore',['uid'=>$user_info['uid'],'type'=>'article'])}">
                <p><i class="icon-assignment"></i>我的文章</p>
                <em>{$user_info.article_count}</em>
            </a>
        </li>
        <li>
            <a href="{:url('member/index/explore',['uid'=>$user_info['uid'],'type'=>'answer'])}">
                <p><i class="icon-insert_comment"></i>我的回答</p>
                <em>{$user_info.answer_count}</em>
            </a>
        </li>
    </ul>
</div>
{else/}
<div class="uk-card uk-card-default uk-card-small uk-card-body mb-3 rounded" data-uk-sticky="offset:70 ; media : @m">
    <h3 class="mb-3">账号登录</h3>
    <p>{:get_setting('site_description')}</p>
    <a href="{:url('member/account/login')}" class="button primary uk-display-block mt-3">登录</a>
    <a class="mt-3 button default uk-display-block" href="{:url('member/account/register')}">注册</a>
</div>
{/if}