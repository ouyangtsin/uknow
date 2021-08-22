<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0">
                <div class="bg-white p-3">
                    <div class="d-flex">
                        <div class="flex-fill">
                            <h3 class="mb-2 font-15">{$title}</h3>
                            <button type="button" onclick="UK.User.focus(this,'favorite','{$id}')" class="btn btn-primary btn-sm {if $focus}active{/if} px-4">{if $focus}已关注{else}关注{/if}</button>
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex text-center">
                                <dl class="flex-fill">
                                    <dt>内容数</dt>
                                    <dd>{$post_count|num2string}</dd>
                                </dl>
                                <dl class="flex-fill">
                                    <dt>评论数</dt>
                                    <dd>{$comment_count|num2string}</dd>
                                </dl>
                                <dl class="flex-fill">
                                    <dt>关注数</dt>
                                    <dd>{$focus_count|num2string}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-1 bg-white p-3">
                    {if $list}
                    {:widget('common/parse',['list'=>$list,'page'=>$page])}
                    {else/}
                    <p class="uk-text-center mt-4text-meta">
                        <img src="/static/common/image/empty.svg">
                        <span class="mt-3 d-block ">暂无收藏记录</span>
                    </p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>