{if $_ajax}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{:get_setting('site_name')} - 后台管理</title>
    {include file="global/header_meta" /}
</head>
<body>
<div class="iframe uk-overflow-auto" style="max-height: 100vh !important;">
{__CONTENT__}
</div>
{include file="global/footer_meta" /}
</body>
</html>
{else /}
    {include file="global/header" /}
    {include file="global/left" /}
    <div class="content-wrapper">
        {include file="global/breadcrumb" /}
        {__CONTENT__}
    </div>
    {include file="global/footer" /}
{/if}



