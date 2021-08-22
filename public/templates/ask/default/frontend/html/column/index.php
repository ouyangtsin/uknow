<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0">
                <div class="uk-nav-container clearfix bg-white px-3 rounded-top-right rounded-top-left">
                    <h2 class="float-left"><i class="icon-explore"></i> 专栏</h2>
                    <ul class="float-right uk-pjax-tab">
                        <li class="{if $sort=='new'}active{/if}"><a data-pjax="uk-index-main" href="{:url('column/index')}">最新</a></li>
                        <li class="{if $sort=='hot'}active{/if}"><a data-pjax="uk-index-main" href="{:url('column/index',['sort'=>'hot'])}">热门</a></li>
                        <li class="{if $sort=='recommend'}active{/if}"><a data-pjax="uk-index-main" href="{:url('column/index',['sort'=>'recommend'])}">推荐</a></li>
                    </ul>
                </div>

                <div id="uk-index-main" class="rounded-bottom-right rounded-bottom-left">
                    {if !empty($list)}
                    <div class="row mx-0">
                        {volist name="list" id="v"}
                        <div class="col-md-3 px-1">
                            <div class="p-3 bg-white mr-2 my-1 rounded">
                                <div class="group-card-thumbnail mb-2">
                                    <a href="{:url('column/detail',['id'=>$v['id']])}" class="d-block text-center">
                                        <img src="{$v.cover}" alt="{$v.name}" style="width: 50px;height: 50px;border-radius: 50%">
                                    </a>
                                </div>
                                <div class="mt-1">
                                    <h3 class="uk-one-line font-11 text-center"><a href="{:url('column/detail',['id'=>$v['id']])}">{$v.name}</a></h3>
                                    <p class="text-color-info my-2 uk-two-line font-9 text-center">{$v.description}</p>
                                    <div class="text-center font-9 py-1 mb-2">
                                        <a href="javascript:;" class="text-color-info"> {$v.post_count|num2string} 内容 </a><span class="text-color-info"> | </span>
                                        <a href="javascript:;" class="text-color-info"> {$v.focus_count|num2string} 关注 </a>
                                    </div>
                                    <div class="d-flex">
                                        <a href="{:url('column/detail',['id'=>$v['id']])}" class="btn btn-sm btn-outline-primary flex-fill">进入专栏</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/volist}
                    </div>
                    {$page|raw}
                    {else/}
                    <p class="text-center p-3 text-color-info bg-white">
                        <img src="/static/common/image/empty.svg">
                        <span class="mt-3 d-block ">暂无记录</span>
                    </p>
                    {/if}
                </div>
            </div>
            <!--侧边栏-->
            <div class="uk-right col-md-3 col-sm-12 px-xs-0">
                {if $user_id}
                <div class="uk-mod mb-1 rounded">
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
                {/if}
            </div>
        </div>
	</div>
</div>
