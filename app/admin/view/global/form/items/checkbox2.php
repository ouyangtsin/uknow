<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-8 col-md-10 col-lg-11">
        <input type="hidden" name="{$form[type].name}" value="{$form[type].value|default=''}">
        <div id="{$form[type].name}" {$form[type].extra_attr|default=''} style="background: #fff;padding: 10px"></div>
    </div>
</div>

<div style="display: none" id="{$form[type].name}Data">
    {$form[type].options}
</div>
<script>
    $(function(){
        var treeData = eval('('+$("#{$form[type].name}Data").text()+')');
        $("#{$form[type].name}").jstree({
            "core": {
                "data": treeData
            },
            "checkbox" : {
                "keep_selected_style": false,//是否默认选中
                "three_state": false,//父子级别级联选择
                "tie_selection": true,
                "cascade":'undetermined'
            },
            "plugins" : ["checkbox", "wholerow"]
        })
    });

    $("#{$form[type].name}").on('changed.jstree', function(e,data){
        $("input[name={$form[type].name}]").val(data.selected.join(','));
    })
</script>

