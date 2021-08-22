	<div class="picker-box">
		<div id="picker_{$field}" class="picker_button">上传多图</div>
		<input type="hidden" name="{$field}" id="field_{$field}" {if condition="$value neq '0'"}value="{$value}"{/if}>
		<div id="fileList_{$field}" class="upload-file-list-info" style="width:280px;">
			{if condition="$value"}
			{php}
			$img_list = explode(',',$value);
			{/php}
			{volist name="img_list" id="item"}
			{php}
			$images = get_cover($item);
			{/php}
			<li class="affix-list-item" id="WU_FILE_{$key}">
				<div class="upload-file-info">
					<span class="webuploader-pick-file-close" data-queued-id="WU_FILE_{$key}" data-id="{$item}" data-fileurl="{$images['path']}"><i class="close"></i></span>
					<span class="fname"></span>
					<span class="fsize">上传时间:{$images['create_time']|date='Y-m-d H:i:s',###}</span>
					<div class="clearfix"></div>
				</div>
				<div class="filebox image">
					<img src="{:config('base_url')}{$images['path']}" class="img-responsive">
				</div>
			</li>
			{/volist}
			{/if}
		</div>
	</div>
	<script type="text/javascript">
		uploadsize =  2;
		$(function(){
			$("#picker_{$field}").SentUploader({
					compress:false,
					uploadEvents: {
						uploadComplete:function(file){}
					},
					listName : 'fileList_{$field}',
					hiddenName: 'field_{$field}',
					hiddenValType:1,
					fileSingleSizeLimit:uploadsize*1024*1024,
					closeX:true
				},
				{
					fileType: 'service',
					filename : 'images',
				});
		});
	</script>