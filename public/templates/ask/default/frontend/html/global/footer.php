<div class="uk-footer-wrap bg-dark mt-3">
    {if !empty($links)}
    <div class="mt-2 bg-white p-3">
        <div class="container">
            <dl class="mb-0">
                <dt class="d-block mb-2">友情链接：</dt>
                {volist name="links" id="v"}
                <dd class="d-inline-block mr-3 mb-0">
                    <a href="">{$v.name}</a>
                </dd>
                {/volist}
            </dl>
        </div>
    </div>
    {/if}
    <div class="container py-3">
        <div class="uk-footer">
            <ul class="d-inline-block font-9 mr-4">
                <li class="d-inline-block mr-2"><a href="{:url('page/index',['url_name'=>'terms'])}" target="_blank">用户协议</a></li>
                <li class="d-inline-block mr-2"><a href="{:url('page/index',['url_name'=>'private'])}" target="_blank">隐私政策</a></li>
                <li class="d-inline-block mr-2"><a href="{:url('page/index',['url_name'=>'use'])}" target="_blank">使用指南</a></li>
                <li class="d-inline-block mr-2"><a href="{:url('page/index',['url_name'=>'ad'])}" target="_blank">广告合作</a></li>
                <li class="d-inline-block mr-2"><a href="{:url('page/index',['url_name'=>'contact'])}" target="_blank">联系我们</a></li>
            </ul>
            <div class="font-9 text-color-info mt-1">
                Copyright © 2021-{:date('Y',time())}
                <a href="{$baseUrl}" class="text-muted">{:get_setting('site_name')}</a>
                {if get_setting('icp')}
                <a href="http://www.beian.miit.gov.cn" class="text-muted" target="_blank">{:get_setting('icp')}</a>
                {/if}
                All Rights Reserved Powered By <a href="https://www.uknowing.com" class="text-muted" target="_blank">UKnowing V{$Think.const.UK_VERSION}</a>
            </div>
        </div>
    </div>
</div>
<a class="uk-back-top hidden-xs" href="javascript:;" onclick="$.scrollTo(1, 600, {queue:true});"><i class="icon-arrow-up-circle"></i></a>
<div class="uk-ajax-box" id="uk-ajax-box"></div>
{if $user_id && $user_info.is_first_login}
<script>
    let width = $(window).width() > 800 ? '600px' : '95%';
    let height = $(window).height() > 600 ? '600px' : '95%';
    let area = [width, height] ;
    layer.open({
        title: '完善资料',
        type: 2,
        shadeClose: true,
        maxmin:true,
        shade: 0.8,
        area: area,
        content: baseUrl+'/member/account/welcome_first_login?_ajax_open=1',
        success: function (layero, index) {
            const that = this;
            $(layero).data("callback", that.callback);
            layer.setTop(layero);
            if ($(layero).height() > $(window).height()) {
                //当弹出窗口大于浏览器可视高度时,重定位
                layer.style(index, {
                    top: 0,
                    height: $(window).height()
                });
            }
            layer.iframeAuto(index);
        },
        end: function () {

        },
        cancel: function(index){

        }
    });
</script>
{/if}