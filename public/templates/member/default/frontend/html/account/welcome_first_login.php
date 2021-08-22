{if $step==1}
<!--完善基本资料-->
<div class="uk-first-login first p-3 bg-white">
    <div class="first-login-head">
        <h2 class="font-10 text-muted mb-2">Hi , {$user_info['user_name']} , 欢迎来到 {:get_setting('site_name')}</h2>
        <ul class="d-flex text-center rounded">
            <li class="active flex-fill mr-1 font-weight-bold">完善基本资料</li>
            <li class="flex-fill mr-1">关注热门话题</li>
            <li class="flex-fill">关注热门用户</li>
        </ul>
    </div>
    <div class="first-login-body mt-4">
        <form action="{:url('member/account/welcome_first_login')}" method="post">
            <input type="hidden" name="step" value="1" />
            <input type="hidden" name="uid" value="{$user_id}" />
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
                <label class="uk-form-label"> 生日 </label>
                <div class="uk-form-controls">
                    <label>
                        <input type="text" class="form-control" id="birthday" name="birthday" value="{$user_info['birthday'] ? date('Y年m月d日',$user_info['birthday']) : 0}">
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="uk-form-label"> 介绍 </label>
                <div class="uk-form-controls">
                    <label>
                        <input type="text" class="form-control" placeholder="如：80后IT男.." id="welcome_signature" value="{$user_info['signature'] ? $user_info['signature'] : ''}" name="signature" />
                    </label>
                </div>
            </div>

            <div class="first-login-footer clearfix">
                <a class="float-left go-back text-muted" href="javascript:;">跳过</a>
                <button class="btn btn-primary float-right btn-sm login-btn" type="button">下一步</button>
            </div>
        </form>
    </div>
</div>
<script>
    //上传头像封面
    UK.upload.webUpload('filePicker_cover','cover_preview','avatar','avatar');
    layui.laydate.render({
        elem: '#birthday',
        format: 'yyyy-MM-dd',
        min: "{:date('Y-m-d',intval(time()-80*365*24*60*60))}",
        max: "{:date('Y-m-d',time())}"
    });
</script>
{/if}

{if $step==2}
<!--关注热门话题-->
<div class="uk-first-login two p-3 bg-white">
    <div class="first-login-head">
        <h2 class="font-10 text-muted mb-2">Hi , {$user_info['user_name']} , 欢迎来到 {:get_setting('site_name')}</h2>
        <ul class="d-flex text-center rounded">
            <li class="flex-fill mr-1">完善基本资料</li>
            <li class="flex-fill mr-1 active font-weight-bold">关注热门话题</li>
            <li class="flex-fill">关注热门用户</li>
        </ul>
    </div>
    <div class="first-login-body mt-4">
        <form action="{:url('member/account/welcome_first_login')}" method="post">
            <input type="hidden" name="step" value="2" />
            <input type="hidden" name="uid" value="{$user_id}" />
            <div id="topicResult" style="min-height: 200px" class="row">
                {volist name="data" id="v"}
                <div class="mb-2 col-12">
                    <dl class="clearfix position-relative p-2 border rounded">
                        <dt class="float-left mr-2">
                            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">
                                <img src="{$v['pic']|default='/static/common/image/topic.svg'}" height="45" width="45">
                            </a>
                        </dt>
                        <dd class="mb-0">
                            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">{$v.title}</a>
                            <p class="mb-0">
                                <span class="mr-2">讨论:{$v.discuss}</span><span class="mr-2">关注:{$v.focus}</span>
                            </p>
                        </dd>
                        <dd class="position-absolute" style="right: 5px;bottom: 5px">
                            <a href="javascript:;" class="cursor-pointer btn btn-sm btn-primary px-3 {$v['is_focus'] ? 'active' : ''}" onclick="UK.User.focus(this,'topic','{$v.id}')" >{$v['is_focus'] ? '已关注' : '关注'}</a>
                        </dd>
                    </dl>
                </div>
                {/volist}
            </div>
            <div class="my-2 clearfix">
                <a href="javascript:;" class="float-right text-muted rand-topic">换一批</a>
            </div>
            <div class="first-login-footer clearfix">
                <a class="float-left go-back text-muted" href="javascript:;">跳过</a>
                <button class="btn btn-primary float-right btn-sm login-btn" type="button">下一步</button>
            </div>
        </form>
    </div>
</div>
<script>
    let totalPage= parseInt("{$total}");
    let rand = parseInt(Math.random() * (totalPage - 1 + 1) + 1);
    function getTopic(page)
    {
        $.ajax({
            url: baseUrl+'/topic/get_hot_topic?_ajax=1',
            dataType: '',
            type: 'post',
            data: {page:page},
            success: function (result) {
                $('#topicResult').html(result.data.html);
            }
        });
    }

    $(document).on('click', '.rand-topic', function (e) {
        getTopic(rand);
    });
</script>
{/if}

{if $step==3}
<!--关注热门用户-->
<div class="uk-first-login three p-3 bg-white">
    <div class="first-login-head">
        <h2 class="font-10 text-muted mb-2">Hi , {$user_info['user_name']} , 欢迎来到 {:get_setting('site_name')}</h2>
        <ul class="d-flex text-center rounded">
            <li class="flex-fill mr-1">完善基本资料</li>
            <li class="flex-fill mr-1">关注热门话题</li>
            <li class="flex-fill active font-weight-bold">关注热门用户</li>
        </ul>
    </div>
    <div class="first-login-body mt-4">
        <form action="{:url('member/account/welcome_first_login')}" method="post">
            <input type="hidden" name="step" value="3" />
            <input type="hidden" name="uid" value="{$user_id}" />
            <div id="userResult" style="min-height: 200px" class="row">
                {volist name="data" id="v"}
                <div class="mb-2 col-12">
                    <dl class="clearfix position-relative p-2 border rounded">
                        <dt class="float-left mr-2">
                            <a href="{:url('member/index//index',['uid'=>$v['uid']])}" class="uk-username" data-id="{$v.uid}">
                                <img src="{$v['avatar']|default='/static/common/image/default-avatar.svg'}" class="rounded"  height="45" width="45">
                            </a>
                        </dt>
                        <dd class="mb-0">
                            <a href="{:url('member/index//index',['uid'=>$v['uid']])}" class="uk-username" data-id="{$v.uid}">{$v.nick_name}</a>
                            <p class="mb-0">
                                提问:{$v.question_count} &nbsp;&nbsp;获赞:{$v.agree_count}
                            </p>
                        </dd>
                        <dd class="position-absolute" style="right: 5px;bottom: 5px">
                            <a href="javascript:;" class="cursor-pointer btn btn-sm btn-primary px-3 {$v['is_focus'] ? 'active' : ''}" onclick="UK.User.focus(this,'user','{$v.uid}')" >{$v['is_focus'] ? '已关注' : '关注'}</a>
                        </dd>
                    </dl>
                </div>
                {/volist}
            </div>
            <div class="my-2 clearfix">
                <a href="javascript:;" class="float-right text-muted rand-user">换一批</a>
            </div>
            <div class="first-login-footer clearfix">
                <a class="float-left go-back text-muted" href="javascript:;">跳过</a>
                <button class="btn btn-primary float-right btn-sm" onclick="parent.layer.closeAll();" type="button">完成</button>
            </div>
        </form>
    </div>
</div>
<script>
    let totalPage= parseInt("{$total}");
    let rand = parseInt(Math.random() * (totalPage - 1 + 1) + 1);
    function getTopic(page)
    {
        $.ajax({
            url: baseUrl+'/people/get_hot_user?_ajax=1',
            dataType: '',
            type: 'post',
            data: {page:page},
            success: function (result) {
                $('#userResult').html(result.data.html);
            }
        });
    }

    $(document).on('click', '.rand-user', function (e) {
        getTopic(rand);
    });
</script>
{/if}

<script>
    /*跳过*/
    $(document).on('click', '.go-back', function (e) {
        parent.layer.closeAll();
    });
    /*提交*/
    $(document).on('click', '.login-btn', function (e) {
        let that = this;
        let form = $($(that).parents('form')[0]);
        $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
               if(result.code===1)
               {
                   location.href = result.data.url;
               }else{
                   layer.msg(result.msg);
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