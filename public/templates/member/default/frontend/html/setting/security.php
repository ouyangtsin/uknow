<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                {include file="setting/nav"}
                <div class="bg-white mt-1 p-3 py-1">
                    <div class="form-group">
                        <label class="uk-form-label"> 账号密码 </label>
                        <div class="uk-form-controls">
                            <label>
                                <input type="text" class="form-control" placeholder="******" name="password" value="">
                            </label>
                            <button data-width="400px" class="btn btn-primary uk-ajax-open" data-title="修改密码" data-url="{:url('member/account/modify_password')}">修改密码</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="uk-form-label"> 交易密码 </label>
                        <div class="uk-form-controls">
                            <label>
                                <input type="text" class="form-control" placeholder="******" name="deal_password" value="">
                            </label>
                            <button data-width="400px" class="btn btn-primary uk-ajax-open" data-url="{:url('member/account/modify_deal_password')}">修改交易密码</button>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>