{if !empty($topic_list)}
<div class="uk-mod bg-white sidebar-hot-topic p-3 mb-1">
	<div class="uk-mod-head">
		<p class="mod-head-title">热门用户</p>
		<a href="{:url('member/index/lists')}" class="mod-head-more" target="_blank">More >></a>
	</div>
	<div class="sidebar-topic-list">
        {volist name="people_list" id="v"}
        <dl>
            <dt>
                <a href="{$v.url}" class="uk-username" data-id="{$v.uid}"><img src="{$v['avatar']|default='/static/common/image/default-avatar.svg'}" class="rounded"></a>
            </dt>
            <dd>
                <a href="{$v.url}" class="uk-username" data-id="{$v.uid}">{$v.nick_name}</a>
                <p class="mb-0">
                    提问:{$v.question_count} &nbsp;&nbsp;获赞:{$v.agree_count}
                </p>
            </dd>
        </dl>
        {/volist}
	</div>
</div>
{/if}
