//邀请回答
var questionId = $('#question-id').val();
var url = baseUrl+"/ask/question/invite?question_id="+questionId;
UK.api.ajaxLoadMore('#ajaxList',url,{name:''});
$(document).on('input propertychange', '#invite-users', function () {
    var name = $(this).val();
    $('#ajaxList').empty();
    let element = layui.element;
    UK.api.ajaxLoadMore('#ajaxList',url,{name:name});
    element.init();
});

//邀请回答按钮
$(document).on('click', '.question-invite', function () {
    var that = $(this);
    var uid = that.data('uid');
    var isInvite = that.data('invite');
    var questionId = that.data('id');
    var url = baseUrl+"/question/save_question_invite?question_id="+questionId;
    UK.api.post(url,{uid:uid,has_invite:isInvite},function (res) {
        if(res.data.invite)
        {
            that.addClass('active');
            that.removeClass('question-invite');
            that.data('invite',1);
            that.text('已邀请');
        }else{
            that.removeClass('active');
            that.data('invite',0);
            that.text('邀请回答');
        }
        layer.msg(res.msg);
    });
})