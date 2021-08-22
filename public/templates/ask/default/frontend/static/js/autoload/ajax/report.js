$(document).on('click', '.save-report-form', function (e) {
    const that = this;
    const form = $($(that).parents('form')[0]);
    return $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if (result.code) {
                UK.api.success(result.msg);
                var index1 = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layer.close(index1); //再执行关闭
                parent.location.reload();
                setTimeout(function () {
                }, 1000);
            } else {
                UK.api.error(result.msg);
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