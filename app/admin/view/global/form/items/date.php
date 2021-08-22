<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <div class="input-group date" id="reservationdate_{$form[type].name}"
             data-date-format="{$form[type].format|default='Y-m-d'}" data-target-input="nearest">
            <input class="form-control" type="text" id="{$form[type].name}" name="{$form[type].name}"
                   value="{$form[type].value|default=''}" placeholder="{$form[type].placeholder}"
                   autocomplete="off" {$form[type].extra_attr|raw}>
            <div class="input-group-append" data-target="#reservationdate_{$form[type].name}"
                 data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
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
        $('#reservationdate_{$form[type].name}').datetimepicker();
    })
</script>