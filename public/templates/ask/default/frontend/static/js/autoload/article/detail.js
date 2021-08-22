$(document).ready(function ()
{
    /*异步获取评论*/

    let articleHeight = $('.uk-article').height();
    if(articleHeight>$(window).height() && $(window).scrollTop()+($(window).height() / 2) < articleHeight)
    {
        $('.uk-article-wrap .actions').addClass('fixed-bottom').addClass('bg-white').addClass('text-center').addClass('p-3');
    }else{
        $('.uk-article-wrap .actions').removeClass('fixed-bottom').removeClass('bg-white').removeClass('text-center').removeClass('p-3');
    }

});

/**
 * 回复评论
 */
$(document).on('click', '.article-comment-reply', function ()
{
    let info = $(this).data('info');
    let commentItem = $('#article-comment-'+$(this).data('comment-id'));
    let commentEditor = $('.uk-article-comment-editor');
    commentItem.find('.replay-editor').append(commentEditor).show();
    commentEditor.find('[name=at_info]').val(JSON.stringify(info));
    commentEditor.find('[name=pid]').val($(this).data('comment-id'));
    //commentEditor.find('textarea[name=message]').attr('placeholder','@'+info.user_name+' ').focus();
    commentEditor.find('textarea[name=message]').val('@'+info.user_name+' ').focus();
});

/**
 * 提交评论
 */
$(document).on('click', '.uk-article-comment-submit', function () {
    let that = this;
    let form = $($(that).parents('form')[0]);
    let data = form.serializeArray();
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: data,
        success: function (result) {
            if(result.code)
            {
                if(result.code===1)
                {
                    $("#article-comment-list").prepend(result.data.html);
                    $('.uk-comment-count').text(result.data.comment_count);
                    $('.uk-article-comment-editor').show();
                    $('.replay-editor').hide();
                }
            }
            layer.msg(result.msg);
        }
    });
});