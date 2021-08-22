$('.uk-question-form').click(function (){
    let that = this;
    let form = $($(that).parents('form')[0]);
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            let url = result.data.url ? result.data.url : result.url;
            if (result.code === 99) {
               UK.api.post(baseUrl+'/pay/apply_scan_pay',result.data,function (res)
               {
                   layer.open({
                       type: 1,
                       title: result.data.title,
                       closeBtn: 1,
                       area: ['auto'],
                       shadeClose: true,
                       content: res.data.html
                   });
               });
            }else{
                UK.api.success(result.msg, url);
            }
        },
        error: function (error) {
            if ($.trim(error.responseText) !== '') {
                layer.closeAll();
                UK.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
            }
        }
    })
})