{if $list}
{volist name="list" id="v"}
<div class="mx-3 py-2" style="border-radius: 10px" data-total="{$total}">
    <span class="bg-light d-block font-8 text-center" style="margin: 1rem auto;width: 150px">{:date('Y-m-d H:i:s',$v['send_time'])}</span>
    <div class="message-bubble {if $user_id==$v['uid']}me{/if}">
        <div class="message-bubble-inner overflow-hidden">
            <div class="message-avatar">
                <a href="{$v['user']['url']}">
                    <img src="{$v['user']['avatar']}" alt="{$v['user']['name']}" />
                </a>
            </div>
            <div class="message-text">
                <p class="font-9">{$v.message}</p>
            </div>
        </div>
    </div>
</div>
{/volist}
{/if}