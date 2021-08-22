{if $user_id && !empty($topic_list)}
<div class="uk-mod bg-white uk-sidebar-write p-3 mb-2">
	<div class="uk-mod-head">
		<p class="mod-head-title">热门话题</p>
		<a href="{:url('member/setting//profile')}" class="mod-head-more" target="_blank">More >></a>
	</div>
	<div class="mt-2 sidebar-focus-topic">
        {volist name="topic_list" id="v"}
		<a href="{:url('topic/detail',['id'=>$v['id']])}" data-id="{$v['id']}" target="_blank" class="topic-btn uk-display-inline-block mr-2 mb-2">{$v.title}</a>
        {/volist}
	</div>
    {volist name="topic_list" id="v"}
	<div class="uk-background-muted rounded uk-padding-small sidebar-topic-hover" id="topic{$v.id}" {if $key==1} style="display: none" {/if}>
		<div class="uk-overflow-hidden">
	        <a href="{:url('topic/detail',['id'=>$v['id']])}" target="_blank" class="text-primary uk-float-left">{$v.title}</a>
	        <a href="{:url('topic/detail',['id'=>$v['id']])}" target="_blank" class="icon-arrow-right uk-float-right"></a>
	    </div>
		<p class="mt-1">
			<b>{$v.question_count}</b> <span>提问</span>
			<b>{$v.article_count}</b> <span>文章</span>
		</p>
	</div>
    {/volist}
</div>
<script>
    $('.sidebar-focus-topic a').mousemove(function(){
        var topic = $(this).attr('data-id');
        $('.sidebar-topic-hover').hide();
        $('#topic'+topic).show();
    });
</script>
{/if}
