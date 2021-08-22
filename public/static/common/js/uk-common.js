/*document.write("<script language='javascript' src='/static/libs/webuploader/webuploader.js'></script>");
document.write("<script language='javascript' src='/static/libs/layui/layui.all.js'></script>");
document.write("<script language='javascript' src='/static/libs/pjax/jquery.pjax.js'></script>");*/

//全局pjax方法
if($.support.pjax)
{
    $.pjax.defaults.timeout = 1200;
    $(document).on('click', 'a[data-pjax],a[target!=_blank]', function(event) {
        let container = $(this).attr('data-pjax')
        let containerSelector = '#' + container;
        $.pjax.defaults.fragment = containerSelector;
        $.pjax.click(event, {container: containerSelector,scrollTo:container})
    })

    $(document).on('pjax:timeout', function(event) {
        event.preventDefault()
    })
}

const UK = {
    config: {
        cacheUserData: [],
        cashTopicData: [],
        card_box_hide_timer: '',
        card_box_show_timer: '',
        dropdown_list_xhr: '',
        loading_timer: '',
        loading_bg_count: 12,
        loading_mini_bg_count: 9,
        notification_timer: '',
        time: {
            waitTime: 3000
        }
    },

    api: {
        /**
         * 加载更多
         * @param element
         * @param url
         * @param data
         * @param callback
         * @param dataType
         */
        ajaxLoadMore: function (element, url, data, callback,dataType) {
            url = url ? url : $(element).data('url');
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            let isAuto =  true;
            layui.flow.load({
                isAuto: isAuto,
                elem: element,
                done: function (page, next) {
                    url = url + (url.indexOf("?") > -1 ? "&" : "?") + "page=" + page;
                    $.ajax({
                        type: data ? 'POST' : 'GET',
                        url: url,
                        data: data,
                        dataType: dataType ? dataType : '',
                        success: function (res) {
                            if (typeof (callback) != 'function') {
                                if(res.data!= undefined)
                                {
                                    var total = res.data.last_page;
                                    next(res.data.html, page < total);
                                }else{
                                    var total = $($(res)[0]).data('total');
                                    next(res, page < total);
                                }
                            } else {
                                callback(res,page, next);
                            }
                        }
                    });
                }
            });
        },

        /**
         * 发送Ajax请求
         * @param options
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
        ajax: function (options, success, error)
        {
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

        /**
         * ajax POST提交
         * @param url
         * @param data
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
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

        /**
         * ajax GET提交
         * @param url
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
        get: function (url, success, error) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                headers: {"Access-Control-Allow-Headers": "X-Requested-With"},
                success: function (ret) {
                    if (typeof success != 'function') {
                        let msg = result.msg ? result.msg : '操作成功';
                        if (result.code > 0) {
                            UK.api.success(msg, result.url)
                        } else {
                            UK.api.error(msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    UK.events.onAjaxError(ret, error);
                }
            });
        },

        /**
         * ajax表单提交
         * @param element 表单标识
         * @param success 成功回调
         */
        ajaxForm:function (element,success){
            let url = $(element).attr('action');
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: $(element).serialize(),
                success: function (result)
                {
                    if (typeof success != 'function') {
                        let msg = result.msg ? result.msg : '操作成功';
                        if (result.code > 0) {
                            UK.api.success(msg, result.url)
                        } else {
                            UK.api.error(msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        success: function (message, url)
        {
            layer.msg(message,{},function (){
                if (typeof url !== 'undefined' && url) {
                    parent.layer.closeAll() || layer.closeAll();
                    window.location.href = url;
                }
            })
        },

        error: function (message, url) {
            layer.msg(message,{},function (){
                if (typeof url !== 'undefined' && url) {
                    parent.layer.closeAll() || layer.closeAll();
                    window.location.href = url;
                }
            })
        },

        msg: function (message, url) {
            layer.msg(message,{},function (){
                if (typeof url !== 'undefined' && url) {
                    parent.layer.closeAll() || layer.closeAll();
                    window.location.href = url;
                }else{
                    window.location.reload();
                }
            })
        },

        /**
         * 打开一个弹出窗口
         * @param url
         * @param title
         * @param options
         * @returns {*}
         */
        open: function (url, title, options) {
            title = options && options.title ? options.title : (title ? title : "");
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1";
            let width = options.width ? options.width : $(window).width() > 800 ? '800px' : '95%';
            let height = options.height ? options.height : $(window).height() > 600 ? '600px' : '95%';
            let area = [width, height] ;
            let max = options.max ? true : false;
            options = $.extend({
                type: 2,
                title: title,
                shadeClose: true,
                scrollbar: false,
                shade: 0.7,
                maxmin: max,
                moveOut: true,
                area: area,
                content: url,
                success: function (layero, index) {
                    const that = this;
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

        //post方式打开
        postOpen: function (url, title, data, options) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'html',
                success: function () {
                    return UK.api.open(url, title, options);
                }
            });
        }
    },

    User:{
        /**
         * 用户赞同
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        agree:function (element,itemType,itemId) {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            let that = $(element);
            let hasClass = that.hasClass('active') ? 1 : 0;
            let voteValue = hasClass ? 0 : 1;
            $.ajax({
                url: baseUrl+'/ask/ajax/set_vote/',
                dataType: 'json',
                type: 'post',
                data: {
                    item_id: itemId,
                    item_type: itemType,
                    vote_value: voteValue
                },
                success: function (result) {
                    layer.closeAll();
                    let value = result.data.vote_value;
                    if (result.code) {
                        if (!value && hasClass) {
                            that.removeClass('active');
                        }
                        if (!value && !hasClass) {
                            that.parents('.actions').find('.uk-ajax-against').removeClass('active');
                        }
                        if (value === 1) {
                            that.addClass('active');
                            that.parents('.actions').find('.uk-ajax-against').removeClass('active');
                        }
                        that.find('span').text(result.data.agree_count);
                        layer.msg(result.msg);
                    }else{
                        layer.msg(result.msg);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.msg('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        /**
         * 用户反对
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        against:function (element,itemType,itemId){
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            let that = $(element);
            let hasClass = that.hasClass('active') ? 1 : 0;
            let voteValue = hasClass ? 0 : -1;

            $.ajax({
                url: baseUrl+'/ask/ajax/set_vote/',
                dataType: 'json',
                type: 'post',
                data: {
                    item_id: itemId,
                    item_type: itemType,
                    vote_value: voteValue
                },
                success: function (result) {
                    let value = result.data.vote_value;
                    if (result.code>0) {
                        if (!value && hasClass) {
                            that.removeClass('active');
                        }
                        if (!value && !hasClass) {
                            that.parents('.actions').find('.uk-ajax-agree').removeClass('active');
                            that.parents('.actions').find('.uk-ajax-agree').find('span').text(result.data.agree_count)
                        }
                        if (value === -1) {
                            that.addClass('active');
                            that.parents('.actions').find('.uk-ajax-agree').removeClass('active');
                            that.parents('.actions').find('.uk-ajax-agree').find('span').text(result.data.agree_count)
                        }
                        layer.msg(result.msg);
                    }else{
                        layer.msg(result.msg);
                    }
                }
            });
        },

        /**
         * 全局关注
         * @param element
         * @param type
         * @param id
         */
        focus:function (element,type,id){
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            let that = $(element);
            switch (type) {
                case 'topic':
                case 'user':
                case 'question':
                case 'column':
                case 'favorite':
                default:
                    UK.api.post(
                        baseUrl+'/ask/ajax/update_focus/',
                        {id: id, type: type},
                        function (res) {
                            if (res.code) {
                                if(that.hasClass('active'))
                                {
                                    that.removeClass('active');
                                    that.text('关注');
                                    UK.api.success('取消关注成功');
                                }else {
                                    that.addClass('active');
                                    that.text('已关注');
                                    UK.api.success('关注成功');
                                }

                                if(that.parent().find('.focus-count'))
                                {
                                    that.parent().find('.focus-count').text(res.data.count);
                                }
                            }
                    });
                    break;
            }
        },

        /**
         * 用户收藏
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        favorite:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }

            return UK.api.open(baseUrl+'/ask/ajax/favorite?item_type='+itemType+'&item_id='+itemId,'用户收藏',{});
        },

        /**
         * 感谢回答
         * @param element
         * @param itemId
         * @returns {boolean|*|jQuery}
         */
        thanks:function (element,itemId)
        {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            return UK.api.ajax(baseUrl+'/ask/question/thanks?id='+itemId,function (res){
                if(res.code)
                {
                    $(element).attr('onclick','javascript:;').addClass('active');
                    $(element).find('span').text('已感谢');
                }
                layer.msg(res.msg);
            });
        },

        /**
         * 通用不感兴趣
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean|*}
         */
        uninterested:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            return UK.api.post(baseUrl+'/ask/ajax/uninterested',{id:itemId,type:itemType},function (res){
                if(res.code)
                {
                    $(element).parent().detach();
                }
                layer.msg(res.msg);
            });
        },

        /**
         * 通用举报
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        report:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            let that = $(element);
            layer.confirm('确定要举报吗？', {}, function(){
                layer.closeAll();
                UK.api.open(baseUrl+'/ask/ajax/report?item_type='+itemType+'&item_id='+itemId,'用户举报',that.data())
            });
        },

        /**
         * 发送私信
         */
        inbox:function(user_name){
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            return UK.api.open(baseUrl+'/ask/ajax/inbox?user_name='+user_name,'与 '+user_name+ '对话', {})
        },

        /**
         * 公共邀请
         * @param element
         * @param itemId
         * @returns {boolean}
         */
        invite:function (element,itemId)
        {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }
            let that = $(element);
            layer.confirm('确定要邀请吗？', {}, function(){
                layer.closeAll();
                UK.api.open(baseUrl+'/ask/question/invite?question_id='+itemId,'邀请用户',that.data())
            });
        },

        /**
         * 保存草稿
         * @param element
         * @param item_type
         * @param itemId
         * @returns {boolean}
         */
        draft:function (element,item_type,itemId)
        {
            if (!parseInt(userId)) {
                layer.msg('请先登录后进行此操作');
                return false;
            }

            let form = $($(element).parents('form')[0]);
            let formData = {};
            let t = form.serializeArray();
            $.each(t, function() {
                formData[this.name] = this.value;
            });

            $.ajax({
                url:baseUrl + '/ask/ajax/save_draft',
                dataType: 'json',
                type:'post',
                data:{
                    data:formData,
                    item_id:itemId,
                    item_type:item_type
                },
                success: function (result)
                {
                    let msg = result.msg ? result.msg : '保存成功';
                    if(result.code> 0)
                    {
                        UK.api.success(msg)
                    }else{
                        UK.api.error(msg)
                    }
                },
                error:  function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        /**
         * 用户卡片
         * @param selector
         * @param type
         * @param time
         */
        showCard: function (selector,type, time) {
            time = time ? time : 300;
            $(document).on('mouseover', selector, function () {
                clearTimeout(UK.config.card_box_hide_timer);
                let _this = $(this);
                let ajaxBox = $('#uk-ajax-box');
                UK.config.card_box_show_timer = setTimeout(function () {
                    let uid = _this.attr('data-id');
                    let html;
                    if(type==='user')
                    {
                        let url = baseUrl+'/ask/ajax/get_user_info?uid=';
                        //判断用户id or 话题id 是否存在
                        if (uid) {
                            if (UK.config.cacheUserData.length === 0) {
                                $.get(url + uid, function (result) {
                                    let data = result.data;
                                    let focusText = data.is_focus ? '已关注' : '关注';
                                    let focusClass = data.is_focus ? 'active' : '';
                                    let signature = data.signature ? data.signature : '这家伙啥都没留下..';
                                    //动态插入盒子
                                    html = '<div id="uk-card-tips" class="uk-card-tips uk-card-tips-user">'+
                                        '<div class="uk-mod">'+
                                        '<div class="uk-mod-head" style="height: auto">'+
                                        '<a href="'+data.url+'" class="img">'+
                                        '<img src="'+data.avatar+'" alt="'+data.name+'" width="50" height="50"/>'+
                                        '</a>'+
                                        '<p class="title clearfix">'+
                                        '<a href="'+data.url+'" class="name" data-id="'+data.uid+'">'+data.name+'</a>'+
                                        '</p>'+
                                        '<p class="uk-user-center-follow-meta">'+
                                        '<span>声望: <em class="aw-text-color-green">'+data.power+'</em></span>'+
                                        '<span>赞同: <em class="aw-text-color-orange">'+data.agree_count+'</em></span>'+
                                        '</p>'+
                                        '</div>'+
                                        '<div class="uk-mod-body pb-3">'+
                                        '<p class="font-9 uk-two-line">'+signature+'</p>'+
                                        '</div>'+
                                        '<div class="uk-mod-footer clearfix">'+
                                        '<span>'+
                                        '<a class="text-muted" href="javascript:void(0)" onclick="UK.User.inbox(\''+data.user_name+'\')"><i class="icon icon-inbox"></i> 私信</a>&nbsp;&nbsp;&nbsp;&nbsp;<!--<a  class="text-color-999" href="javascript:void(0)" onclick=""><i class="icon icon-at"></i> 问Ta</a>-->'+
                                        '</span>'+
                                        '<a class="btn btn-normal btn-primary follow btn-sm float-right '+focusClass+'" href="javascript:void(0)" onclick="UK.User.focus(\'user\','+data.user_name+')"><span>'+focusText+'</span> <em>|</em> <b>'+data.fans_count+'</b></a>'+
                                        '</div>'+
                                        '</div>'+
                                        '</div>';

                                    ajaxBox.html(html).show();
                                    //判断是否为游客or自己
                                    if (userId === data.uid || data.uid < 0) {
                                        $('#uk-card-tips .uk-mod-footer').hide();
                                    }
                                    _init();
                                    //缓存
                                    if(html)
                                    {
                                        UK.config.cacheUserData.push(html);
                                    }
                                }, 'json');
                            } else {
                                let flag = 0;
                                //遍历缓存中是否含有此id的数据
                                $.each(UK.config.cacheUserData, function (i, a) {
                                    if (a.match('data-id="' + uid + '"')) {
                                        ajaxBox.html(a);
                                        $('#uk-card-tips').removeAttr('style');
                                        _init();
                                        flag = 1;
                                    }
                                });
                                if (flag === 0) {
                                    $.get(url + uid, function (result) {
                                        let data = result.data;
                                        let focusText = data.is_focus ? '已关注' : '关注';
                                        let focusClass = data.is_focus ? 'active' : '';
                                        let signature = data.signature ? data.signature : '这家伙啥都没留下..';
                                        //动态插入盒子
                                        html = '<div id="uk-card-tips" class="uk-card-tips uk-card-tips-user">'+
                                            '<div class="uk-mod">'+
                                            '<div class="uk-mod-head" style="height: auto">'+
                                            '<a href="'+data.url+'" class="img">'+
                                            '<img src="'+data.avatar+'" alt="'+data.name+'" width="50" height="50"/>'+
                                            '</a>'+
                                            '<p class="title clearfix">'+
                                            '<a href="'+data.url+'" class="name" data-id="'+data.uid+'">'+data.name+'</a>'+
                                            '</p>'+
                                            '<p class="uk-user-center-follow-meta">'+
                                            '<span>声望: <em class="aw-text-color-green">'+data.power+'</em></span>'+
                                            '<span>赞同: <em class="aw-text-color-orange">'+data.agree_count+'</em></span>'+
                                            '</p>'+
                                            '</div>'+
                                            '<div class="uk-mod-body pb-2">'+
                                            '<p class="font-9 uk-two-line">'+signature+'</p>'+
                                            '</div>'+
                                            '<div class="uk-mod-footer clearfix">'+
                                            '<span>'+
                                            '<a class="text-muted" href="javascript:void(0)" onclick="UK.User.inbox(\''+data.user_name+'\')"><i class="icon icon-inbox"></i> 私信</a>&nbsp;&nbsp;&nbsp;&nbsp;<!--<a  class="text-color-999" href="javascript:void(0)" onclick=""><i class="icon icon-at"></i> 问Ta</a>-->'+
                                            '</span>'+
                                            '<a class="btn btn-normal btn-primary follow btn-sm float-right '+focusClass+'" onclick="UK.User.focus(\'user\','+data.user_name+')"><span>'+focusText+'</span> <em>|</em> <b>'+data.fans_count+'</b></a>'+
                                            '</div>'+
                                            '</div>'+
                                            '</div>';
                                        ajaxBox.html(html).show();
                                        //判断是否为游客or自己
                                        if (userId === data.uid || data.uid < 0) {
                                            $('#uk-card-tips .uk-mod-footer').hide();
                                        }

                                        _init();
                                        //缓存
                                        if(html)
                                        {
                                            UK.config.cacheUserData.push(html);
                                        }
                                    }, 'json');
                                }
                            }
                        }
                    }

                    if(type==='topic')
                    {
                        let url = baseUrl+'/ask/ajax/get_topic_info?id=';
                        //判断用户id or 话题id 是否存在
                        if (uid) {
                            if (UK.config.cashTopicData.length === 0) {
                                $.get(url + uid, function (result) {
                                    let data = result.data;
                                    let focusText = data.is_focus ? '已关注' : '关注';
                                    let focusClass = data.is_focus ? 'active' : '';
                                    //动态插入盒子
                                    html = '<div id="uk-card-tips" class="uk-card-tips uk-card-tips-topic">'+
                                        '<div class="uk-mod">'+
                                        '<div class="uk-mod-head mb-0 border-bottom-0" style="height: auto">'+
                                        '<a href="'+data.url+'" class="img">'+
                                        '<img src="'+data.pic+'" alt="'+data.title+'" width="50" height="50" title="'+data.title+'"/>'+
                                        '</a>'+
                                        '<p class="title">'+
                                        '<a href="'+data.url+'" class="name" data-id="'+data.id+'">'+data.title+'</a>'+
                                        '</p>'+
                                        '<p class="desc font-9 uk-two-line">'+data.description+'</p>'+
                                        '</div>'+
                                        '<div class="uk-mod-footer">'+
                                        '<span>讨论数: '+data.discuss+'</span>'+
                                        '<a class="btn btn-normal btn-primary btn-sm follow '+focusClass+' float-right" onclick="UK.User.focus(this, \'topic\','+data.id+');"><span>'+focusText+'</span> <em>|</em> <b>'+data.focus+'</b></a>'+
                                        '</div>'+
                                        '</div>'+
                                        '</div>';

                                    ajaxBox.html(html).show();
                                    if (!userId)
                                    {
                                        $('#aw-card-tips .mod-footer .follow').hide();
                                    }
                                    _init();
                                    //缓存
                                    if(html)
                                    {
                                        UK.config.cashTopicData.push(html);
                                    }
                                }, 'json');
                            } else {
                                let flag = 0;
                                //遍历缓存中是否含有此id的数据
                                $.each(UK.config.cashTopicData, function (i, a) {
                                    if (a.match('data-id="' + uid + '"')) {
                                        ajaxBox.html(a);
                                        $('#uk-card-tips').removeAttr('style');
                                        _init();
                                        flag = 1;
                                    }
                                });
                                if (flag === 0) {
                                    $.get(url + uid, function (result) {
                                        let data = result.data;
                                        let focusText = data.is_focus ? '已关注' : '关注';
                                        let focusClass = data.is_focus ? 'active' : '';
                                        //动态插入盒子
                                        html = '<div id="uk-card-tips" class="uk-card-tips uk-card-tips-topic">'+
                                            '<div class="uk-mod">'+
                                            '<div class="uk-mod-head mb-0 border-bottom-0" style="height: auto">'+
                                            '<a href="'+data.url+'" class="img" >'+
                                            '<img src="'+data.pic+'" alt="'+data.title+'" width="50" height="50" title="'+data.title+'"/>'+
                                            '</a>'+
                                            '<p class="title">'+
                                            '<a href="'+data.url+'" class="name" data-id="'+data.id+'">'+data.title+'</a>'+
                                            '</p>'+
                                            '<p class="desc font-9 uk-two-line">'+data.description+'</p>'+
                                            '</div>'+
                                            '<div class="uk-mod-footer">'+
                                            '<span>讨论数: '+data.discuss+'</span>'+
                                            '<a class="btn btn-normal btn-primary btn-sm follow '+focusClass+' float-right" onclick="UK.User.focus(this, \'topic\','+data.id+');"><span>'+focusText+'</span> <em>|</em> <b>'+data.focus+'</b></a>'+
                                            '</div>'+
                                            '</div>'+
                                            '</div>';
                                        ajaxBox.html(html).show();
                                        //判断是否为游客or自己
                                        if (!userId) {
                                            $('#uk-card-tips .uk-mod-footer .follow').hide();
                                        }

                                        _init();
                                        //缓存
                                        if(html)
                                        {
                                            UK.config.cashTopicData.push(html);
                                        }
                                    }, 'json');
                                }
                            }
                        }
                    }
                    //初始化
                    function _init() {
                        let left = _this.offset().left,
                            top = _this.offset().top + _this.height() + 5,
                            nTop = _this.offset().top - $(window).scrollTop();

                        let cardBox = $('#uk-card-tips');
                        //判断下边距离不足情况
                        if (nTop + cardBox.innerHeight() > $(window).height()) {
                            top = _this.offset().top - (cardBox.innerHeight()) - 10;
                        }

                        //判断右边距离不足情况
                        if (left + cardBox.innerWidth() > $(window).width()) {
                            left = _this.offset().left - cardBox.innerWidth() + _this.innerWidth();
                        }
                        cardBox.css({left: left, top: top}).fadeIn();
                    }
                }, time);
            });

            $(document).on('mouseout', selector, function () {
                clearTimeout(UK.config.card_box_show_timer);
                UK.config.card_box_hide_timer = setTimeout(function () {
                    $('#uk-card-tips').fadeOut();
                }, 600);
            });
        },

        /**
         * 删除通知
         * @param element
         * @param id
         */
        deleteNotify:function  (element,id)
        {
            UK.api.post(baseUrl+'/member/notify/delete',{id:id},function (res){
                if(res.code) {
                    $(element).parents('dl').detach();
                    $(element).parents('.header-inbox-item').detach();
                    $('.header-notify-count').text(parseInt($('.header-notify-count').text())-1);
                }
            });
        },

        /**
         * 已读通知
         * @param element
         * @param id
         */
        readNotify:function(element,id)
        {
            UK.api.post(baseUrl+'/member/notify/read',{id:id},function (res){
                if(res.code)
                {
                    $(element).hide();
                    $(element).parents('dl').find('.uk-notify-status').removeClass('unread').addClass('read');
                    $(element).parents('.header-inbox-item').detach();
                    $('.header-notify-count').text(parseInt($('.header-notify-count').text())-1);
                }
            });
        },

        readAll:function ()
        {
            UK.api.get(baseUrl+'/member/notify/read_all',function (res){
                if(res.code)
                {
                    $('.uk-notify-status').removeClass('unread').addClass('read');
                    $('.header-inbox-item').detach();
                }
            });
        },

        share: function (title, url, desc, type) {
            let target_url;
            switch (type) {
                //分享QQ好友
                case 'qq':
                    target_url = 'http://connect.qq.com/widget/shareqq/index.html?url=' + url + '&sharesource=qzone&title=' + title + '&desc=' + desc;
                    window.open(target_url);
                    break;
                //分享新浪微博
                case 'weibo':
                    target_url = "https://service.weibo.com/share/share.php?url=" + + url + '&title=' + title;
                    window.open(target_url);
                    break;
                case 'qzone':
                    target_url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '&desc=' + desc;
                    window.open(target_url);
                    break;
            }
        },
    },

    events: {
        //请求成功的回调
        onAjaxSuccess: function (ret, onAjaxSuccess) {
            let data = typeof ret.data !== 'undefined' ? ret.data : null;
            let url = typeof ret.url !== 'undefined' ? ret.url : null;
            let msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作完成';
            if (typeof onAjaxSuccess === 'function') {
                var result = onAjaxSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            UK.api.success(msg, url);
        },
        //请求错误的回调
        onAjaxError: function (ret, onAjaxError) {
            let data = typeof ret.data !== 'undefined' ? ret.data : null;
            let url = typeof ret.url !== 'undefined' ? ret.url : null;
            let msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作完成';
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            UK.api.error(msg, url);
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            response = typeof response === 'object' ? response : JSON.parse(response);
            return response;
        }
    },

    init: function () {
        $(function () {
            //ajax获取
            $(document).on('click', '.uk-ajax-get,.ajax-get', function (e) {
                e.preventDefault();
                e.target.blur();
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
                    options.url = $(that).attr("data-url");
                }
                let success = typeof options.success === 'function' ? options.success : null;
                let error = typeof options.error === 'function' ? options.error : null;
                delete options.success;
                delete options.error;

                if(options.login && !userId)
                {
                    layer.msg('您还未登录,请登录后再操作!');
                    return false;
                }

                if (options.confirm) {
                    layer.confirm(options.confirm,{
                        btn: ['确认', '取消']
                    }, function () {
                        UK.api.ajax(options.url, success, error);
                    }, function(){
                       layer.closeAll();
                    });
                } else {
                    UK.api.ajax(options.url, success, error);
                }
            });

            //弹窗点击
            $(document).on('click', '.ajax-open,.uk-ajax-open', function (e) {
                let that = this;
                let options = $(that).data();
                let url = $(that).data("url") ? $(that).data("url") : $(that).attr('href');
                let title = $(that).attr("title") || $(that).data("title") || $(that).data('original-title');
                if(options.login && !userId)
                {
                    layer.msg('您还未登录,请登录后再操作!');
                    return false;
                }

                if (typeof options.confirm !== 'undefined') {
                    layer.confirm(options.confirm, function (index) {
                        UK.api.open(url, title, options);
                        layer.close(index);
                    });
                } else {
                    UK.api.open(url, title, options);
                }
                return false;
            });

            //post弹窗点击
            $(document).on('click', '.ajax-post-open', function (e) {
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                let url = $(that).data("url") ? $(that).data("url") : $(that).attr('href');
                let title = $(that).attr("title") || $(that).data("title") || $(that).data('original-title');
                let data = $(that).data('data') || {};

                if (typeof options.confirm !== 'undefined') {
                    layer.confirm(options.confirm, function (index) {
                        UK.api.postOpen(url, title, data, options);
                        layer.close(index);
                    });
                } else {
                    window[$(that).data("window") || 'self'].UK.api.postOpen(url, title, data, options);
                }
                return false;
            });

            //ajax表单提交带验证
            $(document).on('click', '.ajax-form', function (e) {
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                let form = $($(that).parents('form')[0]);
                let success = typeof options.success === 'function' ? options.success : null;
                
                if(!verification(form.serializeArray())){
                    return false;
                }
                delete options.success;
                delete options.error;
                $(that).attr('type', 'button');
                $.ajax({
                    url: form.attr('action'),
                    dataType: 'json',
                    type: 'post',
                    data: form.serialize(),
                    beforeSend: function(){
                        $(".ajax-form").attr({ disabled: "disabled" });
                    },
                    success: function (result) {
                        if (typeof success !== 'function') {
                            let msg = result.msg ? result.msg : '操作成功';
                            if (result.code > 0) {
                                UK.api.success(msg, result.url)
                            } else {
                                UK.api.error(msg, result.url)
                            }
                        } else {
                            success || success(result);
                        }
                    },
                    complete: function () {
                    //移除禁用
                        $(".ajax-form").removeAttr("disabled");
                    },
                    error: function (error) {
                        if ($.trim(error.responseText) !== '') {
                            layer.closeAll();
                            UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                        }
                    }
                });
                });

            //ajax表单提交不带验证
            $(document).on('click', '.uk-ajax-form', function (e) {
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                let form = $($(that).parents('form')[0]);
                let success = typeof options.success === 'function' ? options.success : null;
                delete options.success;
                delete options.error;
                $.ajax({
                    url: form.attr('action'),
                    dataType: 'json',
                    type: 'post',
                    data: form.serialize(),
                    success: function (result) {
                        if (typeof success !== 'function') {
                            let msg = result.msg ? result.msg : '操作成功';
                            if (result.code > 0) {
                                UK.api.success(msg, result.url)
                            } else {
                                UK.api.error(msg, result.url)
                            }
                        } else {
                            success || success(result);
                        }
                    },
                    error: function (error) {
                        if ($.trim(error.responseText) !== '') {
                            layer.closeAll();
                            UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                        }
                    }
                });
            });

            //ajax 通用POST提交
            $(document).on('click', '.ajax-post', function (e) {
                let target, query, form;
                let target_form = $(this).attr('data-target-form');
                let that = this;
                let need_confirm = false;
                if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('data-url'))) {
                    form = $('.' + target_form);

                    if ($(this).attr('hide-data') === 'true') { //无数据时也可以使用的功能
                        form = $('.hide-data');
                        query = form.serialize();
                    } else if (form.get(0) == undefined) {
                        return false;
                    } else if (form.get(0).nodeName == 'FORM') {
                        if ($(this).hasClass('confirm')) {
                            if (!confirm('确认要执行该操作吗?')) {
                                return false;
                            }
                        }
                        if ($(this).attr('url') !== undefined) {
                            target = $(this).attr('url');
                        } else {
                            target = form.get(0).action;
                        }
                        query = form.serialize();
                    } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                        form.each(function (k, v) {
                            if (v.type === 'checkbox' && v.checked === true) {
                                need_confirm = true;
                            }
                        });
                        if (need_confirm && $(this).hasClass('confirm')) {
                            if (!confirm('确认要执行该操作吗?')) {
                                return false;
                            }
                        }
                        query = form.serialize();

                    } else {
                        if ($(this).hasClass('confirm')) {
                            if (!confirm('确认要执行该操作吗?')) {
                                return false;
                            }
                        }
                        query = form.find('input,select,textarea').serialize();
                    }

                    $(that).addClass('disabled').attr('autocomplete', 'off').prop('disabled', true);

                    $.post(target, query).success(function (data) {
                        if (data.code === 1) {
                            UK.api.success(data.msg);
                            setTimeout(function () {
                                $(that).removeClass('disabled').prop('disabled', false);
                                if (data.url) {
                                    location.href = data.url;
                                } else if ($(that).hasClass('no-refresh')) {
                                    $('#top-alert').find('button').click();
                                } else {
                                    location.reload();
                                }
                            }, 1500);
                        } else {
                            UK.api.error(data.msg);
                            setTimeout(function () {
                                $(that).removeClass('disabled').prop('disabled', false);
                            }, 1500);
                        }
                    });
                }
                return false;
            });

            //switch
            $(document).on('click', '.uk-switch', function (e) {
                let that = this;
                let url = $(that).data('url');
                UK.api.get(url, function (res) {
                    if ($(that).attr('checked')) {
                        $(that).attr('checked', '');
                        UK.api.success(res.msg);
                    } else {
                        $(that).attr('checked', 'checked');
                        UK.api.success(res.msg);
                    }
                })
            });

            //全选的实现
            $(".check-all").click(function () {
                $(".ids").prop("checked", this.checked);
            });

            $(".ids").click(function () {
                let option = $(".ids");
                option.each(function (i) {
                    if (!this.checked) {
                        $(".check-all").prop("checked", false);
                        return false;
                    } else {
                        $(".check-all").prop("checked", true);
                    }
                });
            });

            $(document).on('click', '[data-delete]', function (t) {
                let url = this.dataset.delete
                let that = $(this);
                layer.confirm('确定要删除吗？', {}, function(){
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        success: function (res) {
                            if(res.code){
                                layer.closeAll();
                                UK.api.success(res.msg);
                                that.parents('.post-comments-single').remove();
                            }else{
                                UK.api.error(res.msg);
                            }
                        },
                    });
                });
            });

            $(document).on('click', '[data-lock]', function (t) {
                let url=this.dataset.lock
                let that=$(this);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    success: function (res) {
                        if(res.code){
                            UK.api.success(res.msg,res.url);
                        }else{
                            UK.api.error(res.msg);

                        }
                    },
                })
            });

            /*! 异步任务状态监听与展示 */
            $(document).on('click', '[data-queue]', function (action) {
                action = this.dataset.queue || '';
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
                                UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
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
        })
    },

    upload:{
        /**
         * 上传组件
         * @param listContainer 多文件内容器
         * @param filePicker 文件选择按钮
         * @param preview 图片预览显示容器
         * @param field 字段名称
         * @param more 多选上传
         * @param type 上传类型
         * @param path 上传路径
         */
        webUpload : function (filePicker, preview, field, path, type, more, listContainer) {
            type = type || 'img';
            path = path || 'common';
            let upload_allowExt,size;
            if(type==='img')
            {
                upload_allowExt = upload_image_ext.replace(/\|/g, ",");
            }else{
                upload_allowExt = upload_file_ext.replace(/\|/g, ",");
            }

            if (type==='img') {
                size = upload_image_size * 1024;
            } else {
                size = upload_file_size * 1024;
            }
            var $list = $("#" + listContainer + "");
            var GUID = WebUploader.Base.guid();                            // 一个GUID
            var uploader = WebUploader.create({
                auto: true,                                                // 选完文件后，是否自动上传。
                swf: '/static/admin/libs/webuploader-0.1.5/uploader.swf',     // 加载swf文件，路径一定要对
                server: '/api/upload/index?upload_type=' + type+'&path='+path, // 文件接收服务端
                pick: '#' + filePicker,                              // 选择文件的按钮。可选。
                resize: false,                                             // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                chunked: true,                                             // 是否分片
                chunkSize: 5 * 1024 * 1024,                                // 分片大小
                threads: 1,                                                // 上传并发数
                formData: {
                    // 由于Http的无状态特征，在往服务器发送数据过程传递一个进入当前页面是生成的GUID作为标示
                    GUID: GUID,                                            // 自定义参数
                },
                compress: false,
                fileSingleSizeLimit: size,                                 // 限制大小200M，单文件
                //fileSizeLimit: allMaxSize*1024*1024,                     // 限制大小10M，所有被选文件，超出选择不上
                accept: {
                    title: '上传图片/文件',
                    extensions: upload_allowExt,                           // 允许上传的类型 'gif,jpg,jpeg,bmp,png'
                    mimeTypes: '*',                                        // 默认全部文件，为兼容上传文件功能，如只上传图片可写成img/*
                }
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $list,
                    $percent = $li.find('.progress .progress-bar');
                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<div class="progress progress-striped active">' +
                        '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                        '</div>' +
                        '</div>').appendTo($li).find('.progress-bar');
                }
                $percent.css('width', percentage * 100 + '%');
            });

            uploader.on('uploadSuccess', function (file, response) {
                if (response.code == 0) {
                    layer.msg(response.msg);
                }
                let url = response.url;
                if (more == true) {
                    var images = '<div class="row"><div class="col-6"><input type="text" name="' + field + '[]" value="' + url + '" class="form-control"/></div> <div class="col-3"><input class="form-control input-sm" type="text" name="' + field + '_title[]" value="' + file.name + '" ></div> <div class="col-xs-3"><button type="button" class="btn btn-block btn-warning remove_images">移除</button></div></div>';
                    var images_list = $('#more_images_' + field).html();

                    $('#more_images_' + field).html(images + images_list);

                } else {
                    $("input[name='" + field + "']").val(url);
                    $("#" + preview).attr('src', url);
                    $("#" + preview).parent("a").attr('href', url);
                }
            });
            uploader.on('uploadComplete', function (file) {
                $list.find('.progress').fadeOut();
            });
            // 错误提示
            uploader.on("error", function (type) {
                if (type == "Q_TYPE_DENIED") {
                    layer.msg('请上传' + upload_allowExt + '格式的文件！');
                } else if (type == "F_EXCEED_SIZE") {
                    layer.msg('单个文件大小不能超过' + size / 1024 + 'kb！');
                } else if (type == "F_DUPLICATE") {
                    layer.msg('请不要重复选择文件');
                } else {
                    layer.msg('上传出错！请检查后重新上传！错误代码' + type);
                }
            });
        }
    },

    common:{
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

        isMobile:function () {
            let userAgentInfo = navigator.userAgent;
            let mobileAgents = [ "Android", "iPhone", "SymbianOS", "Windows Phone", "iPad","iPod"];
            let mobile_flag = false;
            //根据userAgent判断是否是手机
            for (let v = 0; v < mobileAgents.length; v++) {
                if (userAgentInfo.indexOf(mobileAgents[v]) > 0) {
                    mobile_flag = true;
                    break;
                }
            }
            let screen_width = window.screen.width;
            let screen_height = window.screen.height;
            //根据屏幕分辨率判断是否是手机
            if(screen_width < 500 && screen_height < 800){
                mobile_flag = true;
            }
            return mobile_flag;
        }
    }
};

UK.init();
window.UK = UK;