<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars_decode}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <input class="form-control" type="number" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value|htmlspecialchars_decode}" {php}if(isset($form[type]['min']) && $form[type]['min'] !== ''):{/php}min="{$form[type].min}"{php}endif;{/php} {php}if(isset($form[type]['max']) && $form[type]['max'] !== ''):{/php}max="{$form[type].max}"{php}endif;{/php} {php}if(isset($form[type]['step']) && $form[type]['step'] !== ''):{/php}step="{$form[type].step}"{php}endif;{/php} {$form[type].extra_attr|raw}>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>

