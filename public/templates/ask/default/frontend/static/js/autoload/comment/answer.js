
/**
 * 回复评论
 */
$(document).on('click', '.uk-comment-actions>.reply', function ()
{
    // let name = $(this).data('username');
    let info = $(this).data('info');
    // console.log(info);
    let commentEditor = $(this).parents('.uk-comment-box').find('form');
    // commentEditor.appendTo(nowitem);
    console.log(info);
    commentEditor.find('[name=at_info]').val(JSON.stringify(info));
    commentEditor.find('[name=message]').attr('placeholder','@'+info.user_name+' ').focus();
});