<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars_decode}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        {notempty name="form[type].group"}
        <div class="input-group">
        {/notempty}
            {notempty name="form[type].group.0"}
            <div class="input-group-prepend">
                <span class="input-group-text">{$form[type].group.0|raw}</span>
            </div>
            {/notempty}
            <input class="form-control" type="text" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value|htmlspecialchars_decode}" placeholder="{$form[type].placeholder|htmlspecialchars_decode}" {$form[type].extra_attr|raw}>
            {notempty name="form[type].group.1"}
            <div class="input-group-append">
                <span class="input-group-text">{$form[type].group.1|raw}</span>
            </div>
            {/notempty}
        {notempty name="form[type].group"}
        </div>
        {/notempty}
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>

