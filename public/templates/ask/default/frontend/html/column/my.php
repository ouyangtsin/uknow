<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0">
                <div class="uk-nav-container clearfix bg-white px-3">
                    <h2 class="float-left"><i class="icon-explore"></i> 我的专栏</h2>
                    <ul class="float-right">
                        <li class="{if $verify==1}active{/if}"><a data-pjax="uk-index-main" href="{:url('column/my',['verify'=>1])}">已审核</a></li>
                        <li class="{if $verify==0}active{/if}"><a data-pjax="uk-index-main" href="{:url('column/my',['verify'=>0])}">待审核</a></li>
                        <li class="{if $verify==2}active{/if}"><a data-pjax="uk-index-main" href="{:url('column/my',['verify'=>2])}">已拒绝</a></li>
                    </ul>
                </div>

                <div id="uk-index-main" class="p-3 bg-white">
                    {if !empty($list)}
                    <div class="my-column-list">
                        {volist name="list" id="v"}
                        <div class="column-item border-bottom">
                            <dl class="mb-0 clearfix py-2">
                                <dt class="float-left">
                                    <a href="{:url('column/detail',['id'=>$v['id']])}">
                                        <img src="{$v.cover}" alt="" width="70" height="70" class="rounded">
                                    </a>
                                </dt>
                                <dd class="float-right" style="width: calc(100% - 80px)">
                                    <h3 class="uk-one-line font-10 mb-0"> <a href="{:url('column/detail',['id'=>$v['id']])}">{$v.name}</a></h3>
                                    <p class="text-color-info my-1 font-9 uk-one-line">{$v.description}</p>
                                    <div class="font-9 clearfix">
                                        <div class="float-left">
                                            <a href="javascript:;" class="text-color-info"> {$v.post_count|num2string} 内容 </a><span class="text-color-info"> | </span>
                                            <a href="javascript:;" class="text-color-info"> {$v.focus_count|num2string} 关注 </a>
                                        </div>

                                        <div class="float-right font-9">
                                            <div class="dropdown show d-inline-block">
                                                <a href="javascript:;" class="dropdown-toggle d-none-arrow text-color-info" data-toggle="dropdown" ><i class="icon-more-horizontal"></i></a>
                                                <div class="dropdown-menu detail-more-dropdown text-center">
                                                    <span class="arrow"></span>
                                                    {if $verify==1}
                                                    <a href="javascript:;" class="dropdown-item">发文</a>
                                                    {/if}
                                                    <a href="{:url('column/apply',['id'=>$v.id])}" class="dropdown-item">编辑</a>
                                                    <a href="javascript:;" class="dropdown-item">管理</a>
                                                    <a href="javascript:;" class="dropdown-item">删除</a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        {/volist}
                    </div>
                    {$page|raw}
                    {else/}
                    <p class="text-center p-3 text-color-info">
                        <img src="/static/common/image/empty.svg">
                        <span class="mt-3 d-block ">暂无记录</span>
                    </p>
                    {/if}
                </div>
            </div>
            <!--侧边栏-->
            <div class="uk-right col-md-3 col-sm-12">
                <div class="uk-mod mb-1">
                    <div class="uk-mod-body">
                        <dl class="overflow-hidden mb-0 border-bottom bg-white p-3">
                            <dt class="float-left">
                                <a href="{$user_info['url']}">
                                    <img src="{$user_info['avatar']|default='/static/common/image/default-avatar.svg'}" width="45" height="45">
                                </a>
                            </dt>
                            <dd class="float-right" style="width:calc(100% - 60px)">
                                <a href="{$user_info['url']}" class="d-block">
                                    <strong>{$user_info['name']}</strong>
                                    <img src="{$user_info['group_icon']}" height="20">
                                </a>
                                <p class="mb-0 font-8 text-muted uk-one-line">{$user_info['signature']}</p>
                            </dd>
                        </dl>
                        <div class="d-flex text-center bg-white p-3 text-muted">
                            <dl class="flex-fill mb-0">
                                <dt>{$user_info['answer_count']}</dt>
                                <dd>回答</dd>
                            </dl>
                            <dl class="flex-fill mb-0">
                                <dt>{$user_info['article_count']}</dt>
                                <dd>文章</dd>
                            </dl>
                            <dl class="flex-fill mb-0">
                                <dt>{$user_info['question_count']}</dt>
                                <dd>问题</dd>
                            </dl>
                        </div>
                        <div class="d-flex bg-white p-3 border-top">
                            <a href="{:url('column/apply')}" class="btn btn-sm btn-primary flex-fill mr-1">申请专栏</a>
                            <a href="{:url('column/my')}" class="btn btn-sm btn-outline-primary flex-fill ml-1">我的专栏</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
