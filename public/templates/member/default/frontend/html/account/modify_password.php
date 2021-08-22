<form class="p-3" action="" id="verify" style="min-height: 186px">
    <input type="hidden" name="step" value="0">
    <input type="hidden" name="uid" value="{$user_id}">
    {if $user_info.mobile}
    <div class="form-group">
        <input type="text" class="form-control border-0" disabled placeholder="使用手机 {:substr_replace($user_info.mobile,'****',3,4)} 验证" value="使用手机 {:substr_replace($user_info.mobile,'****',3,4)} 验证">
    </div>
    <div class="form-group">
        <label>
            <input type="text" class="form-control border-0 border-bottom verify-text" name="sms_code" placeholder="输入您的短信验证码">
        </label>
        <button class="btn btn-primary btn-sm px-4 send-sms" type="button">获取验证码</button>
    </div>
    {else/}
    <div class="form-group">
        <input type="password" class="form-control border-0 border-bottom verify-text" name="password" placeholder="输入当前账号密码">
    </div>
    {/if}
    <div class="form-group">
        <input type="passord" class="form-control border-0 border-bottom" name="password" placeholder="输入新的密码" value="">
    </div>
    <div class="form-group">
        <input type="passord" class="form-control border-0 border-bottom" name="re_password" placeholder="再次输入新的密码" value="">
    </div>
    <div class="overflow-hidden">
        <button class="uk-ajax-form btn btn-primary w-100" type="button">提交修改</button>
    </div>
</form>
<script>
    $(function(){
        $('.verify-text').bind('input propertychange', function (e) {
            let that = this;
            if($(that).val()!=='')
            {
                $('.verify-btn').removeAttr('disabled');
            }else{
                $('.verify-btn').attr('disabled','disabled');
            }
        })
    })

    {if $user_info.mobile}
    if($.cookie("captcha")){
        let count = $.cookie("captcha");
        let btn = $('.send-sms);
        btn.prop('disabled', true);
        btn.text(count+'秒');
        let resend = setInterval(function(){
            count--;
            if (count > 0){
                btn.text(count+'秒');
                $.cookie("captcha", count, {path: '/', expires: (1/86400)*count});
            }else {
                btn.prop('disabled', false);
                clearInterval(resend);
                btn.text("获取验证码")
            }
        }, 1000);
    }
    {/if}
        $(".send-sms").click(function (e) {
            let that = this;
            let mobile = "{$user_info.mobile}";
            if($(that).text()!=="获取验证码"){
                return false;
            }
            $.ajax({
                url: baseUrl + '/ask/ajax/sms/',
                type: "post",
                dataType: "json",
                data: {
                    mobile: mobile,
                },
                success: function (result) {
                    if(result.code===0)
                    {
                        layer.msg(result.msg);
                        return false;
                    }else
                    {
                        layer.msg(result.msg);
                        let count = 60;
                        let inl = setInterval(function () {
                            $(that).prop('disabled', true);
                            count -= 1;
                            let text = count + ' 秒';
                            $.cookie("captcha", count, {path: '/', expires: (1/86400)*count});
                            $(that).text(text);
                            if (count <= 0) {
                                clearInterval(inl);
                                $(that).prop('disabled', false);
                                $(that).text('获取验证码');
                            }
                        }, 1000);
                        return true;
                    }
                }
            });
        });

        $('.verify-btn').click(function (){
            let that = this;
            let form = $($(that).parents('form')[0]);
            let url = form.attr('action');
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                success: function (result)
                {
                    if(result.code===0)
                    {
                        layer.msg(result.msg);
                    }else{
                        window.location.href = result.url;
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
</script>
<script>
    if($.cookie("captcha")){
        let count = $.cookie("captcha");
        let btn = $('.send-sms);
        btn.prop('disabled', true);
        btn.text(count+'秒');
        let resend = setInterval(function(){
            count--;
            if (count > 0){
                btn.text(count+'秒');
                $.cookie("captcha", count, {path: '/', expires: (1/86400)*count});
            }else {
                btn.prop('disabled', false);
                clearInterval(resend);
                btn.text("获取验证码")
            }
        }, 1000);
    }

    $(".send-sms").click(function (e) {
        let that = this;
        let phoneReg = /^1[3456789]\d{9}$/;
        let mobile = $('input[name="mobile"]').val();

        if(mobile){
            layer.msg("请输入手机号");
            return false;
        }
        if(!phoneReg.test(mobile)){
            layer.msg("手机号格式不正确");
            return false;
        }
        if($(that).text()!=="获取验证码"){
            return false;
        }
        $.post(baseUrl + '/account/check_mobile/',{
            mobile:mobile,
            type:'valid_mobile',
        }, function (result)
        {
            if (result.code === 0)
            {
                layer.msg("手机号已经注册！");
                return false;
            }else{
                $.ajax({
                    url: baseUrl + '/ask/ajax/sms/',
                    type: "post",
                    dataType: "json",
                    data: {
                        mobile: mobile,
                    },
                    success: function (result) {
                        if(result.code===0)
                        {
                            layer.msg(result.msg);
                            return false;
                        }else
                        {
                            layer.msg(result.msg);
                            let count = 60;
                            let inl = setInterval(function () {
                                $(that).prop('disabled', true);
                                count -= 1;
                                let text = count + ' 秒';
                                $.cookie("captcha", count, {path: '/', expires: (1/86400)*count});
                                $(that).text(text);
                                if (count <= 0) {
                                    clearInterval(inl);
                                    $(that).prop('disabled', false);
                                    $(that).text('获取验证码');
                                }
                            }, 1000);
                            return true;
                        }
                    }
                });
            }
        }, 'json');
    });
</script>
