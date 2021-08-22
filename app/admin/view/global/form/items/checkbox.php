<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <div class="dd_radio_lable_left">
            {volist name="form[type].options" id="option"}
            <label class="dd_radio_lable">
                <input type="checkbox" name="{$form[type].name}[]" class="dd_radio" id="{$form[type].name}{$i}" value="{$key}" {in name="key" value="$form[type].value|default=''" }checked{/in} {$form[type].extra_attr|raw|default=''}>
                <span>{$option|raw}</span>
            </label>
            {/volist}
        </div>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>