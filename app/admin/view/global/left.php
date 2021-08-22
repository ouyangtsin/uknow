    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-default elevation-4">
        <!-- Brand Logo -->
        <a href="{:url('Index/index')}" class="brand-link">
            <img src="{:get_setting('site_logo','/static/common/image/logo.png')}" class="brand-image">
        </a>
        <!--<div class="dropdown-user-details">
            <div class="dropdown-user-avatar">
                <img src="{$user_info.avatar}" alt="" style="width: 50px;height: 50px">
            </div>
            <div class="dropdown-user-name">{$user_info.name}</div>
            <span class="badge badge-light">{$user_info.group_name}</span>
            <div class="mt-2">
                <a href="{:url('index/logout')}" class="badge badge-danger">退出登录</a>
            </div>
        </div>-->
        <div class="sidebar" <!--style="margin-top: 0"-->>
            <nav class="mt-2 mb-2">
                <ul class="sidebar-menu nav nav-pills no_radius nav-sidebar flex-column nav-child-indent js_left_menu_show" data-widget="treeview" role="menu" data-accordion="true">
                    {$_menu|raw}
                </ul>
            </nav>
        </div>
    </aside>