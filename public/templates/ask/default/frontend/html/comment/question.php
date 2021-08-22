<div class="uk-comment-box" style="background: rgb(246, 246, 246);">
    {if !empty($list)}
    <div class="uk-comment-list uk-overflow-auto position-absolute w-100" style="height: 505px;">
        {volist name="list" id="v"}
        <div class="uk-comment-item bg-white p-3 mb-1" data-id="{$v.id}">
            <div class="user-details-card pt-0 pb-2">
                <div class="user-details-card-avatar" style="position: relative">
                    <a href="{:get_user_url($v['uid'])}">
                        <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
                    </a>
                </div>
                <div class="user-details-card-name">
                    <a href="{$v['user_info']['url']}" class="text-dark uk-username" data-id="{$v.uid}">{$v['user_info']['user_name']}</a> <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
                </div>
            </div>
            <div class="uk-comment-text">
                <div class="post-comment-text-inner">
                    <p class="text-muted font-8">
                        {if $v['at_info']}
                        <a href="{:get_user_url($v['at_info']['uid'])}">@{$v['at_info']['user_name']} </a>
                        {/if}{$v.message}
                    </p>
                </div>
                <div class="uk-comment-actions mt-2 font-9">
                    <label class="mr-3">
                        <a href="javascript:;" data-type="vote"> <i class="icon-thumb_up"></i> (<span>{$v.vote_count}</span>)</a>
                    </label>
                    {if $user_id}
                    <label class="mr-3">
                        <a href="javascript:;" data-type="reply" data-info='{:json_encode(["uid"=>$v.uid,"user_name"=>$v.user_info.user_name])}'> <i class="icon-reply"></i> 回复 </a>
                    </label>
                    <label class="mr-3">
                        <a href="javascript:;" onclick="UK.User.report(this,'question_comment','{$v.id}')" {if !$v.report}data-type="report"{/if}> <i class="icon-warning"></i> {if $v.report}已{/if}举报 </a>
                    </label>
                    {if ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)}
                    <label>
                        <a href="javascript:;" data-confirm="是否删除该评论?" class="ajax-get"  data-url="{:url('question/delete_comment?id='.$v['id'])}"> <i class="icon-delete"></i> 删除 </a>
                    </label>
                    {/if}
                    {/if}
                </div>
            </div>
        </div>
        {/volist}
        {if $page}
        <div class="px-3 py-2 bg-white">{$page|raw}</div>
        {/if}
    </div>
    {else/}
    <p class="text-center pt-4 text-muted">
        <img src="/static/common/image/empty.svg">
        <span class="mt-3 d-block">该问题暂无评论,快来发表你的评论吧</span>
    </p>
    {/if}
    {if $user_id}
    <div class="uk-dialog-footer fixed-bottom p-2 bg-white">
        <form action="{:url('question/save_comment')}" method="post">
            <input type="hidden" name="at_info">
            <input type="hidden" name="id">
            <input type="hidden" name="question_id" value="{$question_id}">
            <div class="uk-overflow-hidden">
                <input type="text" name="message" class="question-comment-input form-control float-left" placeholder="写下您的评论..." style="width: calc(100% - 85px)">
                <button type="button" class="btn btn-primary px-3 uk-ajax-form float-right" >发布</button>
            </div>
        </form>
    </div>
    {/if}
</div>