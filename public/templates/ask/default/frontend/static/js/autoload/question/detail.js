/**
 * 页面滚动自适应
 */
$(window).scroll(function ()
{
    if(!UK.common.isMobile())
    {
        if ($(window).scrollTop() >= ($(window).height() / 3))
        {
            $('#uk-question-fixed').fadeIn().show();
        }
        else
        {
            $('#uk-question-fixed').fadeOut().hide();
        }
    }

    let questionHeight = $('.uk-question-container').height();
    if(questionHeight>$(window).height() && $(window).scrollTop()+($(window).height() / 2) < questionHeight)
    {
        $('.uk-question-container .actions').addClass('fixed-bottom').addClass('bg-white').addClass('container').addClass('p-3');
    }else{
        $('.uk-question-container .actions').removeClass('fixed-bottom').removeClass('bg-white').removeClass('container').removeClass('p-3');
    }
});

/**
 * 回答编辑器
 */
$(document).on('click', '.uk-answer-editor', function (e) {
    if(!userId)
    {
        layer.msg('您还未登录,请先登录');
    }
    let questionId = $(this).data('question-id');
    let answerId = $(this).data('answer-id');
    let answerEditorContainer = $('#answerEditor');
    answerEditorContainer.toggle();
    $.ajax({
        url: baseUrl+'/ask/question/editor?_ajax=1',
        dataType: '',
        type: 'post',
        data: {
            question_id: questionId,
            answer_id: answerId,
        },
        success: function (result) {
            answerEditorContainer.html(result);
            if(!answerEditorContainer.is(':hidden'))
            {
                let offset= answerEditorContainer.offset();
                $('body,html').animate({
                    scrollTop:offset.top
                })
            }
        },
    });
});

/**
 * 提交回答
 */
$(document).on('click', '.uk-answer-submit', function (e) {
    let that = this;
    let form = $($(that).parents('form')[0]);
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if(result.code)
            {
                if(result.code===2)
                {
                    $(".uk-answer-list").prepend(result.data.html);
                    $('.uk-answer-count').text(result.data.answer_count);
                }

                if(result.code===1)
                {
                    $('#question-answer-'+result.data.id).html(result.data.html);
                }
                $('#answerEditor').empty().hide();
            }
            layer.msg(result.msg);
        }
    });
});

$(document).ready(function ()
{
    let showAll = $('#show-all');
    if(showAll.height() >= 100)
    {
        showAll.show().css('height','100px');
        $('.uk-question-show').show();
    }

    $(document).on('click', '.uk-question-show', function (e) {
        $('.uk-question-show').hide();
        showAll.show().css('height','auto');
        $('.uk-question-hide').show();
    });

    $(document).on('click', '.uk-question-hide', function (e) {
        $('.uk-question-hide').hide();
        showAll.show().css('height','100px');
        $('.uk-question-show').show();
    });

    $('.uk-answer-item .uk-answer-content').each(function(){
        if($(this).height() >= 200)
        {
            if(answerId)
            {
                $(this).css('height','auto');
                $(this).parents('.uk-answer-item').find('.uk-answer-hide').show();
            }else{
                $(this).css('height','200px');
                $(this).parents('.uk-answer-item').find('.uk-answer-show').show();
            }
        }
    });

    $(document).on('click', '.uk-answer-show', function (e) {
        $(this).hide();
        $(this).parents('.uk-answer-item').find('.uk-answer-content').show().css('height','auto');
        $(this).parents('.uk-answer-item').find('.uk-answer-hide').show();
    });

    $(document).on('click', '.uk-answer-hide', function (e) {
        $(this).hide();
        $(this).parents('.uk-answer-item').find('.uk-answer-content').show().css('height','200px');
        $(this).parents('.uk-answer-item').find('.uk-answer-show').show();
    });

    $('.uk-qrcode-container').each(function(){
        let that = $(this);
        let url = that.data('share');
        that.find('.uk-qrcode').qrcode({
            render: "canvas",
            width: 90,
            height: 90,
            text: url,
            /*foreground: "#C00",
            background: "#FFF",*/
        });

        let myCanvas=that.find('.uk-qrcode').find('canvas')[0];
        let img=convertCanvasToImage(myCanvas);
        that.find('.uk-qrcode').append(img);
        that.find('.uk-qrcode canvas').hide();
    });

    function convertCanvasToImage(canvas) {
        let image = new Image();
        image.src = canvas.toDataURL("image/png");
        return image;
    }
})

/*ajax加载回答(已弃用)*/
/*$(function (){
    loadAnswer();
    $('.uk-answer-sort a').click(function ()
    {
        $('.uk-answer-sort li').removeClass('active');
        $(this).parent('div').addClass('active');
        $('.uk-sort-show').find('span').text($(this).text())
        $('.uk-answer-list').empty();
        loadAnswer();
    });

    function loadAnswer()
    {
        layui.flow.load({
            elem: '#uk-answer-list'
            , done: function (page, next) {
                let answerList = $('.uk-answer-list');
                let aid = parseInt(answerList.data('aid'));
                let id = parseInt(answerList.data('id'));
                let sort = $('.uk-answer-sort li.active').data('type');
                let url = baseUrl+'/ask/question/answers?page=' + page + '&question_id=' + id+'&sort='+sort;
                let currentPage = 0;

                if (aid > 0) {
                    currentPage = 1;
                    url = baseUrl+'/ask/question/answers?page=' + page + '&question_id=' + id + '&answer_id=' + aid+'&sort='+sort;
                }
                url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
                $.get(url, function (res) {
                    currentPage = res.data.last_page;
                    currentPage = currentPage ? currentPage : 0;
                    next(res.data.html, page < currentPage);
                    if(page >= currentPage)
                    {
                        $('.layui-flow-more').hide();
                    }
                    $(".layui-flow-more>a").text('加载更多');
                });
            }
        });
    }
});*/
