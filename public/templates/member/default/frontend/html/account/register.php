<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{:get_setting('site_name')} | 注册</title>
    <link rel="stylesheet" href="/static/libs/bootstrap/css/bootstrap.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/common/fonts/fonts.css?v={$version|default='1.0.0'}">
    <!--[if lt IE 9]>
    <script src="/static/common/js/html5.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/common/js/respond.min.js?v={$version|default='1.0.0'}"></script>
    <![endif]-->
    <script src="/static/common/js/jquery.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/common/js/jquery.cookie.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/layer/layer.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/common/js/uk-common.js?v={$version|default='1.0.0'}"></script>
    <script>
        window.userId = parseInt("{$user_id|default='0'}");
        window.baseUrl = '{$baseUrl}';
        window.cdnUrl = '{$cdnUrl}';
        window.tapiRoot = '{$baseUrl}/common/';
        window.thisController ="{$thisController|default=''}";
        window.thisAction ="{$thisAction|default=''}";
        window.staticUrl = cdnUrl + '/static/';
        window.userGroup = '{$user_info.group_id|default=3}';
    </script>
    {$_style|raw}
    <style>
        .main-warp {
            position: absolute;
            left: 0;
            right: 0;
            overflow: auto;
            background: rgb(246, 246, 246);
            background-size: cover
        }
    </style>
</head>
<body>
<div id="main" class="main-warp" style="min-height: 100vh">
    <div class="main-content">
        <div class="formDiv">
            <h2 class="title text-center"><a href="{$baseUrl}">{:get_setting('site_name')}</a> - 注册</h2>
            <form id="registerForm" role="form" class="form-horizontal " action="" method="post">
                <input type="hidden" name="url" value="{$return_url}">
                {:token_field()}
                <div class="form-item">
                    <div class="input-warp">
                        <span class="input-icon icon-users"></span>
                        <input id="userName" name="user_name" placeholder="用户名" type="text" class="inputs" value=""/>
                    </div>
                    <p id="userNameErr" class="errMsg"></p>
                </div>
                {if in_array('mobile',$setting['register_valid_type']) && get_plugins_info('sms')['status'] && get_plugins_config('sms','enable')}
                <div class="form-item">
                    <div class="input-warp">
                        <span class="input-icon icon-phone"></span>
                        <input id="userMobile" name="mobile" placeholder="手机号码" type="text" class="inputs" value=""/>
                    </div>
                    <p id="userMobileErr" class="errMsg"></p>
                </div>
                <div class="form-item">
                    <div class="input-warp s">
                        <span class="input-icon icon-code"></span>
                        <input id="smsCode" name="sms_code" placeholder="验证码" type="text" class="inputs" value=""/>
                        <div id="popup-captcha" style="display: none"></div>
                    </div>
                    <div class="float-right">
                        <button id="btnSendCode" class="btn btn-primary" type="button">获取验证码</button>
                    </div>
                    <p id="kaptchaErr" class="errMsg"></p>
                </div>
                {/if}

                {if $setting.register_type =='invite'}
                <div class="form-item">
                    <div class="input-warp">
                        <span class="input-icon icon-attachment1"></span>
                        <input id="invite_code" type="text" placeholder="请输入邀请码" name="invite_code" value="" class="inputs"/>
                    </div>
                    <p id="invite_codeErr" class="errMsg"></p>
                </div>
                {/if}

                <div class="form-item">
                    <div class="input-warp">
                        <span class="input-icon icon-eye"></span>
                        <input id="pwd" type="password" placeholder="请输入{$setting.password_min_length} - {$setting.password_max_length}位的密码" maxlength="{$setting.password_max_length}" name="password" value="" class="inputs"/>
                    </div>
                    <p id="passwordErr" class="errMsg"></p>
                </div>
                <div class="form-item">
                    <div class="input-warp">
                        <span class="input-icon icon-eye"></span>
                        <input id="repwd" type="password" placeholder="请输入{$setting.password_min_length} - {$setting.password_max_length}位的密码" maxlength="{$setting.password_max_length}" name="re_password" value="" class="inputs"/>
                    </div>
                    <p id="passwordErr2" class="errMsg"></p>
                </div>
                <div class="form-item">
                    <div class="input-warp">
                        <span class="input-icon icon-email"></span>
                        <input id="email" name="email" placeholder="邮箱地址" type="text" class="inputs" value=""/>
                    </div>
                    <p id="emailErr" class="errMsg"></p>
                </div>

                <div class="btn-warp">
                    <div class="text-center">
                        <button type="button" id="btnSubmit"  class="btn btn-primary w-100 uk-ajax-form">注册</button>
                    </div>
                </div>
                <div class="agreement gap">
                    <div class="text-right" style="overflow: hidden">
                        <div style="float: right">已有账号，<a href="{:url('member/account/login')}" class="link">登录</a></div>
                        <p style="float: left">注册即代表您已阅读并同意《<a href="javascript:;" class="link register-agreement" >使用协议</a>》</p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{if in_array('mobile',$setting['register_valid_type'])}
<script>
    let cookie = $.cookie("captcha");
    if(cookie){
        let count = cookie;
        let btn = $('#btnSendCode');
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
    $("#btnSendCode").click(function (e) {
        let that = this;
        let phoneReg = /^1[3456789]\d{9}$/;
        let mobile = $('input[name="mobile"]').val();
        if(!mobile){
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
{/if}
<script type="text/html" id="agreement">
    <div style="padding: 30px">
        {$agreement|raw}
    </div>
</script>
<script>
    $('.register-agreement').click(function (){
        layer.open({
            type: 1,
            title: '注册协议',
            closeBtn: 1,
            area: ['80%', '80%'],
            shadeClose: true,
            content: $('#agreement').html()
        });
    });
</script>
{$_script|raw}
</body>
</html>

