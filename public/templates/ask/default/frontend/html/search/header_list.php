{if $list}
{volist name="list" id="v"}
{switch name="$v['search_type']"}
{case value="question" }
<div class="content-item question-item">
    <div class="uk-overflow-hidden question-top">
        <h3 class="mb-2 content-item-title uk-one-line"><a href="{:url('question/detail',['id'=>$v['id']])}" >{$v.title}</a></h3>
        <a href="{:url('question/detail',['id'=>$v['id']])}#uk-answer-editor" class="write-answer" data-uk-tooltip="回答问题">写回答</a>
    </div>
</div>
{/case}

{case value="article" }
<div class="content-item article-item">
    <div class="uk-overflow-hidden article-top">
        <div class="uk-float-left vote-info">
            <ul>
                <li class="uk-ajax-agree agree mb-1 {if $v['vote_value']==1}active{/if}" data-type="article" data-id="{$v.id}">
                    <a href="javascript:;" class="uk-text-center">
                        <i class="icon-thumb_up"></i>
                        <span>{$v['agree_count']}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="uk-float-left main-content{if !$user_id} no-user {/if}">
            <h3 class="mb-2 content-item-title uk-one-line"><a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']}</a></h3>
            <div class="user-info">
                <a href="{$v['user_info']['url']}">
                    <img src="{$v['user_info']['avatar']}" alt="" class="uk-border-circle" height="22" width="22">
                </a>
                <a href="{$v['user_info']['url']}" data-id="{$v.uid}" class="uk-username">{$v['user_info']['name']}</a>
                <span class="mr-2 ml-2">{$v.view_count} 浏览</span>
                <span class="mr-2">{$v['comment_count']} 条评论</span>
                <span>{:date_friendly($v['create_time'])}</span>
            </div>
        </div>
        {if $user_id}
        <div class="uk-float-right more-info">
            <a href="javascript:;" class="uk-float-right" data-uk-tooltip="更多"><i class="icon-more_horiz"></i></a>
            <div class="mt-0 p-2 uk-dropdown uk-dropdown-bottom-right" data-uk-dropdown="pos: bottom-right;mode:click " style="top: 60px;min-width: 120px">
                <ul class="uk-nav uk-dropdown-nav uk-text-center">
                    <li class="uk-report-button" data-type="article" data-id="{$v.id}"><a href="javascript:;">举报文章</a> </li>
                    <li class="uk-favorite-button" data-type="article" data-id="{$v.id}"><a href="javascript:;">收藏文章 </a></li>
                    <li>
                        <a href="javascript:;">分享文章 </a>
                        <div class="uk-dropdown uk-article-share" data-uk-dropdown="pos: right-top;mode:hover" style="min-width: 150px">
                            <div class="uk-nav uk-dropdown-nav uk-text-center">
                                <dl class="uk-share" data-type="qq" data-title="{$v['title']}" data-url="{:url('article/detail',['id'=>$v['id']])}" data-description="{:str_cut(strip_tags($v['message']),0,100)}">
                                    <dt><i class="icon-brand-qq"></i></dt>
                                    <dd><span>分享到QQ</span></dd>
                                </dl>
                                <dl class="uk-share" data-type="weibo"  data-title="{$v['title']}" data-url="{:url('article/detail',['id'=>$v['id']])}" data-description="{:str_cut(strip_tags($v['message']),0,100)}">
                                    <dt><i class="icon-brand-weibo"></i></dt>
                                    <dd><span>分享到微博</span></dd>
                                </dl>

                                <dl class="uk-share-wechat">
                                    <dt><i class="icon-brand-weixin"></i></dt>
                                    <dd><span>分享到微信</span></dd>
                                </dl>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        {/if}
    </div>
    <div class="article-info">
        <div class="uk-text-description uk-three-line {if condition="$v['cover']"} has-img {/if}">{$v.message|raw}</div>
    {if condition="$v['cover']"}
    <div class="article-img">
        <a href="{:url('article/detail',['id'=>$v['id']])}"><img src="{$v['cover']}" alt="{$v['title']}"></a>
    </div>
    {/if}
</div>
<hr class="my-4">
</div>
{/case}
{case value="users" }
<div class="uk-user-item">
    <div class="friend-card">
        <div class="uk-width-auto">
            <a href="{$v.url}"><img src="{$v.avatar}" alt="{$v.name}"></a>
            <span class="{$v['is_online'] ? 'online-dot' : 'offline-dot'}"></span>
        </div>
        <div class="uk-width-expand">
            <h3><a href="{$v.url}">{$v.name}</a></h3>
            <p>{$v['signature']|default='这家伙还没有留下自我介绍～'}</p>
        </div>
        <div class="uk-width-auto">
            <a href="javascript:;" class="uk-focus-button mr-3" data-type="user" data-id="{$v.uid}">关注</a>
            <a href="javascript:;" class="uk-send-inbox button small">私信</a>
        </div>
    </div>
</div>
{/case}
{default /}
{/switch}
{/volist}
{/if}