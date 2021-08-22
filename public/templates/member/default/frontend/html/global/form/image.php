<div class="uk-cover-box">
    <input type="hidden" name="{$field}" class="uk-upload-field"  {if $value}value="{$value}"{/if}>
    <img src="{$value|default='/static/common/image/default-cover.svg'}" class="mb-2 uk-upload-image rounded" alt="">
    <br><a href="javascript:;" class="button small uk-upload-btn"  data-path="{$field}" data-type="image">上传图片</a>
</div>&nbsp;&nbsp;&nbsp;