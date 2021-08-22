{__NOLAYOUT__}
<style>
    .typeTable tr td {padding: 5px;}
    .typeTable tr td:first-child{text-align: right}
</style>
<script>
    // 修改不同`字段类型`的默认值
    $("select[name='setting[field_type]']").change(function () {
        var checkValue = $(this).val();
        if (checkValue == 'int' || checkValue == 'tinyint' || checkValue == 'smallint' || checkValue == 'mediumint') {
            $("input[name='setting[default]']").val(0);
        } else {
            $("input[name='setting[default]']").val('');
        }
    });
</script>
{switch name="type"}
{case value="text" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="textarea"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]"
                   value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]"
                   value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]"
                   value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="radio"}
<table class="typeTable" cellpadding="2" cellspacing="1" width="100%">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]"
                   value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]"
                   value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]"
                   value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>字符 VARCHAR</option>
                <option value="tinyint" {if $fieldInfo}{$fieldInfo.setting.field_type=='tinyint'?'selected':''}{/if}>整数 TINYINT</option>
                <option value="smallint" {if $fieldInfo}{$fieldInfo.setting.field_type=='smallint'?'selected':''}{/if}>整数 SMALLINT</option>
                <option value="mediumint" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumint'?'selected':''}{/if}>整数 MEDIUMINT</option>
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>整数 INT</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="checkbox"}
<table class="typeTable" cellpadding="2" cellspacing="1" width="100%">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]"
                   value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]"
                   value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]"
                   value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>字符 VARCHAR</option>
                <option value="tinyint" {if $fieldInfo}{$fieldInfo.setting.field_type=='tinyint'?'selected':''}{/if}>整数 TINYINT</option>
                <option value="smallint" {if $fieldInfo}{$fieldInfo.setting.field_type=='smallint'?'selected':''}{/if}>整数 SMALLINT</option>
                <option value="mediumint" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumint'?'selected':''}{/if}>整数 MEDIUMINT</option>
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>整数 INT</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="date"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>日期格式 <a href="https://www.php.net/manual/zh/function.date.php" target="_blank" title="点击查看php 日期格式"><i class="far fa-question-circle"></i></a></td>
        <td><input type="text" class="form-control" name="setting[format]" value="{$fieldInfo.setting.format ?: 'Y-m-d'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="time"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>时间格式 <a href="https://www.php.net/manual/zh/function.date.php" target="_blank" title="点击查看php 日期格式"><i class="far fa-question-circle"></i></a></td>
        <td>
            <input type="text" class="form-control" name="setting[format]" value="{$fieldInfo.setting.format ?: 'H:i:s'}"/>
        </td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="datetime"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>日期时间格式</td>
        <td><input type="text" class="form-control" name="setting[format]" value="{$fieldInfo.setting.format ?: 'Y-m-d H:i:s'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="daterange"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>日期格式</td>
        <td><input type="text" class="form-control" name="setting[format]" value="{$fieldInfo.setting.format ?: 'Y-m-d'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="tag" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="number"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="tinyint" {if $fieldInfo}{$fieldInfo.setting.field_type=='tinyint'?'selected':''}{/if}>tinyint</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="decimal" {if $fieldInfo}{$fieldInfo.setting.field_type=='decimal'?'selected':''}{/if}>decimal</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="password"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="select" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
            	<option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="tinyint" {if $fieldInfo}{$fieldInfo.setting.field_type=='tinyint'?'selected':''}{/if}>tinyint</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="select2" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
            	<option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="tinyint" {if $fieldInfo}{$fieldInfo.setting.field_type=='tinyint'?'selected':''}{/if}>tinyint</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="image"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ? $fieldInfo.setting.placeholder : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="images"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ? $fieldInfo.setting.placeholder : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="file"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ? $fieldInfo.setting.placeholder : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="files"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ? $fieldInfo.setting.placeholder : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="editor"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>高度</td>
        <td><input type="text" class="form-control" name="setting[height]" value="{$fieldInfo.setting.height ? $fieldInfo.setting.height : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="hidden" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="int" {if $fieldInfo}{$fieldInfo.setting.field_type=='int'?'selected':''}{/if}>int</option>
                <option value="tinyint" {if $fieldInfo}{$fieldInfo.setting.field_type=='tinyint'?'selected':''}{/if}>tinyint</option>
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
            </select>
        </td>
    </tr>
</table>
{/case}
{case value="color" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="setting[default]" value="{$fieldInfo.setting.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="setting[extra_attr]" value="{$fieldInfo.setting.extra_attr ? $fieldInfo.setting.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="setting[extra_class]" value="{$fieldInfo.setting.extra_class ? $fieldInfo.setting.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="setting[placeholder]" value="{$fieldInfo.setting.placeholder ? $fieldInfo.setting.placeholder : ''}"/></td>
    </tr>
    <tr>
        <td>字段类型</td>
        <td>
            <select class="form-control" name="setting[field_type]">
                <option value="varchar" {if $fieldInfo}{$fieldInfo.setting.field_type=='varchar'?'selected':''}{/if}>varchar</option>
                <option value="char" {if $fieldInfo}{$fieldInfo.setting.field_type=='char'?'selected':''}{/if}>char</option>
                <option value="text" {if $fieldInfo}{$fieldInfo.setting.field_type=='text'?'selected':''}{/if}>text</option>
                <option value="mediumtext" {if $fieldInfo}{$fieldInfo.setting.field_type=='mediumtext'?'selected':''}{/if}>mediumtext</option>
            </select>
        </td>
    </tr>
</table>
{/case}

{default /}
{/switch}