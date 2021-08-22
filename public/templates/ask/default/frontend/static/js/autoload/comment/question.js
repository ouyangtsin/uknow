
/*问题评论操作*/
$(document).on('click', '.uk-comment-actions>label>a', function () {
    type = $(this).data('type');
    id = $(this).parents('.post-comments-single').data('id');
    questionid = $(this).parents('.post-comments-single').data('itemid');
    switch (type) {
        case 'reply':
            info = $(this).data('info');
            $(this).parents('.uk-comment-box').find('input[name=message]').attr('placeholder','@' + info.user_name + ': ').focus();
            info = JSON.stringify(info);
            $(this).parents('.uk-comment-box').find('input[name=at_info]').val(info);
            $(this).parents('.uk-comment-box').find('input[name=id]').val('');
            break;
        case 'vote':
            id=$(this).parents('.uk-comment-item').data('id');
            $.post("/ask/ajax/set_vote", {"item_id": id,item_type:'question_comment'}, function (result) {
                console.log(result);
                if(result.code){
                    UK.api.success(result.msg);
                    location.reload();
                }else{
                    UK.api.error(result.msg);
                }
            })
        break;


    }
});