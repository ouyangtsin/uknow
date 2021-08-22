<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <div class="col-11 col-sm-8 col-md-6 col-lg-5">
        <label class="text-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars_decode}</label>
        <textarea class="form-control" id="{$form[type].name}" name="{$form[type].name}" rows="{$form[type].rows|default='3'}" placeholder="{$form[type].placeholder}" {$form[type].extra_attr|raw}>{$form[type].value|htmlspecialchars_decode}</textarea>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-sm-4 col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>