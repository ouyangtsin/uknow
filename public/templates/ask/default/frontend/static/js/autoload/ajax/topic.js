$(document).on('input oninput','.topic-search-input', function (e) {
    let that = $('.topic-search-input');
    let keywords = that.val();
    let itemId = that.data('item-id');
    let itemType =that.data('item-type');
    let url = baseUrl+'/topic/get_topic/?keywords=' + encodeURIComponent(keywords) + '&limit=5&item_id='+itemId+'&item_type='+itemType;
    url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
    $.get(url, function (result) {
        $('.topic-search-list').html(result);
    }, 'html');
});

/*添加话题*/
$(document).on('click', '.save-topic', function (e) {
    const that = this;
    let form = $($(that).parents('form')[0]);

    return $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if (result.code) {
                if(result.data.list)
                {
                    let html = '';
                    var topic = [];
                    $.each(result.data.list, function(index, value) {
                        topic.push(value.id);
                        html+='<li class="d-inline uk-tag"><a href="'+value.url+'">'+value.title+'</a></li>';
                    });
                    html += '<input type="hidden" name="topics" value="'+topic+'" >'
                    parent.$('#uk-topic-list').html(html);
                }
                UK.api.success('编辑成功');
                parent.layer.closeAll();
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