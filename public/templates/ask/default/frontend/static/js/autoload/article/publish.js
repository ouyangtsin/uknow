$(document).on('click', '.uk-timing-publish', function () {
    layer.open({
        type: 1,
        closeBtn: 1,
        anim: 2,
        content: $('#timing-publish-modal').html()
    });
    layui.laydate.render({
        elem: '#timing' //指定元素
    });
    $('.select-choose').click(function () {
        var timing = $('#timing').val();
        if(timing)
        {
            $('input[name=wait_time]').val(timing);
        }else{
            $('input[name=wait_time]').val(0);
        }
        layer.closeAll();
    });
});
$(document).on('click', '.uk-preview', function () {
    const that = this;
    const form = $($(that).parents('form')[0]);
    const formData = {};
    const t = form.serializeArray();
    $.each(t, function () {
        formData[this.name] = this.value;
    });
    $.ajax({
        url: baseUrl + '/article/preview',
        dataType: 'json',
        type: 'post',
        data: {
            data: formData,
        },
        success: function () {
            window.open(baseUrl + '/article/preview');
        },
    })
});
function verification(data){
    var arr = {};
    $.each(data, function() {
        arr[this.name] = this.value;
    });
    return true;
}

//上传文章封面
UK.upload.webUpload('filePicker_cover','cover_preview','cover','article');
