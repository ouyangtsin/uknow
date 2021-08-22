{if !empty($list)}
<div class="accordion">
    {volist name="list" id="v"}
    <div class="card">
        <div class="card-header">
            {$v.create_time|date='Y-m-d H:i:s'}
        </div>

        <div class="collapse show" >
            <div class="card-body">
                用户<a href="{:json_decode($v.user_info,1)['url']}" target="_blank">【{:json_decode($v.user_info,1)['title']}】</a>{$v.content}
                <a href="{:json_decode($v.item_info,1)['url']}" target="_blank">【{:json_decode($v.item_info,1)['title']}】</a>
            </div>
        </div>
    </div>
    {/volist}
</div>
{else/}
<p class="text-center mt-4 text-muted">
    <img src="/static/common/image/empty.svg">
    <span class="my-3 d-block  ">暂无内容</span>
</p>
{/if}