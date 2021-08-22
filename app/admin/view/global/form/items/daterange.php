<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <div class="input-group">
            <input class="form-control" type="text" id="{$form[type].name}" name="{$form[type].name}"
                   value="{$form[type].value|default=''}" placeholder="{$form[type].placeholder}"
                   autocomplete="off" {$form[type].extra_attr|raw}>
            <div class="input-group-append">
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
        $('#{$form[type].name}').daterangepicker({
            showDropdowns: true,     // 年月份下拉框
            timePicker: true,        // 显示时间
            timePicker24Hour: true,  // 时间制
            timePickerSeconds: true, // 时间显示到秒
            ranges: {
                '今天': [moment(), moment()],
                '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '上周': [moment().subtract(6, 'days'), moment()],
                '前30天': [moment().subtract(29, 'days'), moment()],
                '本月': [moment().startOf('month'), moment().endOf('month')],
                '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            //timePicker: true,
            //timePickerIncrement: 30,
            locale: {
                format: '{$form[type].format|date_friendly}',
                applyLabel: '确定',       // 确定按钮文本
                cancelLabel: '取消',      // 取消按钮文本
                customRangeLabel: '自定义',
            }
        });
    })
</script>

