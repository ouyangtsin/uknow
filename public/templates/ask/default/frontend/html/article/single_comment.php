<div class="bg-white p-3 mb-1 uk-article-comment-item" id="article-comment-{$comment_info.id}">
    <div class="user-details-card pt-0 pb-2">
        <div class="user-details-card-avatar" style="position: relative">
            <a href="{:url('member/index//index',['uid'=>$comment_info['uid']])}">
                <img src="{$comment_info['user_info']['avatar']}" alt="{$comment_info['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
        </div>
        <div class="user-details-card-name">
            <a href="{$comment_info['user_info']['url']}">{$comment_info['user_info']['name']}</a> <span class="ml-0"> {:date('Y-m-d H:i',$comment_info['create_time'])} </span>
        </div>
    </div>
    <p>{$comment_info.message|raw}</p>
    <div class="actions">
        <div class="font-9 mt-2">
            <a href="javascript:;" class="text-muted uk-ajax-agree mr-3 {if $comment_info['vote_value']==1}active{/if}" onclick="UK.User.agree(this,'article_comment','{$comment_info.id}')"><i class="icon-thumb_up"></i> 点赞 <span>{$comment_info.agree_count}</span></a>
            {if $user_id}
            <a href="javascript:;" class="mr-3 text-muted article-comment-reply" data-username="{$comment_info['user_info']['user_name']}"> <i class="icon-reply"></i> 回复 </a>
            {if $user_id==$comment_info['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
            <a href="javascript:;" class="text-muted uk-ajax-get" data-confirm="确定要删除吗？" data-url="{:url('article/remove_comment',['id'=>$comment_info.id])}"> <i class="icon-delete mr-1"></i>删除 </a>
            {/if}
            {/if}
        </div>
    </div>
    <div class="replay-editor mt-2" style="display: none"></div>
</div>