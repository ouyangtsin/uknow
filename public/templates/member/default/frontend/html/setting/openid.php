<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                {include file="setting/nav"}
                <div class="bg-white mt-1 py-4 px-3">
                    {if get_plugins_info('third')['status'] && get_plugins_config('third','enable')}
                    <div class="d-flex text-center">
                        {if in_array('wechat',explode(',',get_plugins_config('third','enable')))}
                        <dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="iconfont icon-weixin text-success" style="font-size: 4rem"></i></dt>
                            <dd class="text-muted mt-1">微信</dd>
                            <dd class="mt-3">
                                <a class="btn btn-primary btn-sm text-white" href="{:plugins_url('third://Index/bind',['platform'=>'wechat'])}">绑定账号</a>
                            </dd>
                        </dl>
                        {/if}

                        {if in_array('qq',explode(',',get_plugins_config('third','enable')))}
                        <dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="iconfont icon-QQ text-primary" style="font-size: 4rem"></i></dt>
                            <dd class="text-muted mt-1">QQ</dd>
                            <dd class="mt-3">
                                <a class="btn btn-primary btn-sm text-white" href="{:plugins_url('third://Index/bind',['platform'=>'qq'])}">绑定账号</a>
                            </dd>
                        </dl>
                        {/if}

                        {if in_array('weibo',explode(',',get_plugins_config('third','enable')))}
                        <dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="iconfont icon-weibo text-warning" style="font-size: 4rem"></i></dt>
                            <dd class="text-muted mt-1">微博</dd>
                            <dd class="mt-3">
                                <a class="btn btn-primary btn-sm text-white" href="{:plugins_url('third://Index/bind',['platform'=>'weibo'])}">绑定账号</a>
                            </dd>
                        </dl>
                        {/if}

                        <!--<dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="iconfont icon-github text-danger" style="font-size: 4rem"></i></dt>
                            <dd class="text-muted mt-1">GitHub</dd>
                            <dd class="mt-3"><button class="btn btn-primary btn-sm">绑定账号</button></dd>
                        </dl>

                        <dl class="flex-fill mb-0 col-md-12">
                            <dt><i class="iconfont icon-dingtalk text-primary" style="font-size: 4rem"></i></dt>
                            <dd class="text-muted mt-1">DingTalk</dd>
                            <dd class="mt-3"><button class="btn btn-primary btn-sm">绑定账号</button></dd>
                        </dl>-->
                    </div>
                    {else/}
                    <p class="text-center py-3 text-muted">
                        <img src="/static/common/image/empty.svg">
                        <span class="d-block  ">本站暂未开启第三方登录</span>
                    </p>
                    {/if}
				</div>
			</div>
		</div>
	</div>
</div>