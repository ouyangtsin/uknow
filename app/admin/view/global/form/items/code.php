<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <div class="col-11 col-md-11 col-lg-11">
        <label class="text-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
        <textarea id="{$form[type].name}" name="{$form[type].name}" {$form[type].extra_attr|raw}>{$form[type].value}</textarea>
    </div>
    <div class="col-{if $form[type].tips}12{else /}1{/if} dd_ts">
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>
<script>
    $(function () {
        var codeEditor = CodeMirror.fromTextArea(document.getElementById("{$form[type].name}"), {
            mode: "{$form[type].mode}",     // 编辑器语言
            theme: "{$form[type].theme}",   // 编辑器主题
            lineNumbers: true,              // 显示行号
            showCursorWhenSelecting: true,  // 文本选中时显示光标
            lineWrapping: true,             // 代码折叠
        });
        codeEditor.setSize('auto',"{$form[type].height}px");

        //提交表单获取内容
        $('.uk-ajax-form').click(function(){
            $('#{$form[type].name}').val(codeEditor.getValue());
        })
    })
</script>