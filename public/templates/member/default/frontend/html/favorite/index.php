<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="bg-white">
                    <div class="uk-nav-container py-2 px-3">
                        <ul class="uk-pjax-tab">
                            <li class="{if $type=='my'}active{/if} mr-3"><a data-pjax="uk-index-main" href="{:url('member/favorite/index',['type'=>'my'])}">我创建的</a></li>
                            <li class="{if $type=='focus'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/favorite/index',['type'=>'focus'])}">我关注的</a></li>
                        </ul>
                    </div>
                    <div id="uk-index-main" class="p-3">
                        {if $list}
                        {volist name="list" id="v"}
                        <div class="favorite-tag-item">
                            <a href="{:url('member/favorite/detail',['id'=>$v['id']])}" class="font-12">{$v.title}</a>
                            <div class="mt-1 text-color-info font-9 overflow-hidden">
                                <span>{:date_friendly($v['update_time'])}更新 · {$v.post_count} 条内容 · {$v.focus_count} 人关注 </span>
                                <a href="javascript:;" class="ml-3 uk-ajax-get text-color-info float-right" data-confirm="是否删除该标签?" data-url="{:url('member/favorite/delete',['id'=>$v['id']])}">删除</a>
                            </div>
                        </div>
                        {/volist}
                        {$page|raw}
                        {else/}
                        <p class="text-center mt-4 text-meta">
                            <img src="/static/common/image/empty.svg">
                            <span class="mt-3 d-block ">暂无记录</span>
                        </p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>