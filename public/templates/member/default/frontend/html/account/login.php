<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{:get_setting('site_name')} | 登录</title>
    <link rel="stylesheet" href="/static/libs/bootstrap/css/bootstrap.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/common/fonts/fonts.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="{$theme_path}/fonts/iconfont.css?v={$version|default='1.0.0'}">
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
            <h2 class="text-center"><a href="{$baseUrl}">{:get_setting('site_name')}</a> - 登录</h2>
            <form id="loginForm" method="post" action="">
                <input type="hidden" name="url" value="{$return_url}">
                <div class="dataform" >
                    {if in_array('mobile',$setting['register_valid_type']) && get_plugins_info('sms')['status'] && get_plugins_config('sms','enable')}
                    <div class="input-warp gap">
                        <span class="input-icon icon-phone"></span>
                        <input id="userMobile" name="mobile" placeholder="手机号码" type="text" class="inputs" value=""/>
                    </div>
                    <div class="form-item gap mb-3 overflow-hidden">
                        <div class="input-warp float-left" style="width: 70%;">
                            <span class="input-icon icon-code"></span>
                            <input id="smsCode" name="sms_code" placeholder="验证码" type="text" class="inputs" value=""/>
                            <div id="popup-captcha" style="display: none"></div>
                        </div>
                        <div class="float-right">
                            <button id="btnSendCode" class="btn btn-primary" type="button">获取验证码</button>
                        </div>
                    </div>
                    {/if}
                    <div class="input-warp gap">
                        <span class="input-icon icon-users"></span>
                        <input id="userName" name="user_name" type="text" class="inputs" placeholder="用户名\手机号码\邮箱" maxlength="64">
                    </div>
                    <div class="error-content">
                        <span id="userNameErr" class="errMsg"></span>
                    </div>

                    <div class="input-warp gap">
                        <span class="input-icon icon-eye"></span>
                        <input class="inputs" type="password" name="password" placeholder="密码" id="pwd" maxlength="20">
                    </div>
                    <div class="error-content">
                        <span id="passwordErr" class="errMsg"></span>
                    </div>

                    <div class="btn-warp gap">
                        <div class="text-center">
                            <button type="button" id="btnLogin" class="btn btn-primary w-100 uk-ajax-form">登录</button>
                        </div>
                    </div>
                    <div class="gap" style="overflow: hidden">
                        <div style="float: right">
                            <a href="javascript:;" class="link">忘记密码</a>
                            <span class="split-space">|</span>
                            <a href="{:url('member/account/register')}" class="link">新用户注册</a></div>

                        <div class="pretty-box" style="float: left">
                            <input type="checkbox" value="1" name="remember" id="remember" class="">
                            <label for="remember" style="font-weight: normal" >记住我</label>
                        </div>
                    </div>
                    {if get_plugins_info('third')['status'] && get_plugins_config('third','enable')}
                    <div class="biggap third-party-title">
                        <h5 class="text-center"><span>第三方账号登录</span></h5>
                    </div>
                    <div class="third-auth">
                        {if in_array('weibo',explode(',',get_plugins_config('third','enable')))}
                        <a title="用微博登录"  href="{:plugins_url('third://Index/connect',['platform'=>'weibo'])}"><i class="iconfont icon-weibo text-warning" style="font-size: 3rem"></i></a>
                        {/if}
                        {if in_array('wechat',explode(',',get_plugins_config('third','enable')))}
                        <a title="用微信账户登录"  href="{:plugins_url('third://Index/connect',['platform'=>'wechat'])}"><i class="iconfont icon-weixin text-success" style="font-size: 3rem"></i></a>
                        {/if}
                        {if in_array('qq',explode(',',get_plugins_config('third','enable')))}
                        <a title="用QQ账户登录"  href="{:plugins_url('third://Index/connect',['platform'=>'qq'])}"><i class="iconfont icon-QQ text-primary" style="font-size:3rem"></i></a>
                        {/if}
                    </div>
                    {/if}
                </div>
            </form>
        </div>
    </div>
</div>
{$_script|raw}
</body>
</html>