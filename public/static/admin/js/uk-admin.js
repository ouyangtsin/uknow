var UK = {
    btTable: {},  // bootstrapTable

    // 表格封装处理
    table: {
        _option: {},
        // 初始化表格参数
        init: function(options) {
            // 默认参数
            const defaults = {
                id: "bootstrap-table",
                height: undefined,            // 表格的高度,一般不需要设置
                sidePagination: "server",     // server启用服务端分页client客户端分页
                sortName: "id",               // 排序列名称
                sortOrder: "desc",            // 排序方式  asc 或者 desc
                escape: true,                 // 转义HTML字符串
                pagination: true,             // 是否显示分页
                pageSize: 10,                 // 每页的记录行数
                showRefresh: true,            // 是否显示刷新按钮
                showToggle: true,             // 是否显示详细视图和列表视图的切换按钮
                showFullscreen: true,         // 是否显示全屏按钮
                showColumns: true,            // 是否显示隐藏某列下拉框
                search: false,				  // 是否显示自带的搜索框功能
                showSearchButton: false,      // 是否显示自带的搜索按钮
                pageList: [10, 25, 50, 100],  // 每页显示的数量选择
                toolbar: "toolbar",           // 自定义工具栏
                toolbarAlign: "left",         // 工具栏左对齐
                buttonsClass: "",             // 按钮样式
                showFooter: false,			  // 显示页脚
                showExport: false,			  // 显示导出按钮
                clickToSelect: false,         // 是否启用点击选中行
                fixedColumns: false,          // 是否启用固定列功能
                rowStyle: {},                 // 设置行样式
                classes: 'table table-hover', // 设置表样式
                queryParams: UK.table.queryParams,
            };
            options = $.extend(defaults, options);

            UK.table._option = options;
            UK.btTable = $('#' + options.id);
            // 初始化新事件对象的属性
            UK.table.initEvent();
            // 构建bootstrap数据
            var option = {
                url: options.url,                                   // 请求后台的URL（*）
                height: options.height,                             // 表格的高度
                sortable: true,                                     // 是否启用排序
                sortName: options.sortName,                         // 排序列名称
                sortOrder: options.sortOrder,                       // 排序方式  asc 或者 desc
                sortStable: true,                                   // 设置为 true 将获得稳定的排序
                method: 'post',                                     // 请求方式（*）
                cache: false,                                       // 是否使用缓存
                contentType: "application/json",   					// 内容类型
                dataType: 'json',                                   // 数据类型
                responseHandler: UK.table.responseHandler,           // 在加载服务器发送来的数据之前处理函数
                pagination: options.pagination,                     // 是否显示分页（*）
                paginationLoop: true,                               // 是否禁用分页连续循环模式
                sidePagination: options.sidePagination,             // server启用服务端分页client客户端分页
                pageNumber: 1,                                      // 初始化加载第一页，默认第一页
                pageSize: options.pageSize,                         // 每页的记录行数（*）
                pageList: options.pageList,                         // 可供选择的每页的行数（*）
                search: options.search,                             // 是否显示搜索框功能
                showSearchButton: options.showSearchButton,         // 是否显示检索信息
                showColumns: options.showColumns,                   // 是否显示隐藏某列下拉框
                showRefresh: options.showRefresh,                   // 是否显示刷新按钮
                showToggle: options.showToggle,                     // 是否显示详细视图和列表视图的切换按钮
                showFullscreen: options.showFullscreen,             // 是否显示全屏按钮
                showFooter: options.showFooter,                     // 是否显示页脚
                escape: options.escape,                             // 转义HTML字符串
                clickToSelect: options.clickToSelect,				// 是否启用点击选中行
                toolbar: '#' + options.toolbar,                     // 指定工作栏
                detailView: options.detailView,                     // 是否启用显示细节视图
                iconSize: 'undefined',                              // 图标大小：undefined默认的按钮尺寸 xs超小按钮sm小按钮lg大按钮
                rowStyle: options.rowStyle,                         // 通过自定义函数设置行样式
                showExport: options.showExport,                     // 是否支持导出文件
                uniqueId: options.uniqueId,                         // 唯 一的标识符
                fixedColumns: options.fixedColumns,                 // 是否启用冻结列（左侧）
                detailFormatter: options.detailFormatter,           // 在行下面展示其他数据列表
                columns: options.columns,                           // 显示列信息（*）
                classes: options.classes,                           // 设置表样式
                queryParams: options.queryParams,                   // 传递参数（*）
            };
            // 将tree合并到option[关闭分页且传递父id字段才可以看到tree]
            if (option.pagination == false && UK.common.isNotEmpty(options.parentIdField)) {
                // 构建tree
                var tree = {
                    idField: options.uniqueId,
                    treeShowField: options.uniqueId,
                    parentIdField: options.parentIdField,
                    rowStyle: function (row, index) {
                        return classes = [
                            'bg-blue',
                            'bg-green',
                            'bg-red'
                        ];
                    },
                    onPostBody: function onPostBody() {
                        var columns = UK.btTable.bootstrapTable('getOptions').columns;
                        if (columns) {
                            UK.btTable.treegrid({
                                initialState: 'collapsed',// 所有节点都折叠
                                treeColumn: 1, // 默认为第三个
                                onChange: function () {
                                    UK.btTable.bootstrapTable('resetWidth');
                                }
                            });
                        }
                    },
                };
                $.extend(option, tree);
            }
            UK.btTable.bootstrapTable(option);
        },

        // 查询条件
        queryParams: function(params) {
            var curParams = {
                // 传递参数查询参数
                pageSize: params.limit,
                page: params.offset / params.limit + 1,
                searchValue: params.search,
                orderByColumn: params.sort,
                isAsc: params.order
            };
            var currentId = UK.common.isEmpty(UK.table._option.formId) ? 'search_form' : UK.table._option.formId;
            return $.extend(curParams, UK.common.formToJSON(currentId));
        },

        // 请求获取数据后处理回调函数
        responseHandler: function(res) {
            if (typeof UK.table._option.responseHandler == "function") {
                UK.table._option.responseHandler(res);
            }
            return { rows: res.data, total: res.total };
        },

        // 初始化事件
        initEvent: function(data) {
            // 触发行点击事件 加载成功事件
            UK.btTable.on("check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table load-success.bs.table", function () {
                // 工具栏按钮控制
                var rows = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table.selectFirstColumns() : UK.table.selectColumns(UK.table._option.uniqueId);
                // 非多个禁用
                $('#' + UK.table._option.toolbar + ' .multiple').toggleClass('disabled', !rows.length);
                // 非单个禁用
                $('#' + UK.table._option.toolbar + ' .single').toggleClass('disabled', rows.length!=1);
            });
            // 绑定选中事件、取消事件、全部选中、全部取消
            UK.btTable.on("check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table", function (e, rows) {
                // 复选框分页保留保存选中数组
                var rowIds = UK.table.affectedRowIds(rows);
                if (UK.common.isNotEmpty(UK.table._option.rememberSelected) && UK.table._option.rememberSelected) {
                    func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference';
                    selectionIds = _[func](selectionIds, rowIds);
                }
            });
        },

        // 表格销毁
        destroy: function (tableId) {
            var currentId = UK.common.isEmpty(tableId) ? UK.table._option.id : tableId;
            $("#" + currentId).bootstrapTable('destroy');
        },

        // 图片预览
        imageView: function (value, height, width, target) {
            if (UK.common.isEmpty(width)) {
                width = 'auto';
            }
            if (UK.common.isEmpty(height)) {
                height = 'auto';
            }
            // blank or self
            var _target = UK.common.isEmpty(target) ? 'self' : target;
            if (UK.common.isNotEmpty(value)) {
                return UK.common.sprintf("<img class='img-circle img-xs' data-height='%s' data-width='%s' data-target='%s' src='%s'/>", width, height, _target, value);
            } else {
                return UK.common.nullToStr(value);
            }
        },

        // 搜索-默认为 search_form
        search: function(formId, data)
        {
            var currentId = UK.common.isEmpty(formId) ? 'search_form' : formId;
            var params = UK.btTable.bootstrapTable('getOptions');
            params.queryParams = function(params) {
                // 获取所有搜索的form元素
                var search = UK.common.formToJSON(currentId);

                // 如传递data则追加进search中
                if (UK.common.isNotEmpty(data)) {
                    $.each(data, function (key) {
                        search[key] = data[key];
                    });
                }
                search.pageSize = params.limit;
                search.page = params.offset / params.limit + 1;
                search.searchValue = params.search;
                search.orderByColumn = params.sort;
                search.isAsc = params.order;
                return search;
            }
            UK.btTable.bootstrapTable('refresh', params);
        },

        // 导出数据
        export: function(formId) {
            UK.modal.confirm("确定导出所有数据吗？", function() {
                var currentId = UK.common.isEmpty(formId) ? 'search_form' : formId;
                window.open(UK.table._option.exportUrl + '?' +$("#" + currentId).serialize());
            });
        },

        // 设置排序
        sort: function(obj) {
            var url = UK.table._option.sortUrl;
            var data = {"id": $(obj).data('id'), "sort": $(obj).val()};
            UK.operate.submit(url, "post", "json", data);
        },

        // 刷新表格
        refresh: function() {
            UK.btTable.bootstrapTable('refresh', {
                silent: true
            });
        },

        // 显示表格指定列
        showColumn: function(column) {
            UK.btTable.bootstrapTable('showColumn', column);
        },

        // 隐藏表格指定列
        hideColumn: function(column) {
            UK.btTable.bootstrapTable('hideColumn', column);
        },

        // 查询表格指定列值
        selectColumns: function(column) {
            var rows = $.map(UK.btTable.bootstrapTable('getSelections'), function (row) {
                return row[column];
            });
            if (UK.common.isNotEmpty(UK.table._option.rememberSelected) && UK.table._option.rememberSelected) {
                rows = rows.concat(selectionIds);
            }
            return UK.common.uniqueFn(rows);
        },

        // 获取当前页选中或者取消的行ID
        affectedRowIds: function(rows) {
            var column = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table._option.columns[1].field : UK.table._option.uniqueId;
            var rowIds;
            if ($.isArray(rows)) {
                rowIds = $.map(rows, function(row) {
                    return row[column];
                });
            } else {
                rowIds = [rows[column]];
            }
            return rowIds;
        },

        // 查询表格首列值
        selectFirstColumns: function() {
            var rows = $.map(UK.btTable.bootstrapTable('getSelections'), function (row) {
                return row[UK.table._option.columns[1].field];
            });
            if (UK.common.isNotEmpty(UK.table._option.rememberSelected) && UK.table._option.rememberSelected) {
                rows = rows.concat(selectionIds);
            }
            return UK.common.uniqueFn(rows);
        },
    },

    // 表单封装处理
    form: {
        // 表单重置
        reset: function(formId) {
            var currentId = UK.common.isEmpty(formId) ? 'search_form' : formId;
            $("#" + currentId)[0].reset();
            // 重置select2
            $('select.select2').val(null).trigger("change");
            // 刷新表格
            UK.btTable.bootstrapTable('refresh');
        },
    },

    // 弹出层封装处理
    modal: {
        // 消息提示前显示图标(通常不会单独前台调用)
        icon: function (type) {
            var icon = "";
            if (type === "warning") {
                icon = 0;
            } else if (type === "success") {
                icon = 1;
            } else if (type === "error") {
                icon = 2;
            } else {
                icon = 3;
            }
            return icon;
        },
        // 消息提示(第一个参数为内容，第二个为类型，通过类型调用不同的图标效果) [warning/success/error]
        msg: function(content, type) {
            if (type != undefined) {
                layer.msg(content, {icon: UK.modal.icon(type), time: 1500, anim: 5, shade: [0.3]});
            } else {
                layer.msg(content);
            }
        },
        // 错误消息
        msgError: function(content) {
            UK.modal.msg(content, "error");
        },
        // 成功消息
        msgSuccess: function(content) {
            UK.modal.msg(content, "success");
        },
        // 警告消息
        msgWarning: function(content) {
            UK.modal.msg(content, "warning");
        },
        // 弹出提示
        alert: function(content, type, callback) {
            layer.msg(content, {
                icon: UK.modal.icon(type),
            }, callback);
        },
        // 错误提示
        alertError: function(content, callback) {
            UK.modal.alert(content, "error", callback);
        },
        // 成功提示
        alertSuccess: function(content, callback) {
            UK.modal.alert(content, "success", callback);
        },
        // 警告提示
        alertWarning: function(content, callback) {
            UK.modal.alert(content, "warning", callback);
        },
        // 确认窗体
        confirm: function (content, callBack) {
            layer.confirm(content, {
                icon: 3,
                title: "系统提示",
                btn: ['确认', '取消']
            }, function (index) {
                layer.close(index);
                callBack(true);
            });
        },
        // 消息提示并刷新父窗体
        msgReload: function(msg, type) {
            layer.msg(msg, {
                    icon: UK.modal.icon(type),
                    time: 500,
                    shade: [0.1, '#8F8F8F']
                },
                function() {
                    UK.modal.reload();
                });
        },
        // 弹出层指定宽度
        open: function (title, url, width, height, callback) {
            // 如果是移动端，就使用自适应大小弹窗
            if (navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                width = 'auto';
                height = 'auto';
            }
            if (UK.common.isEmpty(title)) {
                title = false;
            }
            if (UK.common.isEmpty(width)) {
                width = 600;
            }
            if (UK.common.isEmpty(height)) {
                height = ($(window).height() - 50);
            }
            if (UK.common.isEmpty(callback)) {
                // 当前层索引参数（index）、当前层的DOM对象（layero）
                callback = function(index, layero) {
                    var iframeWin = layero.find('iframe')[0];
                    iframeWin.contentWindow.submitHandler(index, layero);
                }
            }
            layer.open({
                // iframe层
                type: 2,
                // 宽高
                area: [width + 'px', height + 'px'],
                // 固定
                fix: false,
                // 最大最小化
                maxmin: true,
                // 遮罩
                shade: 0.3,
                // 标题
                title: title,
                // 内容
                content: url,
                // 按钮
                btn: ['确定', '关闭'],
                // 是否点击遮罩关闭
                shadeClose: true,
                // 确定按钮回调方法
                yes: callback,
                // 右上角关闭按钮触发的回调
                cancel: function(index) {
                    return true;
                }
            });
        },

        postOpen: function (url, title, data, options) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'json',
                success: function () {
                    return UK.modal.open(title, url, options.width, options.height);
                }
            });
        },

        // 弹出层指定参数选项
        openOptions: function (options) {
            var _url    = UK.common.isEmpty(options.url)    ? "/404.html"               : options.url;
            var _title  = UK.common.isEmpty(options.title)  ? "系统窗口"                 : options.title;
            var _width  = UK.common.isEmpty(options.width)  ? "800"                     : options.width;
            var _height = UK.common.isEmpty(options.height) ? ($(window).height() - 50) : options.height;
            var _btn = ['<i class="fa fa-check"></i> 确认', '<i class="fa fa-close"></i> 关闭'];
            if (UK.common.isEmpty(options.yes)) {
                options.yes = function(index, layero) {
                    options.callBack(index, layero);
                }
            }
            layer.open({
                type: 2,
                maxmin: true,
                shade: 0.3,
                title: _title,
                fix: false,
                area: [_width + 'px', _height + 'px'],
                content: _url,
                shadeClose: UK.common.isEmpty(options.shadeClose) ? true : options.shadeClose,
                skin: options.skin,
                btn: UK.common.isEmpty(options.btn) ? _btn : options.btn,
                yes: options.yes,
                cancel: function () {
                    return true;
                }
            });
        },
        // 弹出层全屏
        openFull: function (title, url, width, height) {
            //如果是移动端，就使用自适应大小弹窗
            if (navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                width = 'auto';
                height = 'auto';
            }
            if (UK.common.isEmpty(title)) {
                title = false;
            }
            if (UK.common.isEmpty(url)) {
                url = "/404.html";
            }
            if (UK.common.isEmpty(width)) {
                width = 800;
            }
            if (UK.common.isEmpty(height)) {
                height = ($(window).height() - 50);
            }
            var index = layer.open({
                type: 2,
                area: [width + 'px', height + 'px'],
                fix: false,
                //不固定
                maxmin: true,
                shade: 0.3,
                title: title,
                content: url,
                btn: ['确定', '关闭'],
                // 弹层外区域关闭
                shadeClose: true,
                yes: function(index, layero) {
                    var iframeWin = layero.find('iframe')[0];
                    iframeWin.contentWindow.submitHandler(index, layero);
                },
                cancel: function(index) {
                    return true;
                }
            });
            layer.full(index);
        },
        // 重新加载
        reload: function () {
            parent.location.reload();
        },
        // 关闭窗体
        close: function () {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        },
    },

    // 操作封装处理
    operate: {
        // 修改信息
        edit: function(id) {
            // 当前窗口打开要修改的地址
            var url = UK.operate.editUrl(id)
            UK.request.open(url,'编辑',{});
        },

        // 修改访问的地址
        editUrl: function(id) {
            var url = "";
            if (UK.common.isNotEmpty(id)) {
                url = UK.table._option.editUrl.replace("__id__", id);
            } else {
                var id = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table.selectFirstColumns() : UK.table.selectColumns(UK.table._option.uniqueId);
                if (id.length == 0) {
                    UK.modal.alertWarning("请至少选择一条记录");
                    return;
                }
                url = UK.table._option.editUrl.replace("__id__", id);
            }
            // 获取搜索信息
            var back = UK.common.serializeRemoveNull($("#search_form").serialize());
            back = back ? '&back_url=' + encodeURIComponent(back) : '';
            return url + back;
        },

        // 添加信息
        add: function(id) {
            // 当前窗口打开要添加的地址
            var url = UK.operate.addUrl(id)
            UK.request.open(url,'添加',{});
        },

        // 添加访问的地址
        addUrl: function(id) {
            var url = UK.common.isEmpty(id) ? UK.table._option.addUrl.replace("__id__", "") : UK.table._option.addUrl.replace("__id__", id);
            // 获取搜索信息
            var back = UK.common.serializeRemoveNull($("#search_form").serialize());
            if (url.indexOf('?') != -1) {
                back = back ? '&back_url=' + encodeURIComponent(back) : '';
            } else {
                back = back ? '?back_url=' + encodeURIComponent(back) : '';
            }
            return url + back;
        },

        // 删除信息
        remove: function(id) {
            UK.modal.confirm("确定删除该条数据吗？", function() {
                var url = UK.common.isEmpty(id) ? UK.table._option.delUrl : UK.table._option.delUrl.replace("__id__", id);
                var data = { "id": id };
                UK.operate.submit(url, "post", "json", data);
            });
        },

        // 批量删除信息
        removeAll: function() {
            var rows = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table.selectFirstColumns() : UK.table.selectColumns(UK.table._option.uniqueId);
            if (rows.length === 0) {
                UK.modal.alertWarning("请至少选择一条记录");
                return;
            }
            UK.modal.confirm("确认要删除选中的" + rows.length + "条数据吗?", function() {
                var url = UK.table._option.delUrl.replace("__id__", rows.join());
                var data = { "id": rows.join() };
                UK.operate.submit(url, "post", "json", data);
            });
        },

        //自定义表格选择
        selectAll: function(url,title,type) {
            let rows = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table.selectFirstColumns() : UK.table.selectColumns(UK.table._option.uniqueId);
            if (rows.length === 0) {
                UK.modal.alertWarning("请至少选择一条记录");
                return;
            }
            UK.modal.confirm("确认要"+title+"选中的" + rows.length + "条数据吗?", function() {
                url = url.replace("__id__", rows.join());
                var data = { "id": rows.join(),"type":type};
                UK.operate.submit(url, "post", "json", data);
            });
        },

        //自定义表格弹窗选择
        selectDialog: function(url,title) {
            let rows = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table.selectFirstColumns() : UK.table.selectColumns(UK.table._option.uniqueId);
            if (rows.length === 0) {
                UK.modal.alertWarning("请至少选择一条记录");
                return;
            }
            UK.modal.confirm("确认要"+title+"选中的" + rows.length + "条数据吗?", function() {
                url = url.replace("__id__", rows.join());
                url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1&id="+rows.join();
                UK.modal.open(title,url);
            });
        },

        // 修改状态
        state: function(id, url) {
            UK.modal.confirm("确认要更改状态吗?", function () {
                var data = {"id": id};
                UK.operate.submit(url, "post", "json", data);
            });
        },

        // 数据库备份+优化+修复
        database: function(url, title) {
            var rows = UK.common.isEmpty(UK.table._option.uniqueId) ? UK.table.selectFirstColumns() : UK.table.selectColumns(UK.table._option.uniqueId);
            if (rows.length === 0) {
                UK.modal.alertWarning("请至少选择一条记录");
                return;
            }
            UK.modal.confirm("确认要" + title + "选中的" + rows.length + "条数据吗?", function () {
                var data = { "id": rows.join() };
                UK.operate.submit(url, "post", "json", data);
            });
        },

        // 提交数据
        submit: function(url, type, dataType, data, callback) {
            var config = {
                url: url,
                type: type,
                dataType: dataType,
                data: data,
                beforeSend: function () {
                    // "正在处理中，请稍后..."
                },
                success: function (result) {
                    if (typeof callback == "function") {
                        callback(result);
                    }
                    UK.operate.ajaxSuccess(result);
                }
            };
            $.ajax(config)
        },

        // 保存信息 刷新表格
        save: function(url, data, callback) {
            var config = {
                url: url,
                type: "post",
                dataType: "json",
                data: data,
                success: function(result) {
                    if (typeof callback == "function") {
                        callback(result);
                    }
                    UK.operate.successCallback(result);
                }
            };
            $.ajax(config)
        },

        // 成功回调执行事件（父窗体静默更新）
        successCallback: function(result) {
            if (result.code === 1) {
                var parent = window.parent;
                UK.modal.close();
                parent.UK.modal.msgSuccess(result.msg);
                parent.UK.table.refresh();
            } else {
                UK.modal.alertError(result.msg);
            }
        },

        // 保存结果弹出msg刷新table表格
        ajaxSuccess: function (result) {
            if (result.error === 0 || result.code === 1) {
                parent.layer.closeAll();
                UK.modal.msgSuccess(result.msg);
                UK.table.refresh();
            } else {
                UK.modal.alertError(result.msg);
            }
        },

        // 展开/折叠列表树
        treeStatus: function (result) {
            if ($('.treeStatus').hasClass('expandAll')) {
                UK.btTable.treegrid('collapseAll');
                $('.treeStatus').removeClass('expandAll')
            } else {
                UK.btTable.treegrid('expandAll');
                $('.treeStatus').addClass('expandAll')
            }
        },
    },

    // 通用方法封装处理
    common: {
        // 判断字符串是否为空
        isEmpty: function (value) {
            return value == null || this.trim(value) === "";

        },
        // 判断一个字符串是否为非空串
        isNotEmpty: function (value) {
            return !UK.common.isEmpty(value);
        },
        // 空格截取
        trim: function (value) {
            if (value == null) {
                return "";
            }
            return value.toString().replace(/(^\s*)|(\s*$)|\r|\n/g, "");
        },
        // 比较两个字符串（大小写敏感）
        equals: function (str, that) {
            return str === that;
        },
        // 比较两个字符串（大小写不敏感）
        equalsIgnoreCase: function (str, that) {
            return String(str).toUpperCase() === String(that).toUpperCase();
        },
        // 将字符串按指定字符分割
        split: function (str, sep, maxLen) {
            if (UK.common.isEmpty(str)) {
                return null;
            }
            var value = String(str).split(sep);
            return maxLen ? value.slice(0, maxLen - 1) : value;
        },
        // 字符串格式化(%s )
        sprintf: function (str) {
            var args = arguments, flag = true, i = 1;
            str = str.replace(/%s/g, function () {
                var arg = args[i++];
                if (typeof arg === 'undefined') {
                    flag = false;
                    return '';
                }
                return arg;
            });
            return flag ? str : '';
        },
        // 数组去重
        uniqueFn: function(array) {
            var result = [];
            var hashObj = {};
            for (var i = 0; i < array.length; i++) {
                if (!hashObj[array[i]]) {
                    hashObj[array[i]] = true;
                    result.push(array[i]);
                }
            }
            return result;
        },
        // 获取form下所有的字段并转换为json对象
        formToJSON: function(formId) {
            var json = {};
            $.each($("#" + formId).serializeArray(), function(i, field) {
                json[field.name] = field.value;
            });
            return json;
        },
        // pjax跳转页
        jump: function (url) {
            $.pjax({url: url, container: '.content-wrapper'})
        },
        // 序列化表单，不含空元素
        serializeRemoveNull: function (serStr) {
            return serStr.split("&").filter(function (item) {
                    var itemArr = item.split('=');
                    if(itemArr[1]){
                        return item;
                    }
                }
            ).join("&");
        },
    },

    //通用请求
    request: {
        ajax: function (options, success, error) {
            options = typeof options === 'string' ? {url: options} : options;
            options.url = options.url + (options.url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            options = $.extend({
                type: "POST",
                dataType: "json",
                success: function (ret) {
                    if (typeof success === 'function') {
                        success(ret);
                    } else {
                        UK.events.onAjaxSuccess(ret, success);
                    }
                },
                error: function (xhr) {
                    if (typeof error === 'function') {
                        error(xhr);
                    } else {
                        var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                        UK.events.onAjaxError(ret, error);
                    }
                }
            }, options);
            return $.ajax(options);
        },

        post: function (url, data, success, error) {
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            return $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function (ret) {
                    if (ret.code === 1) {
                        if (typeof success === 'function') {
                            success(ret);
                        } else {
                            UK.events.onAjaxSuccess(ret, success);
                        }
                    } else {
                        if (typeof error === 'function') {
                            error(ret);
                        } else {
                            UK.events.onAjaxError(ret, error);
                        }
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    UK.events.onAjaxError(ret, error);
                }
            });
        },

        get: function (url, success, error) {
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            return $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                headers: {"Access-Control-Allow-Headers": "X-Requested-With"},
                success: function (ret) {
                    ret = UK.events.onAjaxResponse(ret);
                    if (ret.code === 1) {
                        UK.events.onAjaxSuccess(ret, success);
                    } else {
                        UK.events.onAjaxError(ret, error);
                    }
                },
                error: function (xhr) {
                    var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    UK.events.onAjaxError(ret, error);
                }
            });
        },

        open: function (url, title, options) {
            title = options && options.title ? options.title : (title ? title : "");
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            let width = options.width ? options.width : $(window).width() > 800 ? '800px' : '95%';
            let height = options.height ? options.height : $(window).height() > 600 ? '600px' : '95%';
            let area = [width, height] ;
            options = $.extend({
                type: 2,
                title: title,
                shadeClose: true,
                shade: 0.7,
                maxmin: true,
                moveOut: true,
                area: area,
                content: url,
                success: function (layero, index) {
                    const that = this;

                    //检测弹窗是否是提示信息
                    var text = window["layui-layer-iframe" + index].document.body.innerText;
                    if(text.indexOf('"code":0') != -1 || text.indexOf('"code":1') != -1)
                    {
                        var result = JSON.parse(text);
                        parent.layer.close(index);
                        UK.modal.msg(result.msg);
                    }

                    //存储callback事件
                    $(layero).data("callback", that.callback);
                    layer.setTop(layero);
                    if ($(layero).height() > $(window).height()) {
                        //当弹出窗口大于浏览器可视高度时,重定位
                        layer.style(index, {
                            top: 0,
                            height: $(window).height()
                        });
                    }
                    layer.iframeAuto(index);
                }
            }, options ? options : {});
            return layer.open(options);
        },
    },

    events: {
        //请求成功的回调
        onAjaxSuccess: function (ret, onAjaxSuccess) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var url = typeof ret.url !== 'undefined' ? ret.url : null;
            var msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : _t('操作完成');
            if (typeof onAjaxSuccess === 'function') {
                var result = onAjaxSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            layer.msg(msg, {},function (){
                if (typeof url !== 'undefined' && url) {
                    parent.layer.closeAll() || layer.closeAll();
                    window.location.href = url;
                }else{
                    window.location.reload();
                }
            });
        },
        //请求错误的回调
        onAjaxError: function (ret, onAjaxError) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var url = typeof ret.url !== 'undefined' ? ret.url : null;
            var msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : _t('操作完成');
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            layer.msg(msg, {},function (){
                if(url)
                {
                    window.location.href = url;
                }else{
                    window.location.reload();
                }
            });
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            response = typeof response === 'object' ? response : JSON.parse(response);
            return response;
        }
    },

    lang: function () {
        var args = arguments,
            string = args[0],
            i = 1;
        string = string.toLowerCase();
        if (string.indexOf('.') !== -1 && false) {
            var arr = string.split('.');
            var current = Lang[arr[0]];
            for (var i = 1; i < arr.length; i++) {
                current = typeof current[arr[i]] != 'undefined' ? current[arr[i]] : '';
                if (typeof current != 'object')
                    break;
            }
            if (typeof current == 'object')
                return current;
            string = current;
        } else {
            string = args[0];
        }
        return string.replace(/%((%)|s|d)/g, function (m) {
            var val = null;
            if (m[2]) {
                val = m[2];
            } else {
                val = args[i];
                switch (m) {
                    case '%d':
                        val = parseFloat(val);
                        if (isNaN(val)) {
                            val = 0;
                        }
                        break;
                }
                i++;
            }
            return val;
        });
    },
};

window._t = UK.lang;
window.UK = UK;

//时间选择框hover
$(document).on('mouseover', "input[daterange='true']", function(){
    $(this).daterangepicker(
        {
            autoUpdateInput: false,  // 自动填充日期
            showDropdowns: true,     // 年月份下拉框
            timePicker: true,        // 显示时间
            timePicker24Hour: true,  // 时间制
            timePickerSeconds: true, // 时间显示到秒
            ranges: {
                '今天': [moment(), moment()],
                '昨天': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '上周': [moment().subtract(6, 'days'), moment()],
                '前30天': [moment().subtract(29, 'days'), moment()],
                '本月': [moment().startOf('month'), moment().endOf('month')],
                '上月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: "YYYY/MM/DD",
                applyLabel: '确定',       // 确定按钮文本
                cancelLabel: '取消',      // 取消按钮文本
                customRangeLabel: '自定义',
            }
        }
    ).on('cancel.daterangepicker', function(ev, picker) {
        $(this).val("");
    }).on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD')+" 至 "+picker.endDate.format('YYYY-MM-DD'));
    });
})

// 列表图片鼠标移上跟随效果（兼容ajax分页）
$(document).on('mouseover', '.image_preview', function (e) {
    var image = $(this).attr("src");
    if (image != "") {
        var zoomView = $('<img src="' + image + '" id="zoomView" />'); // 建立图片查看框
        $(this).after(zoomView);
        $("#zoomView").fadeIn(100);
        $("#zoomView").css({"top": (e.pageY - 250) + "px", "left": (e.pageX - 210) + "px"});  //注意得在CSS文件中将其设置为绝对定位
    }
})

$(document).on('mousemove', '.image_preview', function (e) {
    var image = $(this).attr("image");
    if (image != "") {
        $("#zoomView").css({"top": (e.pageY - 250) + "px", "left": (e.pageX - 210) + "px"}); //鼠标移动时及时更新图片查看框的坐标
    }
})

$(document).on('mouseout', '.image_preview', function(e){
    var image=$(this).attr("image");
    if(image!=""){
        $("#zoomView").remove();    //鼠标移出时删除之前建立的图片查看框
    }
})

// 多图删除
$(document).on('click','.remove_images',function()
{
    var remove = $(this).parent().parent();
    remove.remove();
})

// 返回顶部显示
$(window).scroll(function() {
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
        $('#totop').fadeIn(500)
    } else {
        $('#totop').fadeOut(500)
    }
});

// 返回顶部点击
$(document).on("click", '#totop', function(e) {
    // 防止打开URL
    e.preventDefault();
    $('html,body').animate({
        scrollTop: 0
    }, 300)
});

$(function () {
    // tooltip 提示
    $('[data-toggle="tooltip"]').tooltip();
    var firstNav = $("#topNav");
    //点击顶部第一级菜单栏
    firstNav.on("click", "li", function ()
    {
        firstNav.find('li').removeClass("active");
        $(this).addClass("active");
        $(".sidebar-menu > li.has-treeview").addClass("hidden");
        if($(this).hasClass("data-item"))
        {
            $('.js_left_menu_show > li.data-item').removeClass("hidden").show();
        }

        if ($(this).attr("data-url") === "javascript:;")
        {
            $('.js_left_menu_show > li.data-item').addClass("hidden").hide();
            var sonList = $(".sidebar-menu > li[data-pid='" + $(this).attr("data-id") + "']");
            sonList.removeClass("hidden");
            var sidenav;
            sidenav = $(".sidebar-menu > li[data-pid='" + $(this).attr("data-id") + "']:first > a");
            if(sidenav && sidenav.attr("href") !== "javascript:;")
            {
                window.location.href = sidenav.attr("href");
            }
        }
    });

    //点击移动端一级菜单
    if ($("ul.sidebar-menu li.active a").length > 0)
    {
        $("ul.sidebar-menu li.active a").trigger("click");
    } else {
        $("li:first > a", firstNav).trigger("click");
    }

    // 左侧菜单高亮
    $('.main-sidebar .nav .nav-treeview a.nav-link').on('click', function () {
        if($(this).attr('link') !== '#'){
            $(".main-sidebar .nav .nav-treeview a.nav-link").removeClass('active');
            $(this).addClass('active');
            $(this).parents('.nav-item').last().siblings().children('a').removeClass('active')
            $(this).parents('.nav-item').last().children('a').addClass('active')
        }

        // 小屏幕上点击左边菜单栏按钮，模拟点击 xs: 480,sm: 768,md: 992,lg: 1200
        if ($(window).width() < 992) {
            // 触发左边菜单栏按钮点击事件,关闭菜单栏
            $("[data-widget='pushmenu']").trigger('click');
        }
    });

    // 刷新后匹配当前URL和标题
    $(window).on('load', function(){
        // 获取当前页面面包导航标题
        var _title = $(".content-header").find("h1").clone();
        _title.find(':nth-child(n)').remove();
        if (_title.length>0){
            _title = _title.html().trim();
        }
        // 循环匹配
        $('.sidebar .nav-sidebar a.nav-link').each(function () {
            var _html = $(this).children('p').html().replace("|—","").replace(" ","").trim()
            if(this.href !== '#' && _html == _title){
                // 打开对应菜单
                $(this).addClass('active')
                    .closest('.nav-treeview').show()                      // 打开二级ul
                    .closest('.has-treeview').addClass('menu-open') // 打开一级li
                    .children('a.nav-link').addClass('active');        // 高亮一级a
                // 判断当前所属的是第几个
                var _index = $(this).parents('.nav-item').last().data('item')
                // 执行点击动作
                $(".js_left_menu li").eq(_index).click();
            }
        });
    });

    // tag 标签
    if ($(".tags").length > 0) {
        $('.tags').tagsInput({
            'width': 'auto',
            'height': 'auto',
            'placeholderColor': '#666666',
            'defaultText': '添加标签',
        });
    }

    //ajax获取
    $(document).on('click', '.uk-ajax-get,.ajax-get', function (e) {
        e.preventDefault();
        e.target.blur();
        var that = this;
        var options = $.extend({}, $(that).data() || {});
        if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
            options.url = $(that).attr("data-url");
        }
        var success = typeof options.success === 'function' ? options.success : null;
        var error = typeof options.error === 'function' ? options.error : null;
        delete options.success;
        delete options.error;

        if (options.confirm) {
            layer.confirm(options.confirm,{
                btn: ['确认', '取消']
            }, function () {
                UK.request.ajax(options.url, success, error);
            }, function(){
                layer.closeAll();
            });
        } else {
            UK.request.ajax(options.url, success, error);
        }
    });

    //弹窗点击
    $(document).on('click', '.uk-ajax-open', function (e) {
        e.preventDefault();
        e.target.blur();
        var that = this;
        var options = $(that).data();
        var url = $(that).data("url") ? $(that).data("url") : $(that).attr('href');
        var title = $(that).attr("title") || $(that).data("title") || $(that).data('original-title');
        if (typeof options.confirm !== 'undefined') {
            layer.confirm(options.confirm,{
                btn: ['确认', '取消']
            }, function () {
                UK.request.open(url, title, options);
            }, function(){
                layer.closeAll();
            });
        } else {
            window[$(that).data("window") || 'self'].UK.request.open(url, title, options);
        }
        return false;
    });

    //ajax表单提交不带验证
    $(document).on('click', '.uk-ajax-form', function (e) {
        var that = this;
        var options = $.extend({}, $(that).data() || {});
        var form = $($(that).parents('form')[0]);
        var success = typeof options.success === 'function' ? options.success : null;
        delete options.success;
        delete options.error;
        $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
                if (typeof success !== 'function') {
                    var msg = result.msg ? result.msg : '操作成功';
                    if (result.code > 0) {
                        layer.msg(msg, {},function (){
                            layer.closeAll();
                            window.parent.layer.closeAll();
                            return result.url ? window.parent.location.href = result.url :  window.parent.location.reload();
                        })
                    } else {
                        layer.msg(msg, {},function (){})
                    }
                } else {
                    success || success(result);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    layer.closeAll();
                    layer.msg('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });
})
// 转换日期格式(时间戳转换为datetime格式)
function changeDateFormat(value) {
    if (value == '') {
        return '-';
    }
    if(value != null && value != undefined){
        if (value.toString().indexOf("-") >= 0) {
            return value;
        }
    }
    var dateVal = value * 1000;
    if (value != null) {
        var date = new Date(dateVal);
        var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
        var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();

        var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
        var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
        var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

        return date.getFullYear() + "-" + month + "-" + currentDate + " " + hours + ":" + minutes + ":" + seconds;
    }
}

/*! 异步任务状态监听与展示 */
$(document).on('click', '.do-queue', function (action) {
    action = this.dataset.url || '';
    if (action.length < 1) return $.msg.tips('任务地址不能为空！');
    this.doRuntime = function (index) {
        $.ajax({
            url: action,
            dataType: 'json',
            type: 'post',
            success: function (result) {
                if (result.code > 0) {
                    return $.loadQueue(result.data, true), false;
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    layer.closeAll();
                    layer.msg('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
        layer.close(index);
    };
    this.dataset.confirm ? layer.confirm(this.dataset.confirm, this.doRuntime) : this.doRuntime(0);
});

$.loadQueue = function (code, doScript, doAjax) {
    layer.open({
        type: 1, title: false, area: ['560px', '315px'], anim: 2, shadeClose: false, end: function () {
            doAjax = false;
        }, content: '' +
            '<div class="padding-30 padding-bottom-0" style="width:100%;height:100%;" data-queue-load="' + code + '">' +
            '   <div class="layui-elip nowrap" data-message-title></div>' +
            // '   <div class="margin-top-15 layui-progress layui-progress-big" lay-showPercent="yes"><div class="layui-progress-bar transition" lay-percent="0.00%"></div></div>' +
            '   <div class="margin-top-15" style="height:90%;"><textarea class="layui-textarea layui-bg-black border-0" disabled style="resize:none;overflow:hidden;height:100%"></textarea></div>' +
            '</div>'
    });
    (function loadProcess(code, that) {
        that = this, this.$box = $('[data-queue-load=' + code + ']');
        if (doAjax === false || that.$box.length < 1) return false;
        this.$area = that.$box.find('textarea'), this.$title = that.$box.find('[data-message-title]');
        this.$percent = that.$box.find('.layui-progress div'), this.runCache = function (code, index, value) {
            this.ckey = code + '_' + index, this.ctype = 'admin-queue-script';
            return value !== undefined ? layui.data(this.ctype, {
                key: this.ckey,
                value: value
            }) : layui.data(this.ctype)[this.ckey] || 0;
        };
        this.setState = function (status, message) {
            if (message.indexOf('javascript:') === -1) if (status === 1) {
                that.$title.html('<b class="color-text">' + message + '</b>').addClass('text-center');
                that.$percent.addClass('layui-bg-blue').removeClass('layui-bg-green layui-bg-red');
            } else if (status === 2) {
                if (message.indexOf('>>>') > -1) {
                    that.$title.html('<b class="color-blue">' + message + '</b>').addClass('text-center');
                } else {
                    that.$title.html('<b class="color-blue">正在处理：</b>' + message).removeClass('text-center');
                }
                that.$percent.addClass('layui-bg-blue').removeClass('layui-bg-green layui-bg-red');
            } else if (status === 3) {
                that.$title.html('<b class="color-green">' + message + '</b>').addClass('text-center');
                that.$percent.addClass('layui-bg-green').removeClass('layui-bg-blue layui-bg-red');
            } else if (status === 4) {
                that.$title.html('<b class="color-red">' + message + '</b>').addClass('text-center');
                that.$percent.addClass('layui-bg-red').removeClass('layui-bg-blue layui-bg-green');
            }
        };
        UK.api.post('/api/queue/progress', {code: code}, function (ret) {
            if (ret.code) {
                that.lines = [];
                for (this.lineIndex in ret.data.history) {
                    this.line = ret.data.history[this.lineIndex], this.percent = '[ ' + this.line.progress + '% ] ';
                    if (this.line.message.indexOf('javascript:') === -1) {
                        that.lines.push(this.line.message.indexOf('>>>') > -1 ? this.line.message : this.percent + this.line.message);
                    } else if (!that.runCache(code, this.lineIndex) && doScript !== false) {
                        that.runCache(code, this.lineIndex, 1), location.href = this.line.message;
                        // 跳转
                        var str = this.line.message;
                        str = str.replace('>>> javascript:', '');
                        str = str.replace(' <<<', '');
                        location.href = str;
                    }
                }
                that.$area.val(that.lines.join("\n")), that.$area.animate({scrollTop: that.$area[0].scrollHeight + 'px'}, 200);
                that.$percent.attr('lay-percent', (parseFloat(ret.data.progress || '0.00').toFixed(2)) + '%');
                if (ret.data.status > 0) that.setState(parseInt(ret.data.status), ret.data.message);
                else return that.setState(4, '获取任务详情失败！'), false;
                if (parseInt(ret.data.status) === 3 || parseInt(ret.data.status) === 4) return false;
                return setTimeout(function () {
                    loadProcess(code);
                }, Math.floor(Math.random() * 200)), false;
            }
        });
    })(code)
};