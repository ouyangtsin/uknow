<!--<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                <div class="accordion uk-accordion bg-white py-3" id="accordionNav">
                    <div class="card">
                        <div class="card-header px-4 py-2">
                            <a href="{:url('member/manager/index')}"><i class="icon-home2"></i> 管理首页</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header px-4 py-2 parent-nav collapsed" id="setting" data-toggle="collapse" data-target="#collapseSetting" aria-expanded="true" aria-controls="collapseSetting">
                            <i class="icon-settings1"></i> 账号设置
                        </div>
                        <div id="collapseSetting" class="collapse" aria-labelledby="setting" data-parent="#accordionNav">
                            <div class="card-body py-0">
                                <ul>
                                    <li class="py-2 px-4"><a > 近期热点</a></li>
                                    <li class="py-2 px-4"><a > 近期热点</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header px-4 py-2 parent-nav collapsed" id="manager" data-toggle="collapse" data-target="#collapseManager" aria-expanded="true" aria-controls="collapseManager">
                            <i class="icon-apps"></i> 内容管理
                        </div>
                        <div id="collapseManager" class="collapse" aria-labelledby="manager" data-parent="#accordionNav">
                            <div class="card-body py-0">
                                <ul>
                                    <li class="py-2 px-4"><a > 近期热点</a></li>
                                    <li class="py-2 px-4"><a > 近期热点</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="bg-white mb-2 rounded p-3">
                    <div class="d-flex text-center">
                        <dl class="flex-fill mb-0">
                            <dd>可用积分</dd>
                            <dt><i class="icon-database"></i> {:num2string($user_info['score'])}</dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>可用金额</dd>
                            <dt>￥ {:num2string($user_info['money'])} <small><a href="" class="text-primary">提现</a></small></dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>冻结金额</dd>
                            <dt>￥ {:num2string($user_info['frozen_money'])}</dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>内容总数</dd>
                            <dt>{:num2string($user_info['question_count']+$user_info['article_count']+$user_info['answer_count'])}</dt>
                        </dl>
                    </div>
                </div>

                <div class="bg-white">

                </div>
            </div>
        </div>
    </div>
</div>-->

<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input(get_setting('show_url'))])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                {if $user_id && $user_id == $user['uid']}
                <div class="bg-white mb-2 rounded p-3">
                    <div class="d-flex text-center">
                        <dl class="flex-fill mb-0">
                            <dd>可用积分</dd>
                            <dt><i class="icon-database"></i> {:num2string($user_info['score'])}</dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>可用金额</dd>
                            <dt>￥ {:num2string($user_info['money'])} <small><a href="" class="text-primary">提现</a></small></dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>冻结金额</dd>
                            <dt>￥ {:num2string($user_info['frozen_money'])}</dt>
                        </dl>
                        <dl class="flex-fill mb-0">
                            <dd>内容总数</dd>
                            <dt>{:num2string($user_info['question_count']+$user_info['article_count']+$user_info['answer_count'])}</dt>
                        </dl>
                    </div>
                </div>
                {/if}
                <div class="bg-white">
                    <div class="uk-nav-container py-2 px-3">
                        <ul >
                            <li class="{if $type=='dynamic'}active{/if} mr-3"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/manager/index',['type'=>'dynamic'])}">动态</a></li>
                            <li class="{if $type=='question'}active{/if} mr-3"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/manager/index',['type'=>'question'])}">提问 {$question_count}</a></li>
                            <li class="{if $type=='answer'}active{/if} mr-3"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/manager/index',['type'=>'answer'])}">回答 {$answer_count}</a></li>
                            <li class="{if $type=='article'}active{/if}"><a data-pjax="1" data-pjax-container="uk-index-main" href="{:url('member/manager/index',['type'=>'article'])}">文章 {$article_count}</a></li>
                        </ul>
                    </div>
                    <div id="uk-index-main" class="px-3">
                        {:widget('member/get_user_post',['uid'=>$user['uid'],'type'=>$type])}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>