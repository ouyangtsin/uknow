<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <div class="col-11 col-sm-8 col-md-6 col-lg-5">
        <label class="text-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
        <div class="more_images dd_ts">
            <div id="more_images_{$form[type].name}">
                <div class="hide">
                    <input type="text" name="{$form[type].name}[]" value="">
                    <input type="text" name="{$form[type].name}_title[]" value="">
                </div>
                {notempty name="form[type].value"}
                {volist name="form[type]['value']" id="vo"}
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="{$form[type].name}[]" value="{$vo['image']}" class="form-control">
                    </div>
                    <div class="col-3">
                        <input type="text" name="{$form[type].name}_title[]" value="{$vo['title']}" class="form-control input-sm">
                    </div>
                    <div class="col-xs-3">
                        <button type="button" class="btn btn-block btn-warning remove_images">移除</button>
                    </div>
                </div>
                {/volist}
                {/notempty}
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4 col-md-6 col-lg-6 dd_ts">
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
    webupload('fileList_{$form[type].name}', 'filePicker_{$form[type].name}', '{$form[type].name}_preview', '{$form[type].name}', true, '{$config.upload_file_ext|default=""}', '{$config.upload_file_size|default="0"}', 'file');
</script>