<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                {include file="setting/nav"}
                <div class="bg-white mt-1 px-3 py-1">
                    <form class="p-3" action="{:url('member/account/save_profile')}">
                        <input type="hidden" name="uid" value="{$user_id}">
                        <div class="profile-details text-center mt-2 mb-3">
                            <div class="profile-image">
                                <div id="fileList_cover" class="uploader-list"></div>
                                <div id="filePicker_cover" style="margin: 0 auto">
                                    <a href="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                        <img class="image_preview_info" src="{$user_info.avatar|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                    </a>
                                </div>
                                <input type="hidden" name="avatar" value="{$user_info.avatar|default='/static/common/image/default-cover.svg'}" class="avatar-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="uk-form-label"> 昵称 </label>
                            <div class="uk-form-controls">
                                <label>
                                    <input type="text" class="form-control" name="nick_name" placeholder="输入用户昵称" value="{$user_info.nick_name}">
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="uk-form-label"> 性别 </label>
                            <div class="uk-form-controls">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input value="1" type="radio" id="sex1" name="sex" {if $user_info.sex==1} checked {/if} class="custom-control-input">
                                    <label class="custom-control-label" for="sex1">男</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input value="2" type="radio" id="sex2" name="sex" {if $user_info.sex==2} checked {/if} class="custom-control-input">
                                    <label class="custom-control-label" for="sex2">女</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline">
                                    <input value="0" type="radio" id="sex0" name="sex" {if $user_info.sex==2} checked {/if} class="custom-control-input">
                                    <label class="custom-control-label" for="sex0">保密</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="uk-form-label"> 邮箱 </label>
                            <div class="uk-form-controls">
                                <label>
                                    <input type="email" class="form-control" name="email" placeholder="暂未绑定邮箱信息" value="{$user_info.email}" readonly disabled>
                                </label>
                                <button  class="btn btn-primary uk-ajax-open" data-width="500px" data-title="修改邮箱" data-url="{:url('member/account/modify_email')}" >修改邮箱</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="uk-form-label"> 手机 </label>
                            <div class="uk-form-controls">
                                <label>
                                    <input type="text" class="form-control" placeholder="暂未绑定手机号码" name="mobile" value="{$user_info['mobile'] ? substr_replace($user_info.mobile,'****',3,4) : '暂未绑定手机号码'}" readonly disabled>
                                </label>
                                <button data-width="400px" class="btn btn-primary uk-ajax-open" data-url="{:url('member/account/modify_mobile')}">修改手机号</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="uk-form-label"> 自定义网址 </label>
                            <div class="uk-form-controls">
                                <span class="text-color-info">{$baseUrl.'/people/'}</span>
                                <label>
                                    <input type="text" class="form-control" name="url_token" value="{$user_info['url_token'] ? $user_info['url_token'] : $user_info['user_name']}">
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="uk-form-label"> 生日 </label>
                            <div class="uk-form-controls">
                                <label>
                                    <input type="text" class="form-control" id="birthday" name="birthday" value="{$user_info['birthday'] ? date('Y年m月d日',$user_info['birthday']) : 0}">
                                </label>
                            </div>
                        </div>

                        <div class="overflow-hidden">
                            <button class="uk-ajax-form btn btn-primary px-4 float-right" type="button">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    layui.laydate.render({
        elem: '#birthday',
        format: 'yyyy-MM-dd',
        min: "{:date('Y-m-d',intval(time()-80*365*24*60*60))}",
        max: "{:date('Y-m-d',time())}"
    });
    //上传头像封面
    UK.upload.webUpload('filePicker_cover','cover_preview','avatar','avatar');
</script>