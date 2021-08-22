<section class="content">
    <div class="container-fluid">
        <div class="row">
            {if $info}
            <form class="col-12" method="post" action="{:url('edit')}" submit_confirm>
                <input type="hidden" name="id" value="{$info.id}"/>
                <input type="hidden" name="old_field" value="{$info.field}"/>
            {else /}
            <form class="col-12" method="post" action="{:url('add')}" submit_confirm>
            {/if}
                <input type="hidden" name="module_id" value="{$info ? $info.module_id : $module_id}"/>
                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">所属模块</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <select id="module_id" name="module_id" class="form-control">
                            <option value=''>请选择</option>
                            {volist name="modules" id="module"}
                            <option value="{$module.id}" {$module_id == $module.id ? 'selected="selected"' : ''} >{$module.module_name} - [ {$module.table_name} ]</option>
                            {/volist}
                        </select>
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* 字段所属的模块/表</div>
                </div>
                {if $groups}
                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">所属分组</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <select id="group_id" name="group_id" class="form-control">
                            <option value='0'>请选择</option>
                            {volist name="groups" id="group"}
                            <option value="{$key}" {isset($info['group']) && $info.group == $key ? 'selected="selected"' : ''} >{$group}</option>
                            {/volist}
                        </select>
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* 字段所属分组</div>
                </div>
                {/if}

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段类型</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <select id="type" name="type" class="form-control">
                            <option value=''>请选择字段类型</option>
                            <option value="text" {if $info}{$info.type == 'text' ? 'selected="selected"' : ''}{/if}>单行文本</option>
                            <option value="icon" {if $info}{$info.type == 'icon' ? 'selected="selected"' : ''}{/if}>选择图标</option>
                            <option value="textarea" {if $info}{$info.type == 'textarea' ? 'selected="selected"' : ''}{/if}>多行文本</option>
                            <option value="radio" {if $info}{$info.type == 'radio' ? 'selected="selected"' : ''}{/if}>单选按钮</option>
                            <option value="checkbox" {if $info}{$info.type == 'checkbox' ? 'selected="selected"' : ''}{/if}>多选按钮</option>
                            <option value="date" {if $info}{$info.type == 'date' ? 'selected="selected"' : ''}{/if}>日期</option>
                            <option value="time" {if $info}{$info.type == 'time' ? 'selected="selected"' : ''}{/if}>时间</option>
                            <option value="datetime" {if $info}{$info.type == 'datetime' ? 'selected="selected"' : ''}{/if}>日期时间</option>
                            <option value="daterange" {if $info}{$info.type == 'daterange' ? 'selected="selected"' : ''}{/if}>日期范围</option>
                            <option value="tag" {if $info}{$info.type == 'tag' ? 'selected="selected"' : ''}{/if}>标签</option>
                            <option value="number" {if $info}{$info.type == 'number' ? 'selected="selected"' : ''}{/if}>数字</option>
                            <option value="password" {if $info}{$info.type == 'password' ? 'selected="selected"' : ''}{/if}>密码</option>
                            <option value="select" {if $info}{$info.type == 'select' ? 'selected="selected"' : ''}{/if}>普通下拉菜单</option>
                            <option value="select2" {if $info}{$info.type == 'select2' ? 'selected="selected"' : ''}{/if}>高级下拉菜单</option>
                            <option value="image" {if $info}{$info.type == 'image' ? 'selected="selected"' : ''}{/if}>单张图片</option>
                            <option value="images" {if $info}{$info.type == 'images' ? 'selected="selected"' : ''}{/if}>多张图片</option>
                            <option value="file" {if $info}{$info.type == 'file' ? 'selected="selected"' : ''}{/if}>单文件上传</option>
                            <option value="files" {if $info}{$info.type == 'files' ? 'selected="selected"' : ''}{/if}>多文件上传</option>
                            <option value="editor" {if $info}{$info.type == 'editor' ? 'selected="selected"' : ''}{/if}>编辑器</option>
                            <option value="hidden" {if $info}{$info.type == 'hidden' ? 'selected="selected"' : ''}{/if}>隐藏域</option>
                            <option value="color" {if $info}{$info.type == 'color' ? 'selected="selected"' : ''}{/if}>取色器</option>
                        </select>
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段名称</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="field" class="form-control" placeholder="字段英文名称，如 title"
                               value="{$info.field?$info.field:''}">
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts">* 注意不要包含空格，建议全部小写，通过_分割，如 user_name, goods_price</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段别名</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="name" class="form-control" placeholder="字段中文名称，如 标题"
                               value="{$info.name?$info.name:''}">
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts">* 建议不超过4个汉字</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">提示信息</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="tips" class="form-control" placeholder="字段右侧提示信息"
                               value="{$info.tips?$info.tips:''}">
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts"> 新增/修改页面字段右侧的提示信息</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">是否必填</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <div class="dd_radio_lable_left">
                            <label class="dd_radio_lable">
                                <input type="radio" name="required" value="1" class="dd_radio"
                                       {if $info}{$info.required==1?'checked':''}{/if}><span>是</span>
                            </label>
                            <label class="dd_radio_lable">
                                <input type="radio" name="required" value="0" class="dd_radio"
                                       {if $info}{$info.required==0?'checked':''}{else /}checked{/if}><span>否</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段展示</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <div class="dd_radio_lable_left">
                            <label class="dd_radio_lable">
                                <input type="checkbox" name="is_list" value="1" class="dd_radio" {if $info}{$info.is_list===0?'':'checked'}{else /}checked{/if}><span>列表</span></label>
                            <label class="dd_radio_lable">
                                <input type="checkbox" name="is_add" value="1" class="dd_radio" {if $info}{$info.is_add===0?'':'checked'}{else /}checked{/if}><span>添加</span></label>
                            <label class="dd_radio_lable">
                                <input type="checkbox" name="is_edit" value="1" class="dd_radio" {if $info}{$info.is_edit===0?'':'checked'}{else /}checked{/if}><span>修改</span></label>
                            <label class="dd_radio_lable">
                                <input type="checkbox" name="is_search" value="1" class="dd_radio" {if $info}{$info.is_search===0?'':'checked'}{else /}checked{/if}><span>搜索</span></label>
                            <label class="dd_radio_lable">
                                <input type="checkbox" name="is_sort" value="1" class="dd_radio" {if $info}{$info.is_sort===0?'':'checked'}{else /}checked{/if}><span>排序</span></label>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts">* 设置字段的使用场景</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">搜索类型</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="search_type" class="form-control" placeholder="请填写搜索类型"
                               value="{$info.search_type?$info.search_type:'='}">
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts"> 如：=, <>, >, <, LIKE 等表达式，如字段可被搜索则必须设置</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字符长度</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <div class="col-4 p-0 float-left">
                            <input type="text" name="minlength" class="form-control"
                                   value="{$info.minlength?$info.minlength:'0'}">
                        </div>
                        <div class="col-1 p-0 float-left line_height_38 text-center">-</div>
                        <div class="col-4 p-0 float-left">
                            <input type="text" name="maxlength" class="form-control"
                                   value="{$info.maxlength?$info.maxlength:'0'}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts"> 通常无需配置，系统会自动设置</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">数据源</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <select id="data_source" name="data_source" class="form-control">
                            <option value='0'>字段本身</option>
                            <option value="1" {if $info}{$info.data_source=='1'?'selected="selected"':''}{/if}>系统字典</option>
                            <option value="2" {if $info}{$info.data_source=='2'?'selected="selected"':''}{/if}>模型数据</option>
                        </select>
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* 通常 select|radio|checkbox 或关联了其他模型时需配置 </div>
                </div>

                <div class="row dd_input_group data_source data_source2 hide">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">关联模型</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="relation_model" class="form-control" placeholder="请填写关联的模型"
                               value="{$info.relation_model?$info.relation_model:''}">
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* 只有数据源选择<模型数据>时生效，填写完整的模型名称，如 User</div>
                </div>

                <div class="row dd_input_group data_source data_source2 hide">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">展示字段</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="relation_field" class="form-control" placeholder="请填写关联模型对应的字段"
                               value="{$info.relation_field?$info.relation_field:''}">
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* 只有数据源选择<模型数据>时生效，填写完整的字段名称，如 type_name</div>
                </div>

                <div class="row dd_input_group data_source data_source1 hide">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字典类型</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <select id="dict_code" name="dict_code" class="form-control">
                            <option value=''>请选择</option>
                            {volist name="dictTypes" id="dictTypes"}
                            <option value="{$dictTypes.name}" {if $info}{$info.dict_code == $dictTypes.name ? 'selected="selected"' : ''}{/if}>{$dictTypes.title}</option>
                            {/volist}
                        </select>
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">* 只有数据源选择<系统字典>时生效</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段设置</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <div class="dd_radio_lable_left" id="field_setup">
                            <!--ajax调用文件-->
                            <!--ajax调用结束-->
                        </div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">状态</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <div class="dd_radio_lable_left">
                            {if $info}
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="1" class="dd_radio" {$info.status ?
                                'checked' : ''}><span>显示</span>
                            </label>
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="0" class="dd_radio" {$info.status ?
                                '' : 'checked'}><span>隐藏</span>
                            </label>
                            {else /}
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="1" class="dd_radio" checked><span>显示</span>
                            </label>
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="0" class="dd_radio"><span>隐藏</span>
                            </label>
                            {/if}
                        </div>
                    </div>
                    <div class="col-1 col-md-6 col-lg-6 dd_ts">*</div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">排序</label>
                    <div class="col-7 col-md-4 col-lg-4">
                        <input type="text" name="sort" class="form-control" placeholder="请输入排序" value="{$info.sort ? $info.sort : '50'}">
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-6 col-lg-6 dd_ts"> * 排序为从小到大排序，默认为50</div>
                </div>

                <div class="row dd_input_group">
                    <div class="col-11 col-sm-8 col-md-6 col-lg-5">
                        <label class="text-label">字段备注</label>
                        <textarea class="form-control" name="remark" rows="3" placeholder="请输入字段备注">{$info.remark|default=''}</textarea>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <div class="form-group">
                        <div class="col-12 col-md-6 col-lg-5 text-center">
                            <button type="submit" class="btn btn-flat btn-primary uk-ajax-form">提 交</button>
                            <button type="button" class="btn btn-flat btn-default" onclick="javascript :history.back(-1)">返 回</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(function(){
        // 字段变更时触发
        $("#type").change(function(){
            var type = $(this).val();
            var url = "{:url('changeType')}?isajax=1&moduleId={$module_id}&type=" + type;
            field_setting(type, url);
        });

        $('#data_source').change(function (){
            $('.data_source').addClass('hide');
            var type = $(this).val();
            if(type)
            {
                $('.data_source'+type).removeClass('hide');
            }
        });
        // 编辑字段时触发
        {if $info}
        var type  = '{$info.type}';
        var field = '{$info.field}';
        var url   = "{:url('changeType')}?isajax=1&moduleId={$info.module_id}&type=" + type + "&field=" + field;
        field_setting(type, url);
        {/if}

    })
    function field_setting(type, url, data) {
        $.ajax({
            type : "POST",
            url  : url,
            data : '',
            beforeSend: function () {
                $('#field_setup').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
            },
            success: function (msg) {
                $('#field_setup').html(msg);
            }
        });
    }
</script>
