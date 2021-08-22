{if !isset($list)}
<div class="w-100 p-3" style="height: 500px;">
	<form method="post" action="">
		<input type="hidden" name="question_id" id="question-id" value="{$question_id}">
		<div class="form-group invite-user-search overflow-hidden">
			<input type="text" class="float-left form-control" placeholder="输入要邀请的用户" id="invite-users" style="width: calc(100% - 85px)">
			<button class="float-right btn btn-primary px-3">搜索</button>
		</div>
	</form>
    <div class="invite-user-list" id="ajaxList">

    </div>
</div>
{else/}
{volist name="list" id="v"}
<div class="invite-recommend-user bg-white overflow-auto p-3 mb-1" data-total="{$total}">
    <div class="float-left">
        <img src="{$v.avatar}" alt="" style="border-radius:50%;width: 50px;height: 50px;" />
    </div>
    <div class="float-left ml-2">
        <b> {$v.user_name} </b>
        <p class="text-muted font-9"> 最近回答过该领域问题 </p>
    </div>
    <div class="float-right">
        <a href="javascript:;" data-uid="{$v.uid}" data-invite="{$v['has_invite']}" data-id="{$question_id}" class="px-4 btn btn-primary btn-sm {if $v['has_invite']}active{else/}question-invite{/if}">{$v['has_invite'] ? '已邀请':'邀请回答'}</a>
    </div>
</div>
{/volist}
{/if}