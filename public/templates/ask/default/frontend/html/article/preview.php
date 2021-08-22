<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="col-md-1 text-center actions">
                <label class="px-1 py-2 bg-white rounded d-block mb-2">
                    <a href="javascript:;"  onclick="UK.User.agree(this,'article','{$article_info.id}');">
                        <i class="icon-thumb_up font-12"></i>
                        <span class="d-block">{$article_info['agree_count']}</span>
                    </a>
                </label>
            </div>
            <div class="col-md-7 px-0">
                <div class="bg-white p-3">
                    <article class="uk-article">
                        <h2 class="font-14 mb-3">{$article_info.title}</h2>
                        <div class="uk-author-info mb-3">
                            <div class="uk-user overflow-hidden">
                                <dl class="overflow-hidden float-left mb-0">
                                    <dt class="float-left mr-2 mb-0">
                                        <a href="{$user_info['url']}">
                                            <img alt="{$user_info['name']}" src="{$user_info['avatar']}" class="uk-border-circle" style="width: 40px;height: 40px">
                                        </a>
                                    </dt>
                                    <dd class="float-left mb-0">
                                        <h6 class="mb-0"><a href="{$user_info['url']}">{$user_info['name']}</a></h6>
                                        <p>
                                            <span><i class="icon-vimeo-with-circle uk-text-danger"></i></span>
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
                    <div class="uk-article-bottom overflow-hidden mt-4 text-muted">
                        <p class="float-left publish-info ">发布于 {:date_friendly($article_info['create_time'])}</p>
                        {if isset($article_info['topics']) && !empty($article_info['topics'])}
                        <div class="page-detail-topic float-right">
                            <ul id="uk-topic-list" class="d-inline p-0">
                                {volist name="article_info['topics']" id="v"}
                                <li class="d-inline uk-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}">{$v.title}</a></li>
                                {/volist}
                            </ul>
                        </div>
                        {/if}
                    </div>
                    <div class="text-muted font-8 mt-2">
                        <p><i class="icon-info-with-circle"></i> 本文由 <a href="{$user_info['url']}">{$user_info['name']}</a> 原创发布于 <a href="{$baseUrl}">{$setting['site_name']}</a> ，著作权归作者所有。</p>
                    </div>
                </div>

                {if $article_info.comment_count}
                <div class="uk-mod bg-white px-3 mt-2 pt-3">
                    <div class="uk-mod-head mb-0">
                        <p class="mod-head-title">全部 <span class="uk-answer-count">{$article_info.comment_count}</span>条评论</p>
                        <div class="mod-head-more dropdown show">
                            <a href="javascript:;" class="dropdown-toggle d-none-arrow uk-sort-show" data-toggle="dropdown">
                                <span>默认排序</span> <i class="icon-select-arrows"></i>
                            </a>
                            <div class="dropdown-menu text-center uk-answer-sort-dropdown">
                                <span class="arrow"></span>
                                <div class="uk-nav uk-dropdown-nav text-center uk-answer-sort">
                                    <div class="active py-1" data-type="new"><a href="JavaScript:;">默认排序</a> </div>
                                    <div class="py-1" data-type="hot"><a href="JavaScript:;">热门排序 </a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-comments post" style="padding: 0;box-shadow: none">
                    <div id="article-comment-list"  data-url="{:url('article/get_ajax_comment',['article_id'=>$article_info['id']])}"></div>
                </div>
                {/if}
            </div>
            <div class="col-md-3">
                {:widget('sidebar/hot_topic',['uid'=>$user_id])}
            </div>
        </div>
    </div>
</div>