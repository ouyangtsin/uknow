<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0 mb-1">
                <div class="uk-nav-container clearfix bg-white">
                    <ul>
                        <li class="{if $sort=='new'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('column/detail',['id'=>$column_info['id']])}" data-type="question">最新</a></li>
                        <li class="{if $sort=='recommend'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('column/detail',['id'=>$column_info['id'],'sort'=>'recommend'])}" data-type="article">推荐</a></li>
                        <li class="{if $sort=='hot'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('column/detail',['id'=>$column_info['id'],'sort'=>'hot'])}" data-type="about">热门</a></li>
                    </ul>
                </div>
                <div id="uk-index-main" class="bg-white">
                    {if !empty($_list)}
                    <div class="uk-common-list py-2">
                    {volist name="_list" id="v"}
                        <dl class="article">
                            {if $v['cover']}
                            <dt class="col-sm-12 px-0">
                                <img src="{$v['cover']|default='/static/common/image/default-cover.svg'}" class="rounded uk-cut-img" alt="{$v['title']}">
                            </dt>
                            {/if}
                            <dd class="col-sm-12 m-0 px-0" {if !$v['cover']}style="width:100%"{/if}>
                                <h2>
                                    <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']}</a>
                                </h2>
                                <div class="content uk-two-line">
                                    {$v.message|raw}
                                </div>
                                {if $v['topics']}
                                <div class="uk-tag">
                                    {volist name="$v['topics']" id="topic"}
                                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                                    {/volist}
                                </div>
                                {/if}
                                <div class="uk-common-footer">
                                    <a href="{$v['user_info']['url']}" class="uk-username avatar" data-id="{$v.uid}">
                                        <img src="{$v['user_info']['avatar']}" alt="" class="uk-border-circle" style="width: 22px;height: 22px">
                                    </a>
                                    <a href="{$v['user_info']['url']}" class="uk-user-name name">{$v['user_info']['name']}</a>
                                    <span> | {:date_friendly($v['create_time'])}</span>
                                    <div class="float-right">
                                        <label><i class="icon-eye"></i> {$v['view_count']}</label>
                                    </div>
                                </div>
                            </dd>
                            <div class="clear"></div>
                        </dl>
                    {/volist}
                    {$page|raw}
                    </div>
                    {else/}
                    <p class="text-center p-3 text-color-info">
                        <img src="/static/common/image/empty.svg">
                        <span class="mt-3 d-block ">暂无记录</span>
                    </p>
                    {/if}
                </div>
            </div>
            <div class="uk-right col-md-3 col-sm-12 px-xs-0">
                <div class="bg-white p-3">
                    <p class="text-center mb-2">
                        <img src="{$column_info['cover']}" style="width: 60px;height: 60px;border-radius: 50%" alt="">
                    </p>
                    {if $column_info['verify']==0}<p class="text-center mb-2"><span class="badge badge-danger font-8">审核中</span></p>{/if}
                    <h3 class="my-2 font-12 text-center">{$column_info.name}</h3>
                    <p class="text-color-info my-2 uk-two-line font-9 text-center">{$column_info.description}</p>
                    <div class="text-center font-9 py-1 mb-2">
                        <a href="javascript:;" class="text-color-info"> {$column_info.post_count|num2string} 内容 </a><span class="text-color-info"> | </span>
                        <a href="javascript:;" class="text-color-info"> {$column_info.focus_count|num2string} 关注 </a>
                    </div>
                    <div class="mt-3 d-flex">
                        {if $user_id}
                        <button onclick="UK.User.focus(this,'column','{$column_info.id}')" class="flex-fill btn btn-primary btn-sm px-3 {if !$focus}active{/if} mr-2">{if !$focus}已关注{else}关注{/if}</button>
                        <a href="{:url('ask/article/publish',['column_id'=>$column_info['id']])}" class="flex-fill btn btn-outline-primary text-primary btn-sm mr-2"> <i class="uil-plus"></i> 发文</a>
                        {if $user_id!=$column_info.uid}
                        <button  onclick="UK.User.inbox('{$column_info['user_info']['user_name']}')" class="flex-fill btn btn-outline-primary btn-sm mr-2">私信</button>
                        {/if}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>