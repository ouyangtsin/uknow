{if isset($list)}
{volist name="list" id="v"}
<div class="mb-0 py-2 px-3 header-inbox-item overflow-hidden position-relative cursor-pointer text-left">
    <p class="text-primary">{$v['subject']}</p>
    <p class="text-color-info font-9 mt-1 uk-two-line">{$v.message|raw}</p>
    <p class="font-8">
        {if !$v['read_flag']}
        <a href="javascript:;" onclick="UK.User.readNotify(this,{$v.id})" class="text-color-info">标记已读</a>
        {/if}
        <a href="javascript:;" onclick="UK.User.deleteNotify(this,{$v.id})" class="ml-2 text-color-info"><i class="icon-delete"></i> 删除</a>
    </p>
</div>
{/volist}
{/if}