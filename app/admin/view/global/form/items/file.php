<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <input class="form-control" type="text" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value}" placeholder="{$form[type].placeholder}" {$form[type].extra_attr|raw}>
    </div>
    <div class="col-12 col-md-6 col-lg-6 dd_ts">
        <!--上传图片-->
        <!--用来存放item-->
        <div id="fileList_{$form[type].name}" class="uploader-list"></div>
        <div id="filePicker_{$form[type].name}"><i class="fa fa-upload m-r-10"></i>选择文件</div>
        <!--上传图片-->
        {notempty name="form[type].required"}&nbsp; *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>
<script type="text/javascript">
    webupload('fileList_{$form[type].name}', 'filePicker_{$form[type].name}', '{$form[type].name}_preview', '{$form[type].name}', false, '{$config.upload_file_ext|default=""}', '{$config.upload_file_size|default="0"}', 'file');
</script>

