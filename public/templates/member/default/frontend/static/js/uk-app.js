$(document).ready(function ()
{
    /*用户头像提示box*/
    UK.User.showCard('.uk-username, .uk-user-img, .uk-user-name','user');

    //话题小卡片
    UK.User.showCard('.uk-topic , .uk-topic-img','topic');

    //小卡片mouseover
    $(document).on('mouseover', '#uk-card-tips', function ()
    {
        clearTimeout(UK.config.card_box_hide_timer);
        $(this).show();
    });

    //小卡片mouseout
    $(document).on('mouseout', '#uk-card-tips', function ()
    {
        $(this).hide();
    });

    //用户小卡片关注更新缓存
    $(document).on('click', '.uk-card-tips-user .follow', function ()
    {
        let uid = $(this).parents('.uk-card-tips').find('.name').attr('data-id');

        $.each(UK.config.cacheUserData, function (i, a)
        {
            if (a.match('data-id="' + uid + '"'))
            {
                if (UK.config.cacheUserData.length == 1)
                {
                    UK.config.cacheUserData = [];
                }
                else
                {
                    UK.config.cacheUserData[i] = '';
                }
            }
        });
    });

    $(".uk-search-input").focus(function(){
        $(this).css("border-color","#66AFE9");
        $(this).css('width',"295px");
        $('.uk-top-publish').hide();
        $.ajax({
            url:baseUrl+'/ask/ajax/hot_search?_ajax=1',
            type: "GET",
            dataType: "html",
            success: function (ret) {
                $('.search-dropdown').show().html(ret);
            }
        })
    }).blur(function(){
        $(this).css("border-color","#ccc");
        $(this).css('width',"225px");
        $('.uk-top-publish').show();
        $('.search-dropdown').hide();
    });

    //返回顶部
    if ($('.uk-back-top').length)
    {
        $(window).scroll(function ()
        {
            if ($(window).scrollTop() > ($(window).height() / 2))
            {
                $('.uk-back-top').fadeIn();
            }
            else
            {
                $('.uk-back-top').fadeOut();
            }
        });
    }

    $('.carousel').carousel();
    $('[data-toggle="popover"]').popover();
    $("[data-fancybox]").fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
        prevEffect : 'none',
        nextEffect : 'none',
        closeBtn  : false,
        helpers : {
            title : {
                type : 'inside'
            },
            buttons	: {}
        },
        afterLoad : function() {
            this.title = (this.index + 1) + ' / ' + this.group.length + (this.title ? ' - ' + this.title : '');
        }
    });

    /*popover自定义*/
    $('.uk-popover .popover-title').mousemove(function (){
        let that = $(this);
        let options = that.data();
        let content = options.content ? options.content : that.parent('.uk-popover').find('.popover-content').html();
        let title = options.title ? options.title : that.parent('.uk-popover').find('.popover-head').html();
        let setting = {
            trigger:'click',
            content:content,
            title:title,
            padding:false,
            multi:true,
            closeable:false,
            style:'',
            backdrop:false
        };
        that.webuiPopover($.extend({},setting,options));
    });

    //复制链接
    let clipboard = new Clipboard('.uk-clipboard');
    clipboard.on('success', function(e) {
        layer.msg('复制成功')
    });

    clipboard.on('error', function(e) {
        layer.msg('复制失败')
    });

    //用户资料设置侧边栏导航点击
    $(document).on('click','.uk-pjax-tab li',function (){
        $(this).addClass('active').siblings().removeClass('active');
    });
});
