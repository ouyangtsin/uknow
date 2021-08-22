<ul class="nav nav-tabs" id="builder-form-group-tab" role="tablist">
    {volist name="form.options" id="item" key="items_key"}
    <li class="nav-item">
        <a class="nav-link {eq name="items_key" value="1" }active{/eq}" id="nav-tab-{$items_key}" href="#nav-tab-content-{$items_key}" role="tab" data-toggle="tab">{:lang($key)}</a>
    </li>
    {/volist}
</ul>
<div class="tab-content no-padding">
    {volist name="form.options" id="items" key="items_key"}
    <div class="tab-pane {eq name="items_key" value="1"}active{/eq}" id="nav-tab-content-{$items_key}" >
    {volist name="items" id="form_group"}
        {switch name="form_group.type"}
            {case value="text"}
                {// 单行文本框 }
                {include file="global/form/items/text" type="_group" /}
            {/case}
            {case icon}
                {// 图标 }
                {include file="global/form/items/icon" type='_group' /}
            {/case}
            {case value="textarea"}
                {// 多行文本框 }
                {include file="global/form/items/textarea" type="_group" /}
            {/case}
            {case value="radio"}
                {// 单选 }
                {include file="global/form/items/radio" type="_group" /}
            {/case}
            {case value="checkbox"}
                {// 多选 }
                {include file="global/form/items/checkbox" type="_group" /}
            {/case}

            {case value="checkbox2"}
                {// 树形多选 }
                {include file="global/form/items/checkbox2" type='_group' /}
            {/case}

            {case value="date"}
                {// 日期 }
                {include file="global/form/items/date" type="_group" /}
            {/case}
            {case value="time"}
                {// 时间 }
                {include file="global/form/items/time" type="_group" /}
            {/case}
            {case value="datetime"}
                {// 日期时间 }
                {include file="global/form/items/datetime" type="_group" /}
            {/case}
            {case value="daterange"}
                {// 日期范围 }
                {include file="global/form/items/daterange" type="_group" /}
            {/case}
            {case value="tags"}
                {// 标签 }
                {include file="global/form/items/tags" type="_group" /}
            {/case}
            {case value="number"}
                {// 数字 }
                {include file="global/form/items/number" type="_group" /}
            {/case}
            {case value="password"}
                {// 密码 }
                {include file="global/form/items/password" type="_group" /}
            {/case}
            {case value="select"}
                {// 下拉菜单 }
                {include file="global/form/items/select" type="_group" /}
            {/case}
            {case value="select2"}
                {// 下拉菜单2 }
                {include file="global/form/items/select2" type="_group" /}
            {/case}
            {case value="image"}
                {// 单图片 }
                {include file="global/form/items/image" type="_group" /}
            {/case}
            {case value="images"}
                {// 多图片 }
                {include file="global/form/items/images" type="_group" /}
            {/case}
            {case value="file"}
                {// 单文件 }
                {include file="global/form/items/file" type="_group" /}
            {/case}
            {case value="files"}
                {// 多文件 }
                {include file="global/form/items/files" type="_group" /}
            {/case}
            {case value="editor"}
                {// 编辑器 }
                {include file="global/form/items/editor" type="_group" /}
            {/case}
            {case value="editor"}
                {// 按钮 }
                {include file="global/form/items/button" type="_group" /}
            {/case}
            {case value="hidden"}
                {// 隐藏域 }
                {include file="global/form/items/hidden" type="_group" /}
            {/case}
            {case value="html"}
                {// 自定义html }
                {include file="global/form/items/html" type="_group" /}
            {/case}
            {case value="color"}
                {// 取色器 }
                {include file="global/form/items/color" type="_group" /}
            {/case}
            {case value="code"}
                {// 代码编辑器 }
                {include file="global/form/items/code" type='_group' /}
            {/case}
            {case value="array"}

                 {include file="global/form/items/array" type='_group' /}
            {/case}

            {default/}

        {/switch}
    {/volist}
</div>
{/volist}
</div>
