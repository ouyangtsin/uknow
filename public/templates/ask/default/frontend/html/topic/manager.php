<div class="uk-main-wrap mt-2">
    <div class="container">
        <form method="post" action="{:url('topic/manager')}">
            <div class="row">
                <div class="uk-main col-md-9 col-sm-12 bg-white py-3">
                    <input type="hidden" name="topic_id" value="{$info.id}">
                    {:token_field()}
                    <div class="form-group">
                        <strong >话题名称:</strong>
                        <div class="mt-2">
                            <input class="form-control" type="text" name="title" placeholder="话题名称" value="{$info.title}">
                        </div>
                    </div>

                    <div class="form-group">
                        <strong >话题摘要:</strong>
                        <div class="mt-2">
                            <textarea class="form-control" name="seo_description" rows="4" placeholder="话题描述" >{$info.seo_description}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <strong >话题详情:</strong>
                        <div class="mt-2">
                            {:hook('editor',['name'=>'description','cat'=>'topic','value'=>$info['description']])}
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary px-3 btn-sm uk-ajax-form mr-3">提交修改</button>
                    </div>
                </div>
                <div class="uk-right col-md-3 col-sm-12">
                    <div class="bg-white p-3">
                        <div class="mb-3">
                            <strong class="mb-2">话题封面:</strong>
                            <div class="mt-2">
                                <div id="fileList_cover" class="uploader-list"></div>
                                <div id="filePicker_cover">
                                    <a href="{$info['pic']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                        <img class="image_preview_info" src="{$info['pic']|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                    </a>
                                </div>
                                <input type="hidden" name="pic" value="{$info['pic']|default='/static/common/image/default-cover.svg'}" class="article-cover">
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong >话题别名:</strong>
                            <div class="mt-2">
                                <input class="form-control" type="text" name="url_token" placeholder="话题别名" value="{$info.url_token}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>话题SEO标题:</strong>
                            <div class="mt-2">
                                <input class="form-control" type="text" name="seo_title" placeholder="话题SEO标题" value="{$info.seo_title}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>话题关键词:</strong>
                            <div class="mt-2">
                                <input class="form-control" type="text" name="seo_keywords" placeholder="话题关键词" value="{$info.seo_keywords}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
	</div>
</div>
<script>
    //上传文章封面
    UK.upload.webUpload('filePicker_cover','cover_preview','pic','topic');
</script>
