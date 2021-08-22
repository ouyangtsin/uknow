<div class="bg-white p-3">
    <form method="post" class="form " action="{:url()}" role="form">
        {if isset($keyList) /}
        {volist name="keyList" id="field"}
        {if $field['type'] eq 'hidden'}
        <input type="hidden" name="{$field['name']}" value="{$info[$field['name']]|default=''}"/>
        {else/}
        <div class="form-group">
            <label class="uk-form-label">{$field['title']|htmlspecialchars} </label>
            <div class="uk-form-controls">
                <label>
                    {:widget('Form/show',array($field,$info))}
                </label>
            </div>
        </div>
        {/if}
        {/volist}
        {/if}
        <div class="form-group">
            <input type="hidden" name="id" value="{$info['id']|default=0}">
            <button class="btn btn-primary btn-sm uk-ajax-form px-4" type="submit">确 定</button>
        </div>
    </form>
</div>
