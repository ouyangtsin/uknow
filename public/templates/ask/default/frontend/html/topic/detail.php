<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-xs-0 mb-1">
                <div class="bg-white px-3">
                    <dl class="topic-detail-top py-3 overflow-hidden mb-0">
                        <dt class="float-left">
                            <img src="{$topic_info['pic']|default='/static/common/image/topic.svg'}" class="rounded" alt="{$topic_info.title}">
                        </dt>
                        <dd class="float-right">
                            <h3 class="mb-2 font-12 clearfix">
                                {$topic_info.title}
                                {if $user_id}
                                <a href="javascript:;" class="font-9 float-right cursor-pointer {if $topic_info['has_focus']}active {/if}" onclick="UK.User.focus(this,'topic','{$topic_info.id}')">{$topic_info['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> 已关注</span>' : '<span><i class="icon-plus-circle text-primary"></i> 关注</span>'}</a>
                                {/if}
                            </h3>
                            {if $topic_info['description']}
                            <p class="text-muted uk-three-line mt-2">{:str_cut(strip_tags($topic_info['description']),0,100)}
                                {if mb_strlen(strip_tags($topic_info['description']))>=100}
                                <a href="{:url('topic/detail',['type'=>'about','id'=>$topic_info['id']])}"  data-pjax="uk-index-main" class="pl-3 text-primary">查看详情></a>
                                {/if}
                            </p>
                            {/if}
                        </dd>
                    </dl>
                </div>
                <div class="bg-white mt-2 px-3">
                    <div class="uk-nav-container clearfix position-relative w-100">
                        <ul class="uk-pjax-tab">
                            <li {if $type=='all'}class="active" {/if}><a data-pjax="uk-index-main" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'all','sort'=>$sort])}" data-type="question"> 综合 </a></li>
                            <li {if $type=='question'}class="active" {/if}><a data-pjax="uk-index-main" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'question','sort'=>$sort])}" data-type="question"> 问题 </a></li>
                            <li {if $type=='article'}class="active" {/if}><a data-pjax="uk-index-main" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'article','sort'=>$sort])}" data-type="article"> 文章 </a></li>
                            <li {if $type=='about'}class="active" {/if}><a data-pjax="uk-index-main" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'about'])}" data-type="about"> 简介 </a></li>
                        </ul>
                        {if $type!='about'}
                        <div class="position-absolute dropdown show" style="right: 0;top: 22px">
                            <a href="javascript:;" class="dropdown-toggle d-none-arrow text-muted" data-toggle="dropdown">
                                {if $sort=='new'}
                                <span>最新排序</span>
                                {elseif($sort=='hot')}
                                <span>热门排序</span>
                                {else/}
                                <span>综合排序</span>
                                {/if}
                                <i class="icon-select-arrows"></i>
                            </a>
                            <div class="dropdown-menu text-center uk-answer-sort-dropdown">
                                <span class="arrow"></span>
                                <div class="text-center">
                                    <div class="py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'all'])}" data-pjax="uk-index-main">综合排序</a> </div>
                                    <div class="py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'new'])}" data-pjax="uk-index-main">最新排序 </a></div>
                                    <div class="py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'hot'])}" data-pjax="uk-index-main">热门排序 </a></div>
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                    <div id="uk-index-main">
                        {switch name="type"}

                        {case value="all" }
                        {:widget('common/lists',['sort'=>$sort,'topic_ids'=>[$topic_info['id']]])}
                        {/case}

                        {case value="question" }

                        {:widget('common/lists',['item_type'=>'question','sort'=>$sort,'topic_ids'=>[$topic_info['id']]])}
                        {/case}

                        {case value="article" }

                        {:widget('common/lists',['item_type'=>'article','sort'=>$sort,'topic_ids'=>[$topic_info['id']]])}
                        {/case}

                        {case value="about" }
                        <div class="py-3 px-2 bg-white">
                            {$topic_info['description']|raw}
                        </div>
                        {/case}

                        {default /}
                        {/switch}
                    </div>
                </div>
            </div>
            <div class="uk-right col-md-3 col-sm-12 px-0">
                <div class="uk-mod bg-white uk-sidebar-write py-3 mb-2">
                    <div class="d-flex text-center">
                        <div class="flex-fill">
                            <a href="{:url('question/publish',['topic_id'=>$topic_info['id']])}">
                                <i class="icon-chat"></i><br>
                                <span>发讨论</span>
                            </a>
                        </div>
                        <div class="flex-fill">
                            <a href="{:url('article/publish',['topic_id'=>$topic_info['id']])}">
                                <i class="icon-map"></i><br>
                                <span>写文章</span>
                            </a>
                        </div>
                    </div>
                </div>
                {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}
                <div class="uk-mod bg-white sidebar-hot-topic p-3 mb-1">
                    <div class="uk-mod-head">
                        <p class="mod-head-title">话题管理</p>
                    </div>
                    <ul class="sidebar-user-list py-0">
                        <li>
                            <a href="{:url('topic/manager',['id'=>$topic_info['id']])}" class="text-muted"><i class="icon-edit"></i> 编辑话题</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-lock="{:url('ask/ajax/lock',['id'=>$topic_info['id']])}" class="text-muted"><i class="icon-lock"></i> {if $topic_info['lock']}取消锁定{else}锁定话题{/if} </a>
                        </li>
                        <li>
                            <a href="javascript:;" class="ajax-get text-muted" data-url="{:url('topic/remove_topic',['id'=>$topic_info['id']])}"><i class="icon-trash"></i> 删除话题</a>
                        </li>
                        <li>
                            <a class="uk-ajax-open text-muted" href="javascript:;" data-title="话题 {$topic_info.title} 操作日志" data-url="{:url('topic/logs',['id'=>$topic_info['id']])}"><i class="icon-book"></i> 话题日志</a>
                        </li>
                    </ul>
                </div>
                {/if}
                {:widget('sidebar/hot_topic',['uid'=>$user_id])}
            </div>
        </div>
    </div>
</div>
