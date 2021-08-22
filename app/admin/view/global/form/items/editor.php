<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <div class="col-11 col-md-10 col-lg-8">
        <label class="text-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
        {:hook('editor',['name'=>$form[type].name,'value'=>$form[type].value,'cat'=>'common'])}
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>