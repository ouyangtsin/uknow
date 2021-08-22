{if $_ajax }
{__CONTENT__}
{/if}
{if $_ajax_open}
{include file="global/head" /}
<body>
<div class="uk-overflow-auto" style="max-height: 100vh !important;">
    {__CONTENT__}
    {include file="global/footer_meta" /}
</div>
</body>
</html>
{else /}
{include file="global/head" /}
<body class="uk-white-theme">
    {include file="global/header" /}
    <div class="uk-wrap" id="uk-wrap">
        {__CONTENT__}
    </div>
    {include file="global/footer" /}
    {include file="global/footer_meta" /}
    {$theme_config['footer_js']|raw|htmlspecialchars_decode}
</body>
</html>
{/if}