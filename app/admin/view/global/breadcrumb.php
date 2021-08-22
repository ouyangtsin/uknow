{if isset($page_title) && !empty($page_title)}
<div class="content-header">
    <div class="container-fluid">
        {$page_title|raw|default=''}
    </div>
</div>
<div class="content-header">
    <div class="container-fluid">
        {$page_title|raw|default=''}
    </div>
</div>
{elseif $breadCrumb /}
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1 class="m-0">
                    {$breadCrumb.left.0}
                    <small>{$breadCrumb.left.1}</small>
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{:url('admin/Index/index')}">后台首页</a></li>
                    <li class="breadcrumb-item active"><a href="{:url($breadCrumb.right.url)}">{$breadCrumb.right.title}</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
{/if}