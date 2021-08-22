<div class="row dd_input_group" id="form_group_{$form[type].name}">
    <div class="form-group">
        <div class="col-12">
            {if $form[type]['elemtype'] == "button" }
            <button class="btn btn-flat {$form[type].class|default='btn-primary'}" id="{$form[type].id|default=''}" type="button" {$form[type].data|raw|default=''} {present name="form[type].disabled"}disabled{/present}>
            <i class="{$form[type].icon|default=''}"></i> {$form[type]['title']|default=''}</button>
            {else /}
            <a class="btn btn-flat {$form[type].class|default='btn-primary'} {present name="form[type].disabled"} disabled{/present}" id="{$form[type].id|default=''}" title="{$form[type]['title']|default=''}" target="{$form[type].target|default=''}" href="{$form[type].href|default=''}" {$form[type].data|raw|default=''}><i class="{$form[type].icon|default=''}"></i> {$form[type]['title']|default=''}</a>
            {/if}
        </div>
    </div>
</div>