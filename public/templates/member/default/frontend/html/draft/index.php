<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="bg-white">
                    <div class="uk-nav-container clearfix px-3">
                        <h2 class="float-left"><i class="icon-drafts"></i> 我的草稿</h2>
                        <ul class="float-right uk-pjax-tab">
                            <li class="{if $type=='question'}active{/if} mr-3"><a data-pjax="uk-index-main" href="{:url('draft/index',['type'=>'question'])}">问题草稿</a></li>
                            <li class="{if $type=='article'}active{/if} mr-3"><a data-pjax="uk-index-main" href="{:url('draft/index',['type'=>'article'])}">文章草稿</a></li>
                            <li class="{if $type=='answer'}active{/if}"><a data-pjax="uk-index-main" href="{:url('draft/index',['type'=>'answer'])}">回答草稿</a></li>
                        </ul>
                    </div>
                    <div id="uk-index-main" class="px-3">
                        {if !empty($list)}
                        <div class="uk-common-list py-2">
                            {volist name="list" id="v"}
                            {switch name="$v['item_type']"}
                            {case value="question" }
                            <dl class="question position-relative">
                                <dt class="mb-2">
                                    <a href="{$user_info['url']}" class="uk-user-name">
                                        <img src="{$user_info['avatar']}" alt="{$user_info['name']}">{$user_info['name']}
                                    </a>
                                    <span>发起了提问</span>
                                    <label class="float-right">{:date_friendly($v['create_time'])}</label>
                                    {if isset($v['data']['topics']) && !empty($v['data']['topics'])}
                                    <div class="uk-tag d-inline">
                                        {volist name="$v['data']['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                                        {/volist}
                                    </div>
                                    {/if}
                                </dt>
                                <dd class="title">
                                    <p class="bold">
                                        <em class="ding"></em>
                                        {if $v['item_id']}
                                        <a href="{:url('question/detail',['id'=>$v['item_id']])}" class="uk-one-line">{$v.data.title}</a>
                                        {else/}
                                        <a href="javascript:;" class="uk-one-line">{$v.data.title}</a>
                                        {/if}
                                    </p>
                                </dd>
                                <dd class="content my-2 uk-two-line">
                                    {$v['data']['detail']|raw}
                                </dd>
                                {if $v['item_id']}
                                <div class="uk-common-footer">
                                    <label class="mr-2">
                                        <a href="javascript:;" class="{$v['vote_value']==1 ? 'active' : ''}" data-toggle="popover" title="点赞问题" onclick="UK.User.agree(this,'question','{$v.id}');"><i class="icon-thumb_up"></i> 赞同 <span>{$v['agree_count']}</span></a>
                                    </label>
                                    <label class="mr-2"><i class="icon-eye"></i> {$v.view_count} 浏览</label>
                                    <label class="mr-2"><i></i> {$v.focus_count} 关注</label>
                                    <label class="mr-2"><i class="icon-comment"></i> {$v['comment_count']} 评论</label>
                                </div>
                                {/if}
                                <div class="uk-draft-action font-9 text-muted position-absolute" style="bottom: 1rem;right: 0">
                                    <a href="javascript:;" class="text-color-info mr-2 uk-ajax-get" data-url="{:url('draft/delete',['type'=>'question','item_id'=>$v['item_id']])}" data-confirm="是否确认删除草稿">删除草稿</a>
                                    <a href="{:url('question/publish',['id'=>$v['item_id']])}" class="text-color-info">编辑</a>
                                </div>
                            </dl>
                            {/case}

                            {case value="article" }
                            <dl class="article position-relative">
                                {if $v['data']['cover']}
                                <dt class="col-sm-12">
                                    <a href="{if $v['item_id']}{:url('article/detail',['id'=>$v['item_id']])}{else/}javascript:;{/if}">
                                        <img src="{$v['data']['cover']|default='/static/common/image/default-cover.svg'}" alt="{$v['data']['title']}"  class="rounded uk-cut-img">
                                    </a>
                                </dt>
                                {/if}
                                <dd class="col-sm-12 m-0 px-0" {if !$v['data']['cover']}style="width:100%"{/if}>
                                    <h2>
                                        {if $v['item_id']}
                                        <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['data']['title']}</a>
                                        {else/}
                                        <a href="javascript:;">{$v['data']['title']}</a>
                                        {/if}
                                    </h2>
                                    <div class="content uk-two-line">
                                        {$v.data.message|raw}
                                    </div>
                                    {if isset($v['data']['topics']) && !empty($v['data']['topics'])}
                                    <div class="uk-tag">
                                        {volist name="$v['data']['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                                        {/volist}
                                    </div>
                                    {/if}
                                    <div class="uk-common-footer">
                                        <a href="{$user_info['url']}" class="uk-user-name avatar">
                                            <img src="{$user_info['avatar']}" alt="" class="uk-border-circle" style="width: 22px;height: 22px">
                                        </a>
                                        <a href="{$user_info['url']}" class="uk-user-name name">{$user_info['name']}</a>
                                        <span> | {:date_friendly($v['create_time'])}</span>
                                        <div class="uk-draft-action font-9 d-inline-block ml-3">
                                            <a href="javascript:;" class="text-color-info mr-2 uk-ajax-get" data-url="{:url('draft/delete',['type'=>'article','item_id'=>$v['item_id']])}" data-confirm="是否确认删除草稿">删除草稿</a>
                                            <a href="{:url('article/publish',['id'=>$v['item_id']])}" class="text-color-info">编辑</a>
                                        </div>
                                        <div class="float-right">
                                            <label><i class="icon-eye"></i> {$v['item_id'] ? $v['view_count'] : 0}</label>
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
                        <p class="text-center mt-4 text-muted">
                            <img src="/static/common/image/empty.svg">
                            <span class="py-3 d-block  ">暂无内容</span>
                        </p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
