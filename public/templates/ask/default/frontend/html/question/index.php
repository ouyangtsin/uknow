<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0 mb-1">
                <div class="uk-nav-container clearfix  bg-white px-3 rounded-top-right rounded-top-left">
                    <h2 class="float-left"><i class="icon icon-list"></i> 全部问题</h2>
                    <ul class="float-right uk-pjax-tab">
                        <li class="{if $sort=='recommend'}active{/if}"><a data-pjax="uk-index-main" href="{:url('question/index',['sort'=>'recommend','category_id'=>$category])}"> 推荐 </a></li>
                        <li class="{if $sort=='new'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('question/index',['sort'=>'new','category_id'=>$category])}" > 最新 </a></li>
                        <li class="{if $sort=='hot'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('question/index',['sort'=>'hot','category_id'=>$category])}"> 热门 </a></li>
                        <li class="{if $sort=='unresponsive'}active{/if}"><a data-pjax="uk-index-main"  href="{:url('question/index',['sort'=>'unresponsive','category_id'=>$category])}"> 待回答 </a></li>
                    </ul>
                </div>
                {if $setting.enable_category}
                <div class="p-3 bg-white border-bottom uk-pjax-buttons">
                    <a href="{:url('question/index',['sort'=>$sort,'category_id'=>0])}" data-pjax="uk-index-main" class="mb-2 btn btn-sm px-3 mx-1 {$category==0 ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">全部</a>
                    {volist name="category_list" id="v"}
                    <a href="{:url('question/index',['sort'=>$sort,'category_id'=>$v['id']])}" data-pjax="uk-index-main" class="mb-2 btn btn-sm mx-1 px-3n {$category==$v['id'] ? 'btn-primary text-white' : 'btn-outline-primary text-primary'}">{$v.title}</a>
                    {/volist}
                </div>
                {/if}
                <div id="uk-index-main" class="bg-white px-3 rounded-bottom-right rounded-bottom-left">
                    {:widget('common/lists',['item_type'=>'question','sort'=>$sort,'category_id'=>$category])}
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