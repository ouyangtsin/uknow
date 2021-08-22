{volist name="comment_list" id="v"}
<div class="bg-white p-3 mb-1 uk-article-comment-item" data-total="{$total}" id="article-comment-{$v.id}">
    <div class="user-details-card pt-0 pb-2">
        <div class="user-details-card-avatar" style="position: relative">
            <a href="{:url('member/index//index',['uid'=>$v['uid']])}">
                <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
        </div>
        <div class="user-details-card-name">
            <a href="{$v['user_info']['url']}">{$v['user_info']['name']}</a> <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
        </div>
    </div>
    <p>{$v.message|raw}</p>
	<div class="actions">
		<div class="font-9 mt-2">
            <a href="javascript:;" class="text-muted uk-ajax-agree mr-3 {if $v['vote_value']==1}active{/if}" onclick="UK.User.agree(this,'article_comment','{$v.id}')"><i class="icon-thumb_up"></i> 点赞 <span>{$v.agree_count}</span></a>
			{if $user_id}
            <a href="javascript:;" class="mr-3 text-muted article-comment-reply" data-username="{$v['user_info']['user_name']}" data-info='{:json_encode(["uid"=>$v["uid"],"user_name"=>$v["user_info"]["user_name"]])}'> <i class="icon-reply"></i> 回复 </a>
            {if $user_id==$v['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
			<a href="javascript:;" class="text-muted uk-ajax-get" data-confirm="确定要删除吗？" data-url="{:url('article/remove_comment',['id'=>$v.id])}"> <i class="icon-delete mr-1"></i>删除 </a>
            {/if}
            {/if}
		</div>
	</div>
    <div class="replay-editor mt-2" style="display: none"></div>
</div>
{/volist}