{volist name="data" id="v"}
<div class="col-12 mb-2">
    <dl class="clearfix position-relative p-2 border rounded">
        <dt class="float-left mr-2">
            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">
                <img src="{$v['pic']|default='/static/common/image/topic.svg'}" height="45" width="45">
            </a>
        </dt>
        <dd class="mb-0">
            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">{$v.title}</a>
            <p class="mb-0">
                <span class="mr-2">讨论:{$v.discuss}</span><span class="mr-2">关注:{$v.focus}</span>
            </p>
        </dd>
        <dd class="position-absolute" style="right: 5px;bottom: 5px">
            <a href="javascript:;" class="cursor-pointer btn btn-sm btn-primary px-3 {$v['is_focus'] ? 'active' : ''}" onclick="UK.User.focus(this,'topic','{$v.id}')" >{$v['is_focus'] ? '已关注' : '关注'}</a>
        </dd>
    </dl>
</div>
{/volist}