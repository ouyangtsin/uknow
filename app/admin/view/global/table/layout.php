<!--内容开始-->
<section class="content">
    <!--额外CSS代码-->
    {$extra_css|raw|default=''}
    <!--额外HTML代码-->
    {$extra_html_content_top|raw|default=''}
    <!--顶部提示开始-->
    {if $page_tips_top}
    <div class="alert alert-{$tips_type} alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p>{$page_tips_top|raw}</p>
    </div>
    {/if}
    <!--顶部提示结束-->
    <div class="container-fluid">
        <div class="row">
            {if !empty($group)}
            <div class="w-100 bg-white mb-2">
                <ul class="nav nav-tabs p-0">
                    {volist name="group" id="item" key="items_key"}
                    <li class="nav-item">
                        <a class="nav-link {if $item.active}active{/if}" href="{$item.link}">{$item.title}</a>
                    </li>
                    {/volist}
                </ul>
            </div>
            {/if}
            <!--搜索区域开始-->
            {if $search OR $page_tips_search }
            <div class="col-12 search-collapse">
                <fieldset>
                    <legend>条件选项</legend>
                    <form id="search_form">
                        <div class="select-list">
                            {notempty name="page_tips_search"}{$page_tips_search|raw}{/notempty}
                            {notempty name="search"}
                            <ul>
                                {volist name="search" id="search"}
                                <li>
                                    <label>{$search.title|default=''}： </label>
                                    {if $search.param }
                                    {if $search.data_source == 2 && ($search.type == 'text' || $search.type == 'textarea' || $search.type == 'number' || $search.type == 'hidden') }
                                    {// 模型关联且需要转换的单独处理 }
                                    <input type="text" id="search_{$search.name|default=''}" name="{$search.name|default=''}" value="{$search.default|default=''}"/>
                                    {else}
                                    <select id="search_{$search.name|default=''}" name="{$search.name|default=''}">
                                        <option value="">所有</option>
                                        {notempty name="search.param"}
                                        {volist name="search.param" id="v"}
                                        <option value="{$key}" {if ((string)$search.default == (string)$key)}selected{/if}>{$v}</option>
                                        {/volist}
                                        {/notempty}
                                    </select>
                                    {/if}
                                    {else}
                                    {if $search.type == 'date' OR $search.type == 'time' OR $search.type == 'datetime' }
                                    {// 日期类型的数据 }
                                    <input type="text" id="search_{$search.name|default=''}" name="{$search.name|default=''}" value="{$search.default|default=''}" daterange="true" autocomplete="off"/>
                                    {elseif $search.type == 'select2'}
                                    <select class="select2" id="search_{$search.name|default=''}" name="{$search.name|default=''}" data-value="{$search.default|default=''}">
                                        <option value="">所有</option>
                                    </select>
                                    <script>
                                        $(function () {
                                            var option = {};
                                            // 启用ajax分页查询
                                            option = {
                                                language: "zh-CN",
                                                //allowClear: true,
                                                ajax: {
                                                    delay: 250, // 限速请求
                                                    url: "{:url('admin/Index/select2',['id'=>$search.field_id])}",   //  请求地址
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
                                                            data.data.unshift({id: '', name: "", text: "所有"});
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
                                            var defaultValue = $("#search_{$search.name|default=''}").data("value");
                                            if (defaultValue) {
                                                $.ajax({
                                                    type: "POST",
                                                    url: "{:url('admin/Index/select2',['id'=>$search.field_id])}",
                                                    data: {value: defaultValue},
                                                    dataType: "json",
                                                    async: false,
                                                    success: function(data){
                                                        $("#search_{$search.name|default=''}").append("<option selected value='" + data.key + "'>" + data.value + "</option>");
                                                    }
                                                });
                                            }
                                            $("#search_{$search.name|default=''}").select2(option);
                                        })
                                    </script>
                                    {else}
                                    {// 其他类型的数据 }
                                    <input type="text" id="search_{$search.name|default=''}" name="{$search.name|default=''}" value="{$search.default|default=''}"/>
                                    {/if}
                                    {/if}
                                </li>
                                {/volist}
                                <li>
                                    <a class="btn btn-primary btn-rounded btn-sm" onclick="UK.table.search()"><i class="fa fa-search"></i>&nbsp;搜索</a>
                                    <a class="btn btn-warning btn-rounded btn-sm" onclick="resetPre()"><i class="fas fa-sync-alt"></i>&nbsp;重置</a>
                                    <input class="hide" type="submit" name="btnSave" value="提交" onclick="UK.table.search();return false;"/>
                                </li>
                            </ul>
                            {/notempty}
                        </div>
                    </form>
                </fieldset>
            </div>
            {/if}
            <!--列表区域开始-->
            <div class="col-sm-12 select-table table-striped">
                <div class="btn-group-sm" id="toolbar" role="group">
                {volist name="top_buttons" id="top_button"}
                <a class="{$top_button.class|default=''}" {if isset($top_button['href']) && $top_button['href']}href="{$top_button.href|default=''}"{/if}{if isset($top_button['target']) && $top_button['target']} target="{$top_button.target|default=''}"{/if}{if isset($top_button['onclick']) && $top_button['onclick']} onclick="{$top_button.onclick|default=''}"{/if} title="{$top_button.title|default=''}" {if isset($top_button['url']) && $top_button['url']} data-url="{$top_button.url|default=''}"{/if} >
                    <i class="{$top_button.icon|default=''}"></i> {$top_button.title|default=''}
                </a>
                {/volist}
                </div>
                <table id="bootstrap-table" data-mobile-responsive="true"></table>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            let columns = eval({:json_encode($columns,JSON_UNESCAPED_UNICODE)});
            let right_buttons = eval({:json_encode($right_buttons,JSON_UNESCAPED_UNICODE)});
            let list=[];
            let tmp =[]
            tmp.push({
                checkbox: true,
                formatter: function(value, row, index) {
                    if(row.checkbox_disabled =='1'){
                        return {
                            disabled : true
                        }
                    }
                }
            });
            $.each(columns, function(i, item) {
                var sortable = item.name=='{$unique_id}' ? true : false;
                var class1 = item.class==='' ? '' : item.class;
                var format;
                if (item.name == 'sort')
                {
                    format = function(value, row, index) {
                        return '<input class="form-control input-sm w_40 changeSort" type="text" value="' + value + '" data-id="' + row.{$unique_id} + '" onblur="UK.table.sort(this)">';
                    }
                }else{
                    switch (item.type){
                        case 'text':
                            format = function(value, row, index) {
                                return HTMLDecode(value);
                            }
                            break;

                        case 'icon':
                            format = function(value, row, index) {
                                return HTMLDecode(value);
                            }
                            break;

                        case 'datetime':
                            format = function(value, row, index) {
                                return changeDateFormat(value);
                            }
                            break;

                        case 'status':
                            format = function(value, row, index) {
                                if (value === 0) {
                                    return '<i class="fa fa-toggle-off text-info fa-2x cursor_pointer" onclick="UK.operate.state(\'' + row.{$unique_id} + '\',\'{:url('state')}\')"></i>';
                                } else {
                                    return '<i class="fa fa-toggle-on text-info fa-2x cursor_pointer" onclick="UK.operate.state(\'' + row.{$unique_id} + '\',\'{:url('state')}\')"></i>';
                                }
                            }
                            break;

                        case 'radio':
                            format = function(value, row, index) {
                                if (value === 0) {
                                    return '<span class="badge badge-danger">'+item.param[0]+'</span>';
                                } else if (value === 1) {
                                    return '<span class="badge badge-primary">'+item.param[1]+'</span>';
                                }else {
                                    return '<span class="badge badge-info">'+item.param[value]+'</span>';
                                }
                            }
                            break;

                        case 'tag':
                            format = function(value, row, index) {
                                return '<span class="badge badge-info">'+HTMLDecode(value)+'</span>';
                            }
                            break;

                        case 'bool':
                            format = function(value, row, index) {
                                if (value == 0) {
                                    return '<i class="fa fa-ban text-danger"></i>';
                                } else if (value == 1) {
                                    return '<i class="fa fa-check text-primary"></i>';
                                }
                            }
                            break;

                        case 'link':
                            format = function(value, row, index) {
                                var link = item.default;
                                var reg = /__(.*?)__/g;
                                while (result = reg.exec(link)) {
                                    link = link.replace(result[0], row[result[1]]);
                                }
                                // 拼接
                                link = '<a href="'+link+'" target="_blank">' + value + '</a>';
                                return link;
                            }
                            break;

                        case 'image':
                            format = function(value, row, index) {
                                if (UK.common.isNotEmpty(value)) {
                                    return '<a href="' + value + '" target="_blank"><img class="image_preview" src="' + value + '"></a>';
                                }
                            }
                            break;

                        case 'color':
                            format = function(value, row, index) {
                                if (UK.common.isNotEmpty(value)) {
                                    return '<i class="table_colorpicker" style="background: ' + value + '""></i>';
                                }
                            }
                            break;

                        case 'select':
                            format = function(value, row, index) {
                                return row[item.name];
                            }
                            break;

                        case 'select2':
                            format = function(value, row, index) {
                                return row[item.name];
                            }
                            break;

                        case 'btn':
                            format = function(value, row, index) {
                                var actions = [];
                                $.each(right_buttons, function(i2, item2) {
                                    if(item2.type == 'edit' || item2.type == 'preview'){
                                        if(item2.href){
                                            var url = item2.href;
                                            var reg = /__(.*?)__/g;
                                            // 匹配ID和email，可能为任何的其他参数，但都是 __字段__ 格式
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="'+item2.class+'" title="'+item2.title+'" target="'+item2.target+'" href="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }else{
                                            actions.push('<a class="'+item2.class+'" href="javascript:void(0)" title="'+item2.title+'" onclick="UK.operate.edit(\'' + row.{$unique_id} + '\')"><i class="'+item2.icon+'"></i>'+item2.title+'</a> ');
                                        }
                                    } else if (item2.type == 'delete'){
                                        if(item2.href){
                                            var url = item2.href;
                                            var reg = /__(.*?)__/g;
                                            // 匹配ID和email，可能为任何的其他参数，但都是 __字段__ 格式
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="'+item2.class+'" target="'+item2.target+'" title="'+item2.title+'" href="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }else{
                                            actions.push('<a class="'+item2.class+'" href="javascript:void(0)" title="'+item2.title+'" onclick="UK.operate.remove(\'' + row.{$unique_id} + '\')"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }
                                    }else {
                                        if(item2.href){
                                            var url = item2.href;
                                            var reg = /__(.*?)__/g;
                                            // 匹配ID和email，可能为任何的其他参数，但都是 __字段__ 格式
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="'+item2.class+'" target="'+item2.target+'" title="'+item2.title+'" href="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }else{
                                            var url = item2.url;
                                            var reg = /__(.*?)__/g;
                                            // 匹配ID和email，可能为任何的其他参数，但都是 __字段__ 格式
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }

                                            actions.push('<a class="'+item2.class+'" href="javascript:void(0)" title="'+item2.title+'" data-url="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }
                                    }
                                })
                                return actions.join('');
                            }
                            break;

                        default :
                            format = function(value, row, index) {
                                return HTMLDecode(value);
                            }
                    }
                }

                tmp.push({
                    field: item.name,
                    title: item.title,
                    sortable:sortable,
                    class:class1,
                    formatter:eval(format)
                })
            });
            list.push(tmp);
            var column = list.reduce(
                function(reduced,next){
                    Object.keys(next).forEach(function(key){reduced[key]=next[key];});
                    return reduced;
                }
            );

            var options = {
                uniqueId      : "{$unique_id}",         // 表格主键名称，（默认为id，如表主键不为id必须设置主键）
                url           : "{$data_url|raw}",      // 请求后台的URL
                addUrl        : "{$add_url|raw}",       // 新增的地址
                editUrl       : "{$edit_url|raw}",      // 修改的地址
                delUrl        : "{$del_url|raw}",       // 删除的地址
                exportUrl     : "{$export_url|raw}",    // 导出的地址
                sortUrl       : "{$sort_url|raw}",      // 排序的地址
                sortName      : "{$unique_id}",         // 排序列名称
                sortOrder     : "desc",                 // 排序方式  asc 或者 desc
				pagination    : {$pagination},			// 是否进行分页
                parentIdField : "{$parent_id_field}",   // 列表树模式需传递父id字段名（parent_id/pid）
				clickToSelect : true,				    // 默认false不响应，设为true则当点击此行的某处时，会自动选中此行的checkbox/radiobox
                pageSize      : "{$page_size}",         // 每页显示的行数
                columns:column
            };
            UK.table.init(options);
        });

        // 搜索
        function searchPre() {
            var data = {};
            UK.table.search('', data);
        }

        // 重置搜索
        function resetPre() {
            UK.form.reset();
        }

		//HTML反转义
		function HTMLDecode(text) { 
			var temp = document.createElement("div"); 
			temp.innerHTML = text; 
			var output = temp.innerText || temp.textContent; 
			temp = null; 
			return output; 
		}

		function getDefaultValue(value,text)
        {
            return value ? value : text;
        }

    </script>
    <!--底部提示-->
    {if $page_tips_bottom}
    <div class="alert alert-{$tips_type} alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p>{$page_tips_bottom|raw}</p>
    </div>
    {/if}

    <!--额外HTML代码-->
    {$extra_html_content_bottom|raw|default=''}

    <!--额外JS代码-->
    {$extra_js|raw|default=''}
</section>
<!--内容结束-->
