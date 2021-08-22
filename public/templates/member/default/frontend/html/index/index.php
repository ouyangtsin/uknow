{if !$_pjax}
<style>
    .banner-bg{
        background-image: url("{$theme_path}/images/loginback.png");
        background-size: contain;
    }
</style>
<div class="uk-main-wrap mb-2" style="margin-top: -1rem;">
    <div class="uk-home-banner">
        <span class="banner-bg" id="bannerBg"></span>
        <i></i>
        <div class="uk-author-info">
            <div class="container">
                <div class="uk-author-box">
                    <dl class="position-relative">
                        <dt class="text-center"><a href="{$user.url}"><img src="{$user.avatar}"></a></dt>
                        <dd>
                            <h2>
                                <a href="{$user.url}" style="color: #fff">{$user.name}</a>
                                <img src="{$user.group_icon}" style="height: 24px" data-uk-tooltip="{$user.user_group_name}">
                                {if $user.status==3}
                                <span class="badge badge-danger">已封禁</span>
                                {/if}
                            </h2>
                            <div>
                                <p>{$user.sex==1 ? '男' : ($user.sex==2 ? '女' : '保密')}</p>
                            </div>
                            <em class="uk-two-line">{$user.signature|default='这家伙还没有留下自我介绍～'}</em>
                            <div>
                                <em>关注: {$user.friend_count}</em><span class="uk-divider"></span>
                                <em>粉丝: {$user.fans_count}</em><span class="uk-divider"></span>
                                <em>访问: {$user.views_count}</em>
                            </div>
                        </dd>
                        {if $user_id}
                        <div class="position-absolute uk-home-user-btn">
                            {if $user_id==$user.uid}
                            <a class="btn btn-primary btn-sm px-4" href="{:url('member/setting/profile')}">编辑个人资料</a>
                            {else/}
                            <a class="btn btn-primary btn-sm px-4 {if $user['has_focus']}active{/if}" onclick="UK.User.focus(this,'user','{$user.uid}')">{$user['has_focus'] ? '已关注' : '关注'}</a>
                            <a class="btn btn-outline-primary btn-sm px-4 mr-2" href="javascript:;" onclick="UK.User.inbox('{$user.user_name}')">私信</a>
                            {/if}
                        </div>
                        {/if}
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}
<div class="container" id="uk-index-main">
    <div class="row">
        <div class="bg-white col-9">
            <div class="uk-nav-container py-2" >
                <ul >
                    <li class="{if $type=='dynamic'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'dynamic'])}">动态</a></li>
                    <li class="{if $type=='question'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'question'])}">提问 {$question_count}</a></li>
                    <li class="{if $type=='answer'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'answer'])}">回答 {$answer_count}</a></li>
                    <li class="{if $type=='article'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'article'])}">文章 {$article_count}</a></li>
                    <li class="{if $type=='column'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'column'])}">专栏</a></li>
                   <!-- <li class="{if $type=='focus'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'focus'])}">关注</a></li>
                    <li class="{if $type=='favorite'}active{/if}"><a data-pjax="uk-index-main" href="{:url('member/index/index',['name'=>$user['url_token'],'type'=>'favorite'])}">收藏 {$favorite_count}</a></li>-->
                </ul>
            </div>
            <div class="px-3 pb-3">
                {:widget('member/get_user_post',['uid'=>$user['uid'],'type'=>$type])}
            </div>
        </div>

        <div class="uk-right col-md-3 col-sm-12 px-xs-0">
            {if $user_id && $user_id==$user.uid}
            <div class="uk-mod bg-white sidebar-hot-topic p-3 mb-1">
                <div class="uk-mod-head d-flex">
                    <div class="flex-fill">
                        <a href="{:url('member/manager/index')}">
                            <img src="{$theme_path}/images/creator_entrance.png" height="45px">
                        </a>
                    </div>
                    <div class="flex-fill">
                        <a href="{:url('member/manager/index')}">
                        创作中心
                        <p class="text-muted font-8">去管理我的账户...</p>
                        </a>
                    </div>
                    <div class="flex-fill" style="line-height: 45px">
                        <a href="{:url('member/manager/index')}"><i class="icon-chevron-right text-muted"></i></a>
                    </div>
                </div>
                <div class="uk-mod-body">

                </div>
            </div>
            {/if}
            <div class="uk-mod bg-white sidebar-hot-topic p-3 mb-1">
                <div class="uk-mod-head">
                    <p class="mod-head-title">个人成就</p>
                </div>
                <div class="uk-mod-body">

                </div>
            </div>
        </div>
    </div>
</div>