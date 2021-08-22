<div id="uk-question-fixed" class="bg-white fixed-top d-xs-none" style="display: none">
    <div class="container">
        <div class="overflow-hidden">
            <div class="float-left">
                <h3 class="mb-0"><i class="iconfont icon-shang text-danger font-12"></i> {$question_info.title}</h3>
            </div>
            <div class="float-right">
                {if isset($question_info['look_enable']) && $question_info['look_enable']}
                <button onclick="UK.User.look(this,'{$question_info.id}')" class="btn btn-primary btn-sm px-3 mr-3 {if $question_info['has_look']}active{/if}">{$question_info['has_look'] ? '已围观' : '围观问题'}</button>
                {else/}
                <button onclick="UK.User.focus(this,'question','{$question_info.id}')" class="btn btn-primary btn-sm px-3 mr-3 {if $question_info['has_focus']}active{/if}">{$question_info['has_focus'] ? '已关注' : '关注问题'}</button>
                {/if}
                <button class="btn btn-outline-primary btn-sm px-3 uk-answer-editor" data-question-id="{$question_info['id']}" data-answern-id="0">回答问题</button>
            </div>
        </div>

        <div class="uk-reward-record mt-2 text-danger">
            <b id='timer'>3天4小时后到期</b>
        </div>
    </div>
</div>
<div class="uk-question-container bg-white py-3">
    <div class="container position-relative">
        {if !empty($question_info['topics'])}
        <div class="page-detail-topic mb-2">
            <ul id="uk-topic-list" class="d-inline p-0">
                {volist name="question_info['topics']" id="v"}
                <li class="d-inline uk-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="uk-topic" data-id="{$v.id}">{$v.title}</a></li>
                {/volist}
            </ul>

            {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}
            <a href="javascript:;" class="uk-ajax-open d-inline" data-url="{:url('ask/ajax/topic',['item_type'=>'question','item_id'=>$question_info['id']])}" data-title="编辑话题"><i class="icon-edit1"> </i></a>
            {/if}
        </div>
        {/if}
        <div class="extend-info position-absolute d-sm-none d-xs-none">
            <div class="d-flex text-center text-muted">
                <dl class="flex-fill mb-0 mr-4">
                    <dt class="font-weight-bold">{$question_info['focus_count']}</dt>
                    <dd>关注</dd>
                </dl>
                <dl class="flex-fill mb-0">
                    <dt>{$question_info['view_count']}</dt>
                    <dd>浏览</dd>
                </dl>
            </div>
        </div>
        <div class="uk-content-info">
            <h2 class="mb-2 title">
                {if $question_info.set_top}
                <i class="iconfont icon-zhiding text-warning font-14"></i>
                {/if}
                {$question_info.title}
            </h2>
            <div class="uk-content position-relative" id="question-content">
                <div id="show-all" >{$question_info.detail|raw}</div>
                {if $question_info.detail}
                    <div class="uk-question-show uk-alpha-hidden" style="display: none">
                        <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> 阅读全文</span>
                    </div>
                    <div class="uk-question-hide uk-alpha-hidden" style="display: none;background:none;position: inherit;height: auto">
                        <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> 收起全文</span>
                    </div>
                {/if}
            </div>
            <div class="actions mt-3">
                <label class="mr-3">
                    <button onclick="UK.User.focus(this,'question','{$question_info.id}')" class="btn btn-primary btn-sm px-3 {if $question_info['has_focus']}active{/if}">{$question_info['has_focus'] ? '已关注' : '关注问题'}</button>
                </label>
                <label class="mr-3">
                    <button class="btn btn-outline-primary btn-sm px-3 uk-answer-editor" data-question-id="{$question_info['id']}" data-answern-id="0">回答问题</button>
                </label>
                <label class="mr-4">
                    <button class="btn btn-outline-secondary btn-sm px-3" data-title="邀请回答" onclick="UK.User.invite(this,'{$question_info.id}')">邀请回答</button>
                </label>
                <label class="mr-3">
                    <a href="javascript:;" class="{$question_info['vote_value']==1 ? 'active' : ''}" onclick="UK.User.agree(this,'question','{$question_info.id}');" title="这是个好问题"><i class="icon-thumb_up"></i> 好问题</a>
                </label>
                <label class="mr-3 uk-ajax-open" data-title="评论问题" data-url="{:url('comment/question?question_id='.$question_info['id'])}">
                    <a href="javascript:;"><i class="icon-chat"></i> {$question_info['comment_count'] ? $question_info['comment_count'].'条' : '添加'}评论</a>
                </label>
                <label class="mr-3">
                    <a href="javascript:;" onclick="UK.User.favorite(this,'question','{$question_info.id}')"><i class="icon-turned_in"></i>{if $checkFavorite} 已收藏{else}收藏{/if} </a>
                </label>
                <label class="mr-3">
                    <a href="javascript:;" {if !$checkReport} onclick="UK.User.report(this,'question','{$question_info.id}')" {/if} ><i class="icon-warning"></i> {if $checkReport}已举报{else}举报{/if}</a>
                </label>
                <div class="mr-3 uk-popover d-inline-block">
                    <a href="javascript:;" class="popover-title">
                        <i class="icon-share"></i> 分享
                    </a>
                    <div class="popover-content">
                        <div class="text-left d-block py-2" style="min-width: 100px">
                            <a href="javascript:;"  class="dropdown-item uk-clipboard" data-clipboard-text="{:url('question/detail',['id'=>$question_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                            <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="iconfont icon-weibo text-warning"></i> 新浪微博</a>
                            <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="iconfont icon-QQ text-primary"></i> 腾讯空间</a>
                            <div class="uk-qrcode-container" data-share="{:url('question/detail',['id'=>$question_info.id],true,true)}">
                                <a href="javascript:;" class="dropdown-item "><i class="iconfont icon-weixin text-success"></i> 微信扫一扫</a>
                                <div class="uk-qrcode text-center py-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <label class="uk-question-show mr-3" style="display: none">
                    <a href="javascript:;"><i class="icon-chevrons-down"></i> 展开</a>
                </label>
                <label class="uk-question-hide mr-3" style="display: none;">
                    <a href="javascript:;"><i class="icon-chevrons-up"></i> 收起</a>
                </label>
                {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$question_info['uid'])}
                <div class="uk-popover d-inline-block">
                    <a href="javascript:;" class="popover-title">
                        <i class="icon-more-horizontal"></i>
                    </a>
                    <div class="popover-content">
                        <div class="text-center d-block py-2" style="min-width: 100px">
                            <a href="{:url('question/publish?id='.$question_info['id'])}" class="dropdown-item"><span>编辑问题</span</a>
                            <a class="uk-ajax-get dropdown-item"  href="javascript:;" data-confirm="是否删除该问题?" data-url="{:url('question/remove_question',['id'=>$question_info['id']])}">
                                <span>删除问题</span>
                            </a>
                            {if $user_info['group_id']==1 || $user_info['group_id']==2}
                            <a href="javascript:;" data-confirm="是否推荐该问题?" class="uk-ajax-get dropdown-item" data-url="{:url('question/manager',['id'=>$question_info['id'],'type'=>$question_info['is_recommend'] ? 'un_recommend' : 'recommend'])}">
                                <span>{$question_info['is_recommend'] ? '取消推荐' : '推荐问题'}</span>
                            </a>
                            <a  href="javascript:;" data-confirm="是否置顶该问题?" class="uk-ajax-get dropdown-item" data-url="{:url('question/manager',['id'=>$question_info['id'],'type'=> $question_info['set_top'] ? 'unset_top' : 'set_top'])}">
                                <span>{$question_info['set_top'] ? '取消置顶' : '置顶问题'}</span>
                            </a>
                            {/if}
                        </div>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>
<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-xs-0 mb-1">
                {if $user_id}
                <div id="answerEditor" class="mb-2" style="display: none"></div>
                {/if}

                <div id="answer-container">
                    {if $answer_id}
                    <p class="uk-view-all-answer bg-white p-3 text-center mb-2"><a  data-pjax="answer-container" href="{:url('question/detail',['id'=>$question_info['id']])}">查看全部 <span class="uk-answer-count">{$question_info.answer_count}</span> 个回答</a></p>
                    {else/}
                    <div class="uk-mod bg-white px-3 pt-3">
                        <div class="uk-mod-head mb-0">
                            <p class="mod-head-title">
                                共 <span class="uk-answer-count">{$question_info.answer_count}</span> 个回答
                            </p>
                            <div class="uk-popover mod-head-more">
                                <a href="javascript:;" class="popover-title uk-sort-show">
                                    <span>{$sort=='hot' ? '热门排序':'默认排序'}</span> <i class="icon-select-arrows"></i>
                                </a>
                                <div class="popover-content">
                                    <div class="text-center d-block py-2 uk-nav uk-dropdown-nav text-center uk-answer-sort" style="min-width: 100px">
                                        <div class="{$sort=='new' ? 'active':''} py-1"><a href="{:url('question/detail',['id'=>$question_info['id'],'sort'=>'new'])}"  data-pjax="answer-container">默认排序</a> </div>
                                        <div class="{$sort=='hot' ? 'active':''} py-1"><a href="{:url('question/detail',['id'=>$question_info['id'],'sort'=>'hot'])}"  data-pjax="answer-container">热门排序 </a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/if}
                    <div class="uk-answer-body uk-answer-list" id="uk-answer-list" data-id="{$question_info.id}" data-aid="{$answer_id}">
                        {if !empty($data)}
                        {volist name="data" id="v"}
                        <div class="uk-answer-item p-3 mb-1 bg-white" id="question-answer-{$v.id}" data-answer-id="{$v.id}">
                            <div class="user-details-card pt-0 pb-2">
                                <div class="user-details-card-avatar" style="position: relative">
                                    {if $v.is_anonymous}
                                    <a href="javascript:;">
                                        <img src="/static/common/image/default-avatar.svg" alt="匿名用户" data-toggle="popover" title="匿名用户" style="width: 40px;height: 40px">
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="uk-username" data-id="{$v.uid}" data-toggle="popover" title="{$v['user_info']['name']}">
                                        <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
                                    </a>
                                    {/if}
                                </div>
                                <div class="user-details-card-name">
                                    {if $v.is_anonymous}<a href="javascript:;" data-toggle="popover" title="匿名用户">匿名用户</a>{else/}<a href="{$v['user_info']['url']}" data-toggle="popover" title="{$v['user_info']['name']}">{$v['user_info']['name']}</a>{/if} <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
                                </div>
                                {if $v['is_best']}
                                <div class="uk-answer-best">
                                    <i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>
                                </div>
                                {/if}
                            </div>
                            <div class="uk-content">
                                <div class="uk-answer-content overflow-hidden">
                                    {:html_entity_decode($v.content)}
                                </div>
                                {if $v.content}
                                <div class="uk-answer-show uk-alpha-hidden" style="display: none">
                                    <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> 阅读全文</span>
                                </div>
                                <div class="uk-answer-hide uk-alpha-hidden mt-3" style="display: none;background:none;position: inherit;height: auto">
                                    <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> 收起全文</span>
                                </div>
                                {/if}
                            </div>
                            <div class="answer-btn-actions mt-3">
                                <label class="mr-1">
                                    <a href="javascript:;" class="uk-ajax-agree {if $v['vote_value']==1}active{/if}"  onclick="UK.User.agree(this,'answer','{$v.id}');">
                                        <i class="icon-thumb_up"></i> 赞同 <span> {$v.agree_count}</span>
                                    </a>
                                </label>

                                <label class="mr-3 ">
                                    <a href="javascript:;" class="uk-ajax-against {if $v['vote_value']==-1}active{/if}"  onclick="UK.User.against(this,'answer','{{$v.id}}');">
                                        <i class="icon-thumb_down"></i>
                                    </a>
                                </label>

                                <label class="mr-3">
                                    <a href="javascript:;" class="uk-ajax-open" data-title="评论回答" data-url="{:url('comment/answer?answer_id='.$v['id'])}">
                                        <i class="icon-chat"></i> {$v.comment_count ? $v.comment_count.'条' : '添加'}评论
                                    </a>
                                </label>

                                {if $user_id}
                                <label class="mr-3">
                                    <a href="javascript:;" onclick="UK.User.report(this,'answer','{$v.id}')" ><i class="icon-warning"></i> 举报</a>
                                </label>

                                <label class="mr-3">
                                    <a href="javascript:;" onclick="UK.User.favorite(this,'answer','{$v.id}')"><i class="icon-turned_in"></i> 收藏</a>
                                </label>

                                <label class="mr-3">
                                    <a href="javascript:;"  {if $v.has_thanks} class="active" {else/}onclick="UK.User.thanks(this,'{$v.id}')"{/if}>
                                        <i class="icon-favorite"></i> <span>{$v.has_thanks ? '已喜欢' : '喜欢'}</span>
                                    </a>
                                </label>

                                {if !$v.has_uninterested}
                                <label class="mr-3">
                                    <a href="javascript:;" onclick="UK.User.uninterested(this,'answer','{$v.id}')">
                                        <i class="icon-report"></i> 不感兴趣
                                    </a>
                                </label>
                                {/if}

                                {if $setting.reward_enable}
                                <label>
                                    <a href="javascript:;" data-title="打赏回答" class="mr-3 uk-ajax-open" data-url="">
                                        <i class="iconfont icon-shang font-9"></i> 打赏
                                    </a>
                                </label>
                                {/if}

                                {/if}
                                {if ($user_id && ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)  && !$v['is_best'] && !$best_answer_count)}
                                <label class="mr-3">
                                    <a href="javascript:;"  class="uk-ajax-get" data-confirm="是否把该回答设为最佳?" data-url="{:url('question/set_answer_best?answer_id='.$v['id'])}">
                                        <i class="iconfont">&#xe6f7;</i> 最佳
                                    </a>
                                </label>
                                {/if}
                                <div class="mr-3 uk-popover d-inline-block">
                                    <a href="javascript:;" class="popover-title">
                                        <i class="icon-share"></i> 分享
                                    </a>
                                    <div class="popover-content">
                                        <div class="text-left d-block py-2" style="min-width: 100px">
                                            <a href="javascript:;"  class="dropdown-item uk-clipboard" data-clipboard-text="{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                                            <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="iconfont icon-weibo text-warning"></i> 新浪微博</a>
                                            <a href="javascript:;" onclick="UK.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="iconfont icon-QQ text-primary"></i> 腾讯空间</a>
                                            <div class="uk-qrcode-container" data-share="{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}">
                                                <a href="javascript:;" class="dropdown-item "><i class="iconfont icon-weixin text-success"></i> 微信扫一扫</a>
                                                <div class="uk-qrcode text-center py-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--问题回答操作栏钩子-->
                                {:hook('question_answer_bottom_action',$v)}
                                <div class="uk-share clearfix d-inline-block">
                                    <div class="social-share" data-disabled="google,twitter,facebook,linkedin,douban"></div>
                                </div>
                                {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$v['uid'])}
                                <div class="mr-3 uk-popover d-inline-block">
                                    <a href="javascript:;" class="popover-title">
                                        <i class="icon-more-horizontal"></i>
                                    </a>
                                    <div class="popover-content">
                                        <div class="text-center d-block py-2" style="min-width: 100px">
                                            <a href="javascript:;"  class="dropdown-item uk-answer-editor" data-question-id="{$v.question_id}" data-answer-id="{$v['id']}">编辑</a>
                                            <a href="javascript:;" data-toggle="popover" title="删除回答" class="dropdown-item uk-ajax-get" data-confirm="是否删除该回答?" data-url="{:url('question/delete_answer?answer_id='.$v['id'])}">删除</a>
                                        </div>
                                    </div>
                                </div>
                                {/if}
                            </div>
                        </div>
                        {/volist}
                        {if $page_render}
                        <div class="bg-white p-3">
                            {$page_render|raw}
                        </div>
                        {/if}
                        {else/}
                        <p class="text-center text-muted p-3 bg-white">
                            <img src="/static/common/image/empty.svg">
                            <span class="d-block">暂无回答</span>
                        </p>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="uk-right col-md-3 col-sm-12 px-0">
                {if !$question_info['is_anonymous']}
                <div class="uk-mod bg-white p-3 mb-1">
                    <div class="uk-mod-head">
                        <p class="mod-head-title">关于作者</p>
                    </div>
                    <div class="uk-mod-body">
                        <dl class="overflow-hidden mb-0 pb-2 border-bottom">
                            <dt class="float-left">
                                <a href="{$question_info['user_info']['url']}" class="uk-username" data-id="{$question_info.uid}">
                                    <img src="{$question_info['user_info']['avatar']|default='/static/common/image/default-avatar.svg'}" class="rounded" width="45" height="45">
                                </a>
                            </dt>
                            <dd class="float-right" style="width:calc(100% - 60px)">
                                <a href="{$question_info['user_info']['url']}" class="d-block">
                                    <strong>{$question_info['user_info']['name']}</strong>
                                    <img src="{$question_info['user_info']['group_icon']}" height="20">
                                </a>
                                <p class="mb-0 font-8 text-muted uk-one-line">{$question_info['user_info']['signature']}</p>
                            </dd>
                        </dl>
                        <div class="d-flex text-center pt-3 text-muted">
                            <a href="{:url('member/index//index',['uid'=>$question_info['uid'],'type'=>'publish_answer'])}" target="_blank" class="flex-fill mb-0">
                                <dl>
                                    <dt>{$publish_answer_count}</dt>
                                    <dd>回答</dd>
                                </dl>
                            </a>
                            <a href="{:url('member/index//index',['uid'=>$question_info['uid'],'type'=>'publish_article'])}" target="_blank" class="flex-fill mb-0">
                                <dl>
                                    <dt>{$publish_article_count}</dt>
                                    <dd>文章</dd>
                                </dl>
                            </a>
                            <a href="{:url('member/index//index',['uid'=>$question_info['uid'],'type'=>'publish_question'])}" target="_blank" class="flex-fill mb-0">
                                <dl>
                                    <dt>{$publish_question_count}</dt>
                                    <dd>问题</dd>
                                </dl>
                            </a>
                        </div>
                        {if isset($user_id) && $user_id!=$question_info['uid']}
                        <div class="user-btn-group d-flex text-center">
                            <div class="mr-2 flex-fill">
                                <button class="btn btn-primary btn-sm w-100 {$question_info['user_focus'] ? 'active' : ''}" onclick="UK.User.focus(this,'user','{$question_info.uid}')">{$question_info['user_focus'] ? '已关注' : '关注他'}</button>
                            </div>
                            <div class="ml-2 flex-fill">
                                <button onclick="UK.User.inbox('{$question_info['user_info']['user_name']}')" class="btn btn-outline-secondary btn-sm w-100">发私信</button>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
                {/if}

                {if $relation_question}
                <div class="uk-mod bg-white p-3 mb-1">
                    <div class="uk-mod-head mb-1">
                        <p class="mod-head-title font-12">相关问题</p>
                    </div>
                    <div>
                        {volist name="relation_question" id="v"}
                        <dl class="mb-0 py-2">
                            <dt class="d-block uk-one-line font-weight-normal font-9">
                                <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} 浏览</label>
                                <label class="mr-2 mb-0">{$v.focus_count} 关注</label>
                                <label class="mr-2 mb-0">{$v['comment_count']} 评论</label>
                            </dd>
                        </dl>
                        {/volist}
                    </div>
                </div>
                {/if}

                {if $recommend_post}
                <div class="uk-mod bg-white p-3 mb-1">
                    <div class="uk-mod-head mb-1">
                        <p class="mod-head-title font-12">推荐内容</p>
                    </div>
                    <div>
                        {volist name="recommend_post" id="v"}
                        {if $v['item_type']=='article'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block uk-one-line font-weight-normal font-9">
                                <span class="bg-primary text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">文</span> <a href="{:url('article/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} 浏览</label>
                                <label class="mr-2 mb-0">{$v['comment_count']} 评论</label>
                            </dd>
                        </dl>
                        {/if}
                        {if $v['item_type']=='question'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block uk-one-line font-weight-normal font-9">
                                <span class="bg-warning text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">问</span> <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} 浏览</label>
                                <label class="mr-2 mb-0">{$v.focus_count} 关注</label>
                                <label class="mr-2 mb-0">{$v['comment_count']} 评论</label>
                            </dd>
                        </dl>
                        {/if}
                        {/volist}
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>
<script>
    let answerId = parseInt("{$answer_id ? $answer_id : 0}");
</script>