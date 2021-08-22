<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <select class="select2 form-control" id="{$form[type].name}" name="{$form[type].name}" data-value="{$form[type].value}" {$form[type].extra_attr|default=''}>
            <option value="">{$form[type].placeholder}</option>
            {volist name="form[type].options" id="option"}
            <option value="{$key}" {in name="key" value="$form[type].value"}selected{/in}>{$option}</option>
            {/volist}
        </select>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} col-md-6 col-lg-6 dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>
<script>
    $(function () {
        var option = {};
        {if !$form[type].options}
        if('{$form[type].ajax_url}' !== ''){
            // 启用ajax分页查询
            option = {
                language: "zh-CN",
                //allowClear: true,
                ajax: {
                    delay: 250, // 限速请求
                    url: "{$form[type].ajax_url}",   //  请求地址
                    dataType: 'json',
                    data: function (params) {
                        return {
                            keyWord: params.term || '',    //搜索参数
                            page: params.page || 1,        //分页参数
                            rows: params.pagesize || 10,   //每次查询10条记录
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        if (params.page == 1) {
                            data.data.unshift({id: '', name: "", text: "{$form[type].placeholder}"});
                        }
                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page) < data.last_page
                            }
                        };
                    },
                    cache: true
                }
            };
            // 默认值设置
            var defaultValue = $('#{$form[type].name}').data("value");
            if (defaultValue) {
                $.ajax({
                    type: "POST",
                    url: "{$form[type].ajax_url}",
                    data: {value:defaultValue},
                    dataType: "json",
                    success: function(data){
                        $('#{$form[type].name}').append("<option selected value='" + data.key + "'>" + data.value + "</option>");
                    }
                });
            }
        }
        {/if}
        $('#{$form[type].name}').select2(option);
    })
</script>
