<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <select class="form-control" id="{$form[type].name}" name="{$form[type].name}" {$form[type].extra_attr|default=''}>
            <option value="">{$form[type].placeholder}</option>
            {volist name="form[type].options" id="option"}
            <option value="{$key}" {if ((string)$form[type].value == (string)$key)}selected{/if}>{$option}</option>
            {/volist}
        </select>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>

