{if !isset($search_list)}
<div class="bg-white p-3" >
    <div class="form-group overflow-hidden d-flex">
        <input type="text" data-item-id="{$item_id}" data-item-type="{$item_type}" class="flex-fill form-control topic-search-input" placeholder="输入要搜索的话题">
        <!--{if $user_info['permission']['create_topic_enable']}
        <button class="btn btn-primary flex-fill" style="min-width: 120px">创建</button>
        {/if}-->
    </div>
    <!--选择保存话题-->
    <form method="post" action="" class="topic-save-form">
        <div class="mt-3 overflow-hidden topic-search-list w-100">
            {volist name="topic_list" id="v"}
            <dl class="overflow-hidden">
                <dt class="text-muted mb-2 font-9">
                    <input type="checkbox" name="tags[]" value="{$v.id}"  {if $v.is_checked}checked{/if}> {$v.title}
                </dt>
                {if isset($v['childs'])}
                <dd class="w-100 d-block">
                    {volist name="$v['childs']" id="v1"}
                    <label class="font-9 text-muted mr-2">
                        <input type="checkbox" name="tags[]" value="{$v1.id}"  {if $v1.is_checked}checked{/if}> {$v1.title}
                    </label>
                    {/volist}
                </dd>
                {/if}
            </dl>
            {/volist}
        </div>
        <button class="save-topic btn btn-primary mt-3 px-4 btn-sm" type="button">保存</button>
    </form>
</div>
{else/}
<dl class="overflow-hidden">
    <dt class="text-muted mb-2 font-9">搜索结果</dt>
    <dd class="row w-100 d-block">
        {volist name="search_list" id="v"}
        <label class="col-3">
            <input type="checkbox" {if $v.is_checked}checked{/if} name="tags[]" value="{$v.id}"> {$v.title}
        </label>
        {/volist}
    </dd>
</dl>
{/if}