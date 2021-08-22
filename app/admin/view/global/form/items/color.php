<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <div class="input-group {$form[type].name}-colorpicker">
            <input class="form-control" type="text" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value}" placeholder="{$form[type].placeholder}" {$form[type].extra_attr|raw}>
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fas fa-square" {if $form[type].value}style="color: {$form[type].value}"{/if}></i>
                </span>
            </div>
        </div>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>

<script>
    $(function () {
        $('.{$form[type].name}-colorpicker').colorpicker()
        $('.{$form[type].name}-colorpicker').on('colorpickerChange', function(event) {
            $('.{$form[type].name}-colorpicker .fa-square').css('color', event.color.toString());
        });
    })
</script>