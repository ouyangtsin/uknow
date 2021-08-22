<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0">
				<div class="bg-white p-3">
					<form method="post" action="{:url('column/apply')}">
                        <input type="hidden" name="id" value="{$info.id|default=0}">
						{:token_field()}
						<div class="form-group">
							专栏封面:&nbsp;&nbsp;&nbsp;&nbsp;
							<div class="uk-width-1-3 mt-3">
                                <div class="column-cover-box">
                                    <div id="fileList_cover" class="uploader-list"></div>
                                    <div id="filePicker_cover">
                                        <a href="/static/common/image/default-cover.svg" target="_blank">
                                            <img class="image_preview_info rounded" src="{$info.cover|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                        </a>
                                    </div>
                                    <input type="hidden" name="cover" value="{$info.cover|default='/static/common/image/default-cover.svg'}" class="article-cover">
                                </div>
							</div>&nbsp;&nbsp;&nbsp;
						</div>

						<div class="form-group">
                            <input class="form-control" type="text" value="{$info.name|default=''}" name="name" placeholder="专栏名称">
                        </div>

						<div class="form-group">
							<textarea class="form-control" name="description" id="content_text" rows="5" placeholder="专栏简介">{$info.description|default=''}</textarea>
						</div>

						<div class="form-group">
							<button type="button" class="btn btn-primary uk-ajax-form px-4">提交</button>
						</div>
					</form>
				</div>
			</div>

            <div class="uk-right col-md-3 col-sm-12">
				<div class="uk-card uk-card-default uk-card-small uk-card-body">
					<h4 class="uk-text-bold">内容检测</h4>
				</div>
			</div>
		</div>
	</div>
</div>