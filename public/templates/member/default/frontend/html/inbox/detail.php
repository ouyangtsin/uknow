<div class="chats-headline">
	<div class="uk-flex">
		<img src="{$user.avatar}" style="height: 40px" class="uk-border-circle" alt="">
		<h4 class="ml-2"> {$user.name} <span>{$user['is_online'] ? '在线' : '离线'}</span> </h4>
	</div>
	<div class="message-action">
		<a href="#" uk-tooltip="filter"><i class="uil-outgoing-call"></i></a>
		<a href="#" uk-tooltip="filter"><i class="uil-video"></i></a>
		<a href="#" uk-tooltip="More"><i class="uil-ellipsis-h"></i></a>
		<div uk-dropdown="mode: click ;animation: uk-animation-slide-bottom-small">
			<ul class="uk-nav uk-dropdown-nav">
				<li><a href="#"> 刷新 </a></li>
				<li><a href="#"> 管理</a></li>
				<li><a href="#"> 设置</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="message-content-inner uk-overflow-auto" style="height: 400px">
	{if $list}
	<div class="message-time-sign">
		<span>28 June, 2018</span>
	</div>
	{volist name="list" id="v"}
	<div class="message-bubble {if $user_id==$v['user']['uid']}me{/if}">
		<div class="message-bubble-inner">
			<div class="message-avatar">
				<img src="{$v['user']['avatar']}" alt="" />
			</div>
			<div class="message-text">
				<p>{$v.message}</p>
			</div>
		</div>
		<div class="uk-clearfix"></div>
	</div>
	{/volist}
	{/if}
</div>
<div class="message-reply">
	<form class="uk-flex uk-width-1-1" action="{:url('member/inbox//send')}" method="post">
		<div class="uk-flex  mr-3 uk-width-auto">
			<a href="javascript:;" class="button primary mr-2" uk-tooltip="表情">
				<i class="uil-smile-wink"></i>
			</a>
			<a href="javascript:;" class="button primary" uk-tooltip="超链接">
				<i class="uil-link-alt"></i>
			</a>
		</div>
        <input type="hidden" name="recipient_uid" value="{$user.uid}">
		<textarea class="uk-textarea uk-width-expand uk-background-muted" name="message" rows="5" style="padding-left: 10px;padding-right: 10px" placeholder="输入您的私信内容" data-autoresize></textarea>
		<button type="submit" class="button primary uk-width-auto ajax-form"><i class="icon-send1"></i></button>
	</form>
</div>