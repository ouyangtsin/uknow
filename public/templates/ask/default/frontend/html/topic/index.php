<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 bg-white mb-1 rounded">
                <div class="uk-nav-container clearfix rounded">
                    <h2 class="float-left"><i class="iconfont icon-huati"></i> 话题广场</h2>
                    <ul class="float-right uk-pjax-tab">
                        <li class="{if $type=='recommend'}active{/if}"><a data-pjax="uk-index-main" href="{:url('topic/index',['type'=>'recommend','pid'=>$pid])}"> 全部 </a></li>
                        <li class="{if $type=='new'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('topic/index',['type'=>'new','pid'=>$pid])}" > 最新 </a></li>
                        <li class="{if $type=='hot'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('topic/index',['type'=>'hot','pid'=>$pid])}"> 最热 </a></li>
                    </ul>
                </div>
                <div class="py-3 uk-pjax-buttons">
                    <a href="{:url('topic/index',['type'=>$type,'pid'=>0])}" data-pjax="uk-index-main" class="mb-2 btn btn-sm px-3 mx-1 {$pid==0 ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">全部话题</a>
                    {volist name="$parent_list" id="v"}
                    <a href="{:url('topic/index',['type'=>$type,'pid'=>$v['id']])}" data-pjax="uk-index-main" class="mb-2 btn btn-sm mx-1 px-3n {$pid==$v['id'] ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">{$v.title}</a>
                    {/volist}
                </div>

                <div id="uk-index-main">
                    <div class="uk-overflow-hidden rounded">
                        <div class="uk-mod bg-white uk-topic-list">
                            <div class="row">
                                {volist name="list" id="v"}
                                <div class="col-md-6 col-xs-12 px-3 mb-3">
                                    <div class="border px-3 py-2 rounded">
                                        <dl>
                                            <dt>
                                                <a href="{:url('topic/detail',['id'=>$v['id']])}">
                                                    <img src="{$v['pic']|default='/static/common/image/topic.svg'}" class="rounded">
                                                </a>
                                            </dt>
                                            <dd class="info">
                                                <a href="{:url('topic/detail',['id'=>$v['id']])}">{$v.title}</a>
                                                <p class="mb-0 font-9 uk-two-line">{$v.description}</p>
                                            </dd>
                                        </dl>
                                        <p class="mt-2 font-9 text-muted">
                                            <span class="mr-3">{$v.discuss}个内容</span>
                                            <span class="mr-3"><span class="uk-global-focus-count">{$v.focus}</span>人关注</span>
                                            {if $user_id}
                                            <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active' : ''}" onclick="UK.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> 已关注</span>' : '<span><i class="icon-plus-circle text-primary"></i> 关注</span>'}</a>
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                                {/volist}
                            </div>
                        </div>
                    </div>
                    {$page|raw}
                </div>
            </div>
            <div class="uk-right col-md-3 col-sm-12 px-xs-0">
                {:widget('sidebar/hot_topic',['uid'=>$user_id])}
            </div>
        </div>
    </div>
</div>