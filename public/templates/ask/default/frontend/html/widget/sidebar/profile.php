<div class="uk-card uk-card-default uk-card-small uk-card-body mb-3">
	<div class="profile-details" style="margin-top: 15px">
		<div class="profile-image">
			<img src="{$user_info.avatar}" alt="">
		</div>
		<div class="profile-details-info mb-3">
			<h1> {$user_info['nick_name']} </h1>
			<p> {$user_info['signature']|default='暂无个人简介'}</p>
		</div>
		<div class="profile-meta uk-grid">
			<dl class="uk-width-1-4">
				<dt>{$user_info['question_count']}</dt>
				<dd>问题</dd>
			</dl>
			<dl class="uk-width-1-4">
				<dt>{$user_info['answer_count']}</dt>
				<dd>回答</dd>
			</dl>
			<dl class="uk-width-1-4">
				<dt>{$user_info['article_count']}</dt>
				<dd>文章</dd>
			</dl>
			<dl class="uk-width-1-4">
				<dt>{$user_info['agree_count']}</dt>
				<dd>获赞</dd>
			</dl>
		</div>
        {if $user_info['uid']!=$uid}
        <div class="uk-grid mt-3">
            <div class="uk-width-1-2">
                <a href="javascript:;" class="uk-display-block button uk-focus" data-type="user">关注</a>
            </div>
            <div class="uk-width-expand">
                <a href="javascript:;" class="uk-display-block button uk-send-inbox" data-uid="{$user_info['uid']}">私信</a>
            </div>
        </div>
        {/if}
	</div>
</div>