<div class="favorite-tag-list overflow-auto">
    <div class="favorite-body">
        {volist name="list" id="v"}
        <div class="favorite-item overflow-hidden p-3 bg-white mb-2">
            <div class="favorite-item-inner float-left">
                <h4 class="favorite-item-name">{$v.title}</h4>
                <div class="mt-2 text-muted"><span class="favorite-post-count">{$v.post_count}</span> 条内容</div>
            </div>
            {if $v['is_favorite']}
            <a class="favorite-ajax-get btn btn-primary btn-sm px-3 active float-right" data-url="{:url('ajax/favorite',['item_id'=>$item_id,'item_type'=>$item_type,'tag_id'=>$v['id']])}">取消收藏</a>
            {else/}
            <a class="favorite-ajax-get btn btn-primary btn-sm px-3 float-right" data-url="{:url('ajax/favorite',['item_id'=>$item_id,'item_type'=>$item_type,'tag_id'=>$v['id']])}">收藏</a>
            {/if}
        </div>
        {/volist}
    </div>
</div>
<div class="p-3 bg-white">
    <div class="no-info text-center " {if $list}style="display:none;"{/if}>
        <p>
            <img src="/static/common/image/empty.svg">
        </p>
        <p class="uk-text-meta mt-4">
            <span class="mt-3 mr-2">暂无收藏夹</span>
            去 <a href="javascript:;" class="text-primary create-favorite">创建收藏夹</a>
        </p>
    </div>

    <div class="favorite-tag-add mt-3" style="display: none">
        <form action="{:url('member/favorite/save_favorite')}" method="post">
            <div class="form-group">
                <input type="text" name="title" class="form-control" placeholder="标签名字">
            </div>
            <div class="form-group">
                <input type="checkbox" name="is_public" > 公开收藏夹
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-outline-primary  px-3 btn-sm cancel-create">取消</button>
                <button type="button" class="btn btn-primary btn-sm px-3 save-favorite-tag">确认创建</button>
            </div>
        </form>
    </div>
</div>
