<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 bg-white px-0 rounded">
                <div class="p-3">
                    <form class="uk-form" method="post" action="{:url('article/publish')}">
                        <input type="hidden" name="id" value="{$article_info.id|default=0}">
                        <input type="hidden" name="wait_time">
                        {:token_field()}
                        <div class="form-group d-flex">
                            <div class="flex-fill">
                                <input class="form-control" type="text" name="title" value="{$article_info['title']|default=''}" placeholder="文章标题">
                            </div>
                            {if($column_list)}
                            <div class="flex-fill ml-2">
                                <select class="form-control" name="column_id">
                                    <option value="0">选择专栏</option>
                                    {volist name="column_list" id="v"}
                                    <option value="{$v.id}" {if isset($article_info['column_id']) && $v['id']==$article_info['column_id']}selected{/if}>{$v.name}</option>
                                    {/volist}
                                </select>
                            </div>
                            {/if}
                            {if $article_category_list && $setting.enable_category}
                            <div class="flex-fill ml-2">
                                <select class="form-control" name="category_id">
                                    <option value="0">选择分类</option>
                                    {volist name="article_category_list" id="v"}
                                    <option value="{$v.id}" {if isset($article_info['category_id']) && $article_info['category_id']==$v['id']}selected {/if}>{$v.title}</option>
                                    {/volist}
                                </select>
                            </div>
                            {/if}
                        </div>

                        <div class="form-group">
                            <label>文章封面:</label>
                            <div class="article-cover-box">
                                <div id="fileList_cover" class="uploader-list"></div>
                                <div id="filePicker_cover">
                                    <a href="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                        <img class="image_preview_info" src="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                    </a>
                                </div>
                                <input type="hidden" name="cover" value="{$article_info['cover']|default=''}" class="article-cover">
                            </div>
                        </div>

                        <div class="form-group ">
                            {:hook('editor',['name'=>'message','cat'=>'article','value'=>isset($article_info['message']) ? $article_info['message']:''])}
                        </div>

                        {if !isset($article_info['id'])}
                        <div class="form-group mb-3">
                            <div class="page-detail-topic">
                                <ul class="d-inline p-0" id="uk-topic-list">
                                    {if !empty($article_info['topics'])}
                                    {volist name="article_info['topics']" id="v"}
                                    <li class="d-inline uk-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}">{$v.title}</a></li>
                                    {/volist}
                                    <input type="hidden" name="topics" value="{:implode(',',array_column($article_info['topics'],'id'))}">
                                    {/if}
                                </ul>
                                <a href="javascript:;" class="text-primary font-9 uk-ajax-open d-inline" data-url="{:url('ask/ajax/topic',['item_type'=>'article','item_id'=>isset($article_info['id']) ? $article_info['id'] : 0])}" data-title="编辑话题"><i class="icon-add"></i> 添加话题</a>&nbsp;&nbsp;<span class="font-9 text-muted">(最多输入5个)</span>
                            </div>
                        </div>
                        {/if}

                        <div class="form-group">
                            <button type="button" class="btn btn-primary btn-sm px-4 uk-ajax-form mr-3">发表文章</button>
                            {if !isset($article_info['id'])}
                            <!--<button type="button" class="btn btn-outline-primary btn-sm px-4 mr-3 uk-timing-publish">定时发布</button>-->
                            {/if}
                            <button type="button" onclick="UK.User.draft(this,'article','{$article_info.id|default=0}')" class="btn btn-outline-primary px-4 btn-sm mr-3">存草稿</button>
                            <button type="button" class="btn btn-outline-primary btn-sm uk-preview px-4">预览</button>
                        </div>

                        {if !isset($article_info['id'])}
                        <script id="timing-publish-modal" type="text/html">
                            <div class="rounded p-3">
                                <div class="form-group">
                                    <label for="timing"><input type="text" id="timing" placeholder="选择定时发布时间" class="form-control"></label>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm px-4 mr-3 select-choose">确定选择</button>
                            </div>
                        </script>
                        {/if}
                    </form>
                </div>
			</div>

            <div class="uk-right col-md-3 col-sm-12">
                {:hook('content_ocr',['type'=>'article','element'=>''])}
			</div>
		</div>
	</div>
</div>