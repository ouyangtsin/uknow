<link rel="stylesheet" href="/static/libs/bootstrap/css/bootstrap.min.css?v={$version|default='1.0.0'}">
<link rel="stylesheet" href="/static/common/fonts/fonts.css?v={$version|default='1.0.0'}">
<link rel="stylesheet" href="{$theme_path}/fonts/iconfont.css?v={$version|default='1.0.0'}">
<link rel="stylesheet" href="/static/libs/highlight/styles/tomorrow.css">
<link rel="stylesheet" type="text/css" href="/static/libs/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
<link rel="stylesheet" href="/static/libs/webui-popover/dist/jquery.webui-popover.min.css">
<link rel="stylesheet" href="{$theme_path}css/common.css?v={$version|default='1.0.0'}">
{$_style|raw}
<!--[if lt IE 9]>
<script src="/static/common/js/html5.min.js?v={$version|default='1.0.0'}"></script>
<script src="/static/common/js/respond.min.js?v={$version|default='1.0.0'}"></script>
<![endif]-->
<script src="/static/libs/highlight/highlight.pack.js"></script>
<script>
    window.userId = parseInt("{$user_id|default='0'}");
    window.baseUrl = '{$baseUrl}';
    window.cdnUrl = '{$cdnUrl}';
    window.tapiRoot = '{$baseUrl}/common/';
    window.thisController ="{$thisController|default=''}";
    window.thisAction ="{$thisAction|default=''}";
    window.staticUrl = cdnUrl + '/static/';
    window.userGroup = '{$user_info.group_id|default=3}';
    let upload_image_ext = "{$setting.upload_image_ext}" ;
    let upload_file_ext = "{$setting.upload_file_ext}" ;
    let upload_image_size = "{$setting.upload_image_size}" ;
    let upload_file_size = "{$setting.upload_file_size}" ;
    //代码高亮
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('pre').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });
</script>
<script src="/static/common/js/jquery.js?v={$version|default='1.0.0'}"></script>
<script src="/static/libs/webui-popover/dist/jquery.webui-popover.min.js"></script>
<script src="/static/common/js/jquery.qrcode.min.js"></script>
<script src="/static/common/js/jquery.cookie.js?v={$version|default='1.0.0'}"></script>
<script src="/static/libs/bootstrap/js/bootstrap.bundle.min.js?v={$version|default='1.0.0'}"></script>
<script src="/static/libs/layui/layui.all.js?v={$version|default='1.0.0'}"></script>
<script src="/static/libs/pjax/jquery.pjax.js?v={$version|default='1.0.0'}"></script>
<script src="/static/libs/webuploader/webuploader.js"></script>
<script src="/static/common/js/uk-common.js?v={$version|default='1.0.0'}"></script>
<script src="/static/libs/clipboard/clipboard.min.js?v={$version|default='1.0.0'}"></script>
<script type="text/javascript" src="/static/libs/fancybox/lib/jquery.mousewheel.pack.js"></script>
<script type="text/javascript" src="/static/libs/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="{$theme_path}js/uk-app.js?v={$version|default='1.0.0'}"></script>