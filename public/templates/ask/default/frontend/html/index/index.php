<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0 mb-1">
                {if $theme_config['banner_enable']}
                <div class="uk-index-banner mb-2" style="height: 250px">
                    <div id="uk-index-banner" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#uk-index-banner" data-slide-to="0" class="active"></li>
                            <li data-target="#uk-index-banner" data-slide-to="1"></li>
                            <li data-target="#uk-index-banner" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                            {if $theme_config['banner_image1']}
                            <div class="carousel-item active">
                                <a href="{$theme_config['banner_link1']}">
                                    <img src="{$theme_config['banner_image1']}" class="d-block w-100 rounded" style="height: 250px">
                                    <div class="carousel-caption d-none d-md-block">
                                        <p>{$theme_config['banner_text1']}</p>
                                    </div>
                                </a>
                            </div>
                            {/if}
                            {if $theme_config['banner_image2']}
                            <div class="carousel-item">
                                <a href="{$theme_config['banner_link2']}">
                                    <img src="{$theme_config['banner_image2']}" class="d-block w-100 rounded" style="height: 250px">
                                    <div class="carousel-caption d-none d-md-block">
                                        <p>{$theme_config['banner_text2']}</p>
                                    </div>
                                </a>
                            </div>
                            {/if}
                            {if $theme_config['banner_image3']}
                            <div class="carousel-item">
                                <a href="{$theme_config['banner_link3']}">
                                    <img src="{$theme_config['banner_image3']}" class="d-block w-100 rounded" style="height: 250px">
                                    <div class="carousel-caption d-none d-md-block">
                                        <p>{$theme_config['banner_text3']}</p>
                                    </div>
                                </a>
                            </div>
                            {/if}
                        </div>
                        <a class="carousel-control-prev" href="#uk-index-banner" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#uk-index-banner" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
                {/if}
                <div class="uk-nav-container clearfix bg-white px-3 rounded-top-left rounded-top-right">
                    <h2 class="float-left"><i class="icon-explore"></i> 发现</h2>
                    <ul class="float-right uk-pjax-tab">
                        {if $user_id}
                        <li class="{if $sort=='focus'}active{/if}"><a data-pjax="uk-index-main" href="{:url('index/index',['sort'=>'focus'])}"> 关注 </a></li>
                        {/if}
                        <li class="{if $sort=='recommend'}active{/if}"><a data-pjax="uk-index-main" href="{:url('index/index',['sort'=>'recommend'])}"> 推荐 </a></li>
                        <li class="{if $sort=='new'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('index/index',['sort'=>'new','category_id'=>$category])}" > 最新 </a></li>
                        <li class="{if $sort=='hot'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('index/index',['sort'=>'hot','category_id'=>$category])}"> 热门 </a></li>
                    </ul>
                </div>
                {if $sort!='focus' && $sort!='recommend' && $setting.enable_category}
                <div class="p-3 bg-white border-bottom uk-pjax-buttons">
                    <a href="{:url('index/index',['sort'=>$sort,'category_id'=>0])}" data-pjax="uk-index-main" class="mb-2 btn btn-sm px-3 mx-1 {$category==0 ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">全部</a>
                    {volist name="category_list" id="v"}
                    <a href="{:url('index/index',['sort'=>$sort,'category_id'=>$v['id']])}" data-pjax="uk-index-main" class="mb-2 btn btn-sm mx-1 px-3n {$category==$v['id'] ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">{$v.title}</a>
                    {/volist}
                </div>
                {/if}
                <div id="uk-index-main" class=" bg-white px-3 rounded-bottom-left rounded-bottom-right pb-3">
                    {:widget('common/lists',['sort'=>$sort,'category_id'=>$category])}
                </div>
            </div>
            <div class="uk-right col-md-3 col-sm-12 px-xs-0">
                {:widget('sidebar/write_nav')}
                {:widget('sidebar/hot_topic',['uid'=>$user_id])}
                {:widget('sidebar/hot_users',['uid'=>$user_id])}
            </div>
        </div>
    </div>
</div>