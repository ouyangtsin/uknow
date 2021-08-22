{if !empty($topic_list)}
<div class="uk-mod bg-white sidebar-hot-topic p-3 mb-1">
	<div class="uk-mod-head">
		<p class="mod-head-title">热门话题</p>
		<a href="{:url('topic/index')}" class="mod-head-more" target="_blank">More >></a>
	</div>
	<div class="sidebar-topic-list">
        {volist name="topic_list" id="v"}
        <dl>
            <dt>
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">
                    <img src="{$v['pic']|default='/static/common/image/topic.svg'}" class="rounded">
                </a>
            </dt>
            <dd>
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">{$v.title}</a>
                <p class="mb-0">
                    <span class="mr-2">讨论:{$v.discuss}</span><span class="mr-2">关注:{$v.focus}</span>
                </p>
            </dd>
        </dl>
        {/volist}
	</div>
</div>
{/if}
