<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!--渲染器-->
    <meta name="renderer" content="webkit">
    <!--优先使用最新版本的IE 和 Chrome 内核-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{:get_setting('site_name')} | 登录</title>
    <link rel="stylesheet" href="/static/admin/libs/layui/css/layui.css">
    <script src="/static/libs/layui/layui.js"></script>
    <script src="/static/admin/libs/jquery/jquery.min.js"></script>
    <script>
        layui.use('layer',
            function () {
                var $ = layui.jquery, layer = layui.layer;
            })
    </script>
    <style>
        html {
            background-color: #f2f2f2
        }
        #video_wrapper {
            margin: 0px;
            padding: 0px;
        }
        #video_wrapper video {
            position: fixed;
            top: 50%;
            left: 50%;
            z-index: -100;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
        }

        .layui-nav-item .layui-icon {
            margin-right: 10px
        }

        .layui-nav-child a {
            padding: 0 20px 0 40px !important
        }

        #LAY-user-login, .uk-user-display-show {
            display: block !important
        }

        .uk-user-login {
            position: relative;
            left: 0;
            top: 0;
            padding: 150px 0;
            min-height: 100%;
            box-sizing: border-box;
        }

        .uk-user-login-main {
            width: 450px;
            padding: 10px 0 0 0;
            border-radius: 10px;
            margin: 0 auto;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.407843);
        }

        .uk-user-login-box {
            padding: 30px 90px;
        }

        .uk-user-login-header {
            text-align: center;
            border-bottom: 1px solid #666;
            line-height: 40px;
            padding: 10px
        }

        .uk-user-login-header h2 {
            font-weight: 300;
            font-size: 30px;
            color: #333;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        .uk-user-login-header p {
            font-weight: 300;
            color: #999
        }

        .uk-user-login-body .layui-form-item {
            position: relative
        }

        .uk-user-login-icon {
            position: absolute;
            left: 1px;
            top: 1px;
            width: 38px;
            line-height: 36px;
            text-align: center;
            color: #d2d2d2
        }

        .uk-user-login-body .layui-form-item .layui-input {
            padding-left: 38px
        }

        .uk-user-login-other > * {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
            font-size: 14px
        }

        .uk-user-login-other .layui-icon {
            position: relative;
            top: 2px;
            font-size: 26px
        }

        .uk-user-login-other a:hover {
            opacity: .8
        }
        .uk-user-login-footer {
            left: 0;
            bottom: 0;
            width: 100%;
            line-height: 25px;
            padding: 10px 0 0;
            text-align: center;
            box-sizing: border-box;
            color: rgba(0, 0, 0, .5)
        }

        .uk-user-login-footer span {
            padding: 0 5px
        }

        .uk-user-login-footer a {
            padding: 0 5px;
            color: rgba(0, 0, 0, .5)
        }

        .uk-user-login-footer a:hover {
            color: rgba(0, 0, 0, 1)
        }

        .uk-user-login-main[bgimg] {
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, .05)
        }

        .ladmin-user-login-theme ul {
            display: inline-block;
            padding: 5px;
            background-color: #fff
        }

        .ladmin-user-login-theme ul li {
            display: inline-block;
            vertical-align: top;
            width: 64px;
            height: 43px;
            cursor: pointer;
            transition: all .3s;
            -webkit-transition: all .3s;
            background-color: #f2f2f2
        }

        .ladmin-user-login-theme ul li:hover {
            opacity: .9
        }

        @media screen and (max-width: 768px) {
            .uk-user-login {
                padding-top: 60px
            }

            .uk-user-login-main {
                width: 300px
            }

            .uk-user-login-box {
                padding: 10px
            }
        }
    </style>
</head>
<body>
<!--<div id="video_wrapper">
    <video autoplay muted loop>
        <source src="/static/admin/coverr/bg.mp4" type="video/mp4">
    </video>
</div>-->

<div class="uk-user-login uk-user-display-show" id="LAY-user-login">
    <div class="uk-user-login-main">
        <div class="uk-user-login-box uk-user-login-header">
            <h2>{:get_setting('site_name')}</h2>
        </div>
        <div class="uk-user-login-box uk-user-login-body layui-form">
            <div class="layui-form-item">
                <label class="uk-user-login-icon layui-icon layui-icon-username"
                       for="LAY-user-login-username">
                </label>
                <input type="text" name="username" id="LAY-user-login-username" lay-verify="required"
                       placeholder="用户名" class="layui-input" {if isset($user_info)} value="{$user_info['user_name']}" readonly {/if}>
            </div>
            <div class="layui-form-item">
                <label class="uk-user-login-icon layui-icon layui-icon-password"
                       for="LAY-user-login-password">
                </label>
                <input type="password" name="password" id="LAY-user-login-password" lay-verify="required"
                       placeholder="密码" class="layui-input">
            </div>
            <div class="layui-form-item">
                <input type="hidden" name="__token__" value="{:token()}"/>
                <button class="layui-btn layui-btn-fluid login" lay-submit lay-filter="LAY-user-login-submit">
                    登 录
                </button>
            </div>
            <div class="layui-trans uk-user-login-footer">
                <p>CopyRight© UKnowing</p>
            </div>
        </div>
    </div>
</div>
<script>
    // 回车触发登录
    $(document).keyup(function (event) {
        if (event.keyCode == 13) {
            $(".login").trigger("click");
        }
    });
    $(function () {
        // 刷新验证码操作
        $(".uk-user-login-codeimg").click(function () {
            $(this).attr("src", $(this).attr("src") + '?' + Math.random());
        })
        // 后台登录
        $(".login").click(function () {
            var username = $("input[name='username']").val();
            var password = $("input[name='password']").val();
            var __token__ = $("input[name='__token__']").val();
            var vercode = $("input[name='vercode']").val();
            if (!username) {
                layer.msg('请输入用户名', {
                    icon: 2
                }, function (index) {
                    layer.close(index);
                    $("input[name='username']").focus();
                });
                return false;
            }
            if (!password) {
                layer.msg('请输入密码', {
                    icon: 2
                }, function (index) {
                    layer.close(index);
                    $("input[name='password']").focus();
                });
                return false;
            }
            $.ajax({
                type: "post",
                url: "{:url('admin/Index/login')}",
                data: {
                    user_name: username,
                    password: password,
                    vercode: vercode,
                    __token__: __token__
                },
                dataType: "json",
                success: function (data) {
                    if (data.error == 1) {
                        layer.msg(data.msg, {
                            icon: 2
                        }, function (index) {
                            layer.close(index);
                            $(".uk-user-login-codeimg").attr("src", $(".uk-user-login-codeimg").attr("src") + '?' + Math.random());
                            $("input[name='vercode']").val('').focus();
                        });
                    } else if (data.error == 2) {
                        layer.msg(data.msg, {
                            icon: 2
                        }, function (index) {
                            layer.close(index);
                            window.location.reload();
                        });
                    } else {
                        layer.msg(data.msg, {}, function (index) {
                            layer.close(index);
                            layer.load();
                            window.location.href = data.url;
                        });
                    }
                },
                error: function (xhr) {

                }
            });

        })
    })
</script>
</body>
</html>