<div class="row dd_input_group hide {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <div class="form-group">
        <input type="hidden" name="{$form[type].name}" value="{$form[type].value|default=''}" id="{$form[type].name}" {$form[type].extra_attr|raw|default=''}>
    </div>
</div>

