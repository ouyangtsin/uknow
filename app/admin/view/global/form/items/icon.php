<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{$form[type].title|htmlspecialchars}</label>
    <div class="col-{if $form[type].tips}8{else /}7{/if} col-md-4 col-lg-4">
        <input class="form-control uk-form-icon" type="text" name="{$form[type].name}" value="{$form[type].value}" placeholder="点击右侧选择图标" style="width: calc(100% - 70px);display: inline-block">
        <div style="display: inline-block;margin-left: 10px;height: 38px;width: 38px"><i class="{$form[type].value | default='fa fa-cogs'} uk-form-icon-select uk-ajax-open" data-url="{:url('admin/Index/icons')}" data-title="选择图标" style="font-size: 30px;cursor: pointer;height: 38px;width: 38px" ></i></div>
    </div>
    <div class="col-12 col-md-6 col-lg-6 dd_ts">
        <!--上传图片-->
        {notempty name="form[type].required"} *{/notempty}
        {notempty name="form[type].tips"} {$form[type].tips|raw}{/notempty}
    </div>
</div>