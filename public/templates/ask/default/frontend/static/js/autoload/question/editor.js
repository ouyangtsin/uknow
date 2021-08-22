$(document).on('click', '.uk-answer-submit', function (e) {
    var that = this;
    var form = $($(that).parents('form')[0]);
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
                    $(".uk-answer-list", window.parent.document).prepend(result.data.html);
                    $('.uk-answer-count', window.parent.document).text(result.data.answer_count);
                }
                parent.layer.closeAll();
            }
            parent.layer.msg(result.msg);
        },
        error: function (error) {
            if ($.trim(error.responseText) !== '') {
                layer.closeAll();
                UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
            }
        }
    });
});