<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0"  id="uk-center-main">
                {include file="setting/nav"}
				<div class="bg-white mt-1 px-3 py-1">
                    <form method="post" action="{:url('member/setting/notify')}">
                        <div class="uk-mod bg-white px-3 pt-3">
                            <div class="uk-mod-head mb-0">
                                <p class="mod-head-title font-12 ">私信设置</p>
                            </div>
                            <div class="uk-mod-body">
                                <dl>
                                    <dt class="text-muted my-2 font-9">谁可以给我发私信 :</dt>
                                    <dd class="row px-0 font-9">
                                        <label class="col-4"><input type="radio" value="all" name="inbox_setting" {if isset($user_setting['inbox_setting']) && $user_setting['inbox_setting']=='all'} checked="checked" {/if}> 所有人</label>
                                        <label class="col-4"><input type="radio" value="focus" name="inbox_setting" {if isset($user_setting['inbox_setting']) && $user_setting['inbox_setting']=='focus'} checked="checked" {/if}> 我关注的人</label>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="uk-mod bg-white px-3 pt-3">
                            <div class="uk-mod-head mb-0">
                                <p class="mod-head-title font-12">邮件设置</p>
                            </div>
                            <div class="uk-mod-body">
                                <dl>
                                    <dt class="text-muted my-2 font-9">什么情况下给我发邮件 :</dt>
                                    <dd class="row px-0 font-9">
                                        {volist name="email_setting" id="v"}
                                        {if $v.user_setting}
                                        <label class="col-4">
                                            <input name="email_setting[]" type="checkbox" value="{$key}" {if isset($user_setting['email_setting']) && in_array($key,$user_setting['email_setting'])} checked="checked" {/if}> {$v.title}
                                        </label>
                                        {/if}
                                        {/volist}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <div class="uk-mod bg-white px-3 pt-3">
                            <div class="uk-mod-head mb-0">
                                <p class="mod-head-title font-12">通知设置</p>
                            </div>
                            <div class="uk-mod-body">
                                <dl>
                                    <dt class="text-muted my-2 font-9">什么情况下给我发送通知 :</dt>
                                    <dd class="row px-0 font-9">
                                        {volist name="notify_setting" id="v"}
                                        {if $v.user_setting}
                                        <label class="col-4">
                                            <input name="notify_setting[]" type="checkbox" value="{$key}" {if isset($user_setting['notify_setting']) && in_array($key,$user_setting['notify_setting'])} checked="checked" {/if}> {$v.title}
                                        </label>
                                        {/if}
                                        {/volist}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary px-4 uk-ajax-form btn-sm mb-3">提交修改</button>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>