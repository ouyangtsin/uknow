<div class="uk-cover-box">
    <div id="{$field}_fileList_cover" class="uploader-list"></div>
    <div id="{$field}_filePicker_cover">
        <a href="{$article_info['cover']|default='/static/common/image/default-cover.svg'}" target="_blank">
            <img class="image_preview_info" src="{$value|default='/static/common/image/default-cover.svg'}" id="{$field}_cover_preview" width="100" height="100">
        </a>
    </div>
    <input type="hidden" name="{$field}" value="{$value|default='/static/common/image/default-cover.svg'}" class="article-cover">
</div>&nbsp;&nbsp;&nbsp;
<script>
    UK.upload.webUpload('{$field}_filePicker_cover','{$field}_cover_preview','{$field}','common');
</script>