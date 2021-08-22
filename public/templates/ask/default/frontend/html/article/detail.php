<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="col-md-1 text-center d-xs-none actions">
                <label class="px-1 py-2 bg-white rounded d-block mb-2">
                    <a href="javascript:;" class="uk-ajax-agree {$article_info['vote_value']==1 ? 'active' : ''}" onclick="UK.User.agree(this,'article','{$article_info.id}');">
                        <i class="icon-thumb_up font-12"></i>
                        <span class="d-block">{$article_info['agree_count']}</span>
                    </a>
                </label>
                <div class="uk-popover px-1 py-2 bg-white rounded d-block mb-2 ">
                    <a href="javascript:;" class="popover-title" style="color: #76839b;">
                        <i class="icon-share"></i>
                        <span class="d-block">分享</span>
                    </a>
                    <div class="popover-content">
                        <div class="text-left d-block py-2" style="min-width: 100px">
                            <a href="javascript:;"  class="dropdown-item uk-clipboard" data-clipboard-text="{:url('ask/article/detail',['id'=>$article_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                            <a href="javascript:;" onclick="UK.User.share('{$article_info.title}','{:url('ask/article/detail',['id'=>$article_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="iconfont icon-weibo text-warning"></i> 新浪微博</a>
                            <a href="javascript:;" onclick="UK.User.share('{$article_info.title}','{:url('ask/article/detail',['id'=>$article_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="iconfont icon-QQ text-primary"></i> 腾讯空间</a>
                            <div class="uk-qrcode-container" data-share="{:url('ask/article/detail',['id'=>$article_info.id],true,true)}">
                                <a href="javascript:;" class="dropdown-item "><i class="iconfont icon-weixin text-success"></i> 微信扫一扫</a>
                                <div class="uk-qrcode text-center py-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {if $user_id}
                {if $user_id!=$article_info['uid']}
                <label class="px-1 py-2 bg-white rounded d-block">
                    <a href="javascript:;" class="uk-ajax-against {$article_info['vote_value']==-1 ? 'active' : ''}" onclick="UK.User.against(this,'article','{$article_info.id}');">
                        <i class="icon-thumb_down font-12"></i>
                        <span class="d-block">反对</span>
                    </a>
                </label>
                <label class="px-1 py-2 bg-white rounded d-block"  onclick="UK.User.report(this,'article','{$article_info.id}');">
                    <a href="javascript:;">
                        <i class="icon-warning font-12"></i>
                        <span class="d-block">{if $article_info['is_report']}已举报{else/}举报{/if}</span>
                    </a>
                </label>
                {/if}
                <label class="px-1 py-2 bg-white rounded d-block" onclick="UK.User.favorite(this,'article','{$article_info.id}');">
                    <a href="javascript:;">
                        <i class="icon-star-outlined font-12"></i>
                        <span class="d-block">{if $article_info['is_favorite']}已收藏{else/}收藏{/if}</span>
                    </a>
                </label>
                {/if}

                {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$article_info['uid'])}
                <div class="uk-popover px-1 py-2 bg-white rounded d-block">
                    <a href="javascript:;" class="popover-title d-block" style="color: #76839b;">
                        <i class="icon-more-horizontal"></i>
                        <span class="d-block">更多</span>
                    </a>
                    <div class="popover-content">
                        <div class="text-center d-block py-2" style="min-width: 100px">
                            {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}
                            <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ask/article/action',['type'=>'recommend','is_recommend'=>$article_info['is_recommend'],'article_id'=>$article_info['id']])}">
                                <span>{$article_info['is_recommend'] ? '取消推荐' : '推荐文章'}</span>
                            </a>
                            <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ask/article/action',['type'=>'set_top','set_top'=>$article_info['set_top'],'article_id'=>$article_info['id']])}">
                                <span>{$article_info['set_top'] ? '取消置顶' : '置顶文章'}</span>
                            </a>
                            {/if}

                            {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$article_info['uid'])}
                            <a href="{:url('ask/article/publish',['id'=>$article_info['id']])}" class=" py-1 text-muted dropdown-item" target="_blank">
                                <span>编辑文章</span>
                            </a>
                            <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-confirm="确定要删除吗？" data-url="{:url('article/remove_article',['id'=>$article_info['id']])}">
                                <span>删除文章</span>
                            </a>
                            {/if}
                        </div>
                    </div>
                </div>
                {/if}
            </div>
            <div class="{if $relation_article || $recommend_post}col-md-8 {else/}col-md-11 {/if}px-0 col-sm-12">
                <div class="bg-white p-3 uk-article-wrap rounded">
                    <article class="uk-article">
                        <h2 class="font-14 mb-3">{if $article_info.set_top}
                            <i class="iconfont icon-zhiding text-warning font-14"></i>{/if}{$article_info.title}
                        </h2>
                        <div class="uk-author-info mb-3">
                            <div class="uk-user overflow-hidden">
                                <dl class="overflow-hidden float-left mb-0">
                                    <dt class="float-left mr-2 mb-0">
                                        <a href="{$article_info['user_info']['url']}" class="uk-username" data-id="{$article_info.uid}">
                                            <img alt="{$article_info['user_info']['name']}" src="{$article_info['user_info']['avatar']}" class="uk-border-circle" style="width: 50px;height: 50px">
                                        </a>
                                    </dt>
                                    <dd class="float-left mb-0">
                                        <h6 class="mb-0">
                                            <a href="{$article_info['user_info']['url']}" class="uk-username" data-id="{$article_info.uid}">{$article_info['user_info']['name']}</a>
                                        </h6>
                                        <p>
                                            <span>
                                                <img src="{$article_info.user_info.group_icon}" style="width: auto;height: 18px;border-radius:0" data-tooltip="{$article_info.user_info.user_group_name}">
                                            </span>
                                        </p>
                                    </dd>
                                </dl>
                                <p class="float-right text-muted "><span>{$article_info.agree_count}</span>&nbsp;人点赞了该文章 · {$article_info.view_count}&nbsp;浏览</p>
                            </div>
                        </div>
                        <div class="uk-content mt-3">
                            {$article_info.message|raw}
                        </div>
                    </article>
                    {:hook('article_detail_bottom',$article_info)}

                    <div class="uk-article-bottom overflow-hidden mt-2 mb-2 text-muted">
                        <p class="float-left publish-info ">发布于 {:date_friendly($article_info['create_time'])}</p>
                        {if !empty($article_info['topics'])}
                        <div class="page-detail-topic float-right">
                            <ul id="uk-topic-list" class="d-inline p-0">
                                {volist name="article_info['topics']" id="v"}
                                <li class="d-inline uk-tag"><a href="{:url('ask/topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">{$v.title}</a></li>
                                {/volist}
                            </ul>
                            {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}
                            <a href="javascript:;" data-width="600px" class="uk-ajax-open d-inline" data-url="{:url('ask/ajax/topic',['item_type'=>'article','item_id'=>$article_info['id']])}" data-title="编辑话题"><i class="icon-edit1"> </i></a>
                            {/if}
                        </div>
                        {/if}
                    </div>
                    <div class="actions d-sm-none">
                        <label class="mr-3 mb-0">
                            <a href="javascript:;" class="{$article_info['vote_value']==1 ? 'active' : ''} uk-ajax-agree" onclick="UK.User.agree(this,'article','{$article_info.id}');">
                                <i class="icon-thumb_up"></i> 赞同 <span class="agree-count">{$article_info['agree_count'] ? $article_info['agree_count'] : ''}</span>
                            </a>
                        </label>
                        <div class="uk-popover mr-3 mb-0 d-inline-block">
                            <a href="javascript:;" class="popover-title" style="color: #76839b;">
                                <i class="icon-share"></i> 分享
                            </a>
                            <div class="popover-content">
                                <div class="text-left d-block py-2" style="min-width: 100px">
                                    <a href="javascript:;"  class="dropdown-item uk-clipboard" data-clipboard-text="{:url('article/detail',['id'=>$article_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                                    <a href="javascript:;" onclick="UK.User.share('{$article_info.title}','{:url('ask/article/detail',['id'=>$article_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="iconfont icon-weibo text-warning"></i> 新浪微博</a>
                                    <a href="javascript:;" onclick="UK.User.share('{$article_info.title}','{:url('ask/article/detail',['id'=>$article_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="iconfont icon-QQ text-primary"></i> 腾讯空间</a>
                                    <div class="uk-qrcode-container" data-share="{:url('article/detail',['id'=>$article_info.id],true,true)}">
                                        <a href="javascript:;" class="dropdown-item "><i class="iconfont icon-weixin text-success"></i> 微信扫一扫</a>
                                        <div class="uk-qrcode text-center py-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if $user_id}
                        {if $user_id!=$article_info['uid']}
                        <label class="mr-3 mb-0">
                            <a href="javascript:;" class="uk-ajax-against {$article_info['vote_value']==-1 ? 'active' : ''}" onclick="UK.User.against(this,'article','{$article_info.id}');">
                                <i class="icon-thumb_down"></i> <span>反对</span>
                            </a>
                        </label>
                        <label class="mr-3 mb-0"  onclick="UK.User.report(this,'article','{$article_info.id}');">
                            <a href="javascript:;">
                                <i class="icon-warning"></i> <span>{if $article_info['is_report']}已举报{else/}举报{/if}</span>
                            </a>
                        </label>
                        {/if}
                        <label class="mr-3 mb-0" onclick="UK.User.favorite(this,'article','{$article_info.id}');">
                            <a href="javascript:;">
                                <i class="icon-star-outlined"></i> <span>{if $article_info['is_favorite']}已收藏{else/}收藏{/if}</span>
                            </a>
                        </label>
                        {/if}
                        {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$article_info['uid'])}
                        <div class="uk-popover mr-3 d-inline-block">
                            <a href="javascript:;" class="popover-title" style="color: #76839b;">
                                <i class="icon-more-horizontal"></i> <span>更多</span>
                            </a>
                            <div class="popover-content">
                                <div class="text-center d-block py-2" style="min-width: 100px">
                                    {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}
                                    <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ask/article/action',['type'=>'recommend','is_recommend'=>$article_info['is_recommend'],'article_id'=>$article_info['id']])}">
                                        <span>{$article_info['is_recommend'] ? '取消推荐' : '推荐文章'}</span>
                                    </a>
                                    <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ask/article/action',['type'=>'set_top','set_top'=>$article_info['set_top'],'article_id'=>$article_info['id']])}">
                                        <span>{$article_info['set_top'] ? '取消置顶' : '置顶文章'}</span>
                                    </a>
                                    {/if}

                                    {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$article_info['uid'])}
                                    <a href="{:url('ask/article/publish',['id'=>$article_info['id']])}" class=" py-1 text-muted dropdown-item" target="_blank">
                                        <span>编辑文章</span>
                                    </a>
                                    <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-confirm="确定要删除吗？" data-url="{:url('ask/article/remove_article',['id'=>$article_info['id']])}">
                                        <span>删除文章</span>
                                    </a>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                    <div class="font-9 mt-2 p-2 bg-light">
                        <p class="text-muted"><i class="icon-info-with-circle"></i> 本文由 <a href="{$article_info['user_info']['url']}">{$article_info['user_info']['name']}</a> 原创发布于 <a href="{$baseUrl}">{$setting['site_name']}</a> ，著作权归作者所有。</p>
                    </div>
                </div>
                <div class="bg-white mt-2 p-3 uk-article-comment-editor rounded">
                    {if $user_id}
                    <form method="post" action="{:url('ask/article/save_comment')}">
                        <input type="hidden" name="article_id" value="{$article_info.id}">
                        <input type="hidden" name="at_info" value="">
                        <input type="hidden" name="pid" value="0">
                        <textarea type="text" name="message" class="form-control" rows="6" placeholder="写下您的评论吧..."></textarea>
                        <div class="overflow-hidden mt-3">
                            <div class="float-left uk-username" data-id="{$user_info.uid}">
                                <img src="{$user_info['avatar']|default='/static/plugin/uk-home/images/avatars/avatar-2.jpg'}" alt="{$user_info['name']}" class="uk-border-circle" style="width: 36px;height: 36px">
                                <span>{$user_info['user_name']}</span>
                            </div>
                            <div class="float-right">
                                <button type="button" class="uk-article-comment-submit btn btn-primary btn-sm">发布</button>
                            </div>
                        </div>
                    </form>
                    {else/}
                    <p class="text-center">登录一下，更多精彩内容等你发现，贡献精彩回答，参与评论互动</p>
                    <p class="text-center mt-2">去 <a href="{:url('member/account/login')}" class="mr-1 text-primary">登录</a>! 还没有账号？去<a href="{:url('member/account/register')}" class="text-primary">注册</a></p>
                    {/if}
                </div>
                {if $article_info.comment_count}
                <div id="comment-container " class="rounded">
                    <div class="uk-mod bg-white px-3 mt-2 pt-3">
                        <div class="uk-mod-head mb-0">
                            <p class="mod-head-title">全部 <span class="uk-comment-count">{$article_info.comment_count}</span>条评论</p>
                            <div class="uk-popover mod-head-more">
                                <a href="javascript:;" class="popover-title uk-sort-show">
                                    <span>{$sort=='hot' ? '热门排序':'默认排序'}</span> <i class="icon-select-arrows"></i>
                                </a>
                                <div class="popover-content">
                                    <div class="text-center d-block py-2 uk-nav uk-dropdown-nav text-center uk-answer-sort" style="min-width: 100px">
                                        <div class="{$sort=='new' ? 'active':''} py-1"><a href="{:url('ask/article/detail',['id'=>$article_info['id'],'sort'=>'new'])}"  data-pjax="answer-container">默认排序</a> </div>
                                        <div class="{$sort=='hot' ? 'active':''} py-1"><a href="{:url('ask/article/detail',['id'=>$article_info['id'],'sort'=>'hot'])}"  data-pjax="answer-container">热门排序 </a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="post-comments post" style="padding: 0;box-shadow: none">
                        <div id="article-comment-list">
                            {if $comment_list}
                            {volist name="comment_list" id="v"}
                            <div class="bg-white p-3 mb-1 uk-article-comment-item" id="article-comment-{$v.id}">
                                <div class="user-details-card pt-0 pb-2">
                                    <div class="user-details-card-avatar" style="position: relative">
                                        <a href="{:get_user_url($v['uid'])}">
                                            <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
                                        </a>
                                    </div>
                                    <div class="user-details-card-name">
                                        <a href="{$v['user_info']['url']}">{$v['user_info']['name']}</a> <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
                                    </div>
                                </div>
                                <p>{$v.message|raw}</p>
                                <div class="actions">
                                    <div class="font-9 mt-2">
                                        <a href="javascript:;" class="text-muted uk-ajax-agree mr-3 {if $v['vote_value']==1}active{/if}" onclick="UK.User.agree(this,'article_comment','{$v.id}')"><i class="icon-thumb_up"></i> 点赞 <span>{$v.agree_count}</span></a>
                                        {if $user_id}
                                        <a href="javascript:;" class="mr-3 text-muted article-comment-reply"  data-comment-id="{$v.id}" data-username="{$v['user_info']['user_name']}" data-info='{:json_encode(["uid"=>$v["uid"],"user_name"=>$v["user_info"]["user_name"]])}'> <i class="icon-reply"></i> 回复 </a>
                                        {if $user_id==$v['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                                        <a href="javascript:;" class="text-muted uk-ajax-get" data-confirm="确定要删除吗？" data-url="{:url('ask/article/remove_comment',['id'=>$v.id])}"> <i class="icon-delete mr-1"></i>删除 </a>
                                        {/if}
                                        {/if}
                                    </div>
                                </div>
                                <div class="replay-editor mt-2" style="display: none"></div>
                                {if isset($v['childs']) && $v['childs']}
                                <div class="article-comment-child px-3 bg-light rounded mt-2">
                                    {volist name="$v['childs']" id="v1"}
                                    <div class="p-3 mb-1 uk-article-comment-item border-bottom" style="border-color: #eee !important;" id="article-comment-{$v1.id}">
                                        <div class="user-details-card pt-0 pb-2">
                                            <div class="user-details-card-avatar" style="position: relative">
                                                <a href="{:get_user_url($v['uid'])}">
                                                    <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
                                                </a>
                                            </div>
                                            <div class="user-details-card-name">
                                                <a href="{$v['user_info']['url']}">{$v['user_info']['name']}</a> <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
                                            </div>
                                        </div>
                                        <p>{$v1.message|raw}</p>
                                        <div class="actions">
                                            <div class="font-9 mt-2">
                                                <a href="javascript:;" class="text-muted uk-ajax-agree mr-3 {if $v1['vote_value']==1}active{/if}" onclick="UK.User.agree(this,'article_comment','{$v1.id}')"><i class="icon-thumb_up"></i> 点赞 <span>{$v1.agree_count}</span></a>
                                                {if $user_id}
                                                <a href="javascript:;" class="mr-3 text-muted article-comment-reply" data-username="{$v1['user_info']['user_name']}" data-comment-id="{$v1.id}" data-info='{:json_encode(["uid"=>$v1["uid"],"user_name"=>$v1["user_info"]["user_name"]])}'> <i class="icon-reply"></i> 回复 </a>
                                                {if $user_id==$v1['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                                                <a href="javascript:;" class="text-muted uk-ajax-get" data-confirm="确定要删除吗？" data-url="{:url('article/remove_comment',['id'=>$v1.id])}"> <i class="icon-delete mr-1"></i>删除 </a>
                                                {/if}
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="replay-editor mt-2" style="display: none"></div>
                                    </div>
                                    {/volist}
                                </div>
                                {/if}
                            </div>
                            {/volist}
                            <div class="p-3 bg-white">{$page_render|raw}</div>
                            {/if}
                        </div>
                    </div>
                </div>
                {/if}
            </div>
            {if $relation_article || $recommend_post}
            <div class="col-md-3 col-sm-12">
                {if $relation_article}
                <div class="uk-mod bg-white p-3 mb-1">
                    <div class="uk-mod-head mb-1">
                        <p class="mod-head-title">相关文章</p>
                    </div>
                    <div>
                        {volist name="relation_article" id="v"}
                        <dl class="mb-0 py-2">
                            <dt class="d-block uk-one-line">
                                <a href="{:url('ask/article/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} 浏览</label>
                                <label class="mr-2 mb-0">{$v['comment_count']} 评论</label>
                            </dd>
                        </dl>
                        {/volist}
                    </div>
                </div>
                {/if}

                {if $recommend_post}
                <div class="uk-mod bg-white p-3 mb-1">
                    <div class="uk-mod-head mb-1">
                        <p class="mod-head-title font-12">推荐内容</p>
                    </div>
                    <div>
                        {volist name="recommend_post" id="v"}
                        {if $v['item_type']=='article'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block uk-one-line font-weight-normal font-9">
                                <span class="bg-primary text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">文</span> <a href="{:url('ask/article/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} 浏览</label>
                                <label class="mr-2 mb-0">{$v['comment_count']} 评论</label>
                            </dd>
                        </dl>
                        {/if}
                        {if $v['item_type']=='question'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block uk-one-line font-weight-normal font-9">
                                <span class="bg-warning text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">问</span> <a href="{:url('ask/question/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} 浏览</label>
                                <label class="mr-2 mb-0">{$v.focus_count} 关注</label>
                                <label class="mr-2 mb-0">{$v['comment_count']} 评论</label>
                            </dd>
                        </dl>
                        {/if}
                        {/volist}
                    </div>
                </div>
                {/if}
            </div>
            {/if}
        </div>
    </div>
</div>