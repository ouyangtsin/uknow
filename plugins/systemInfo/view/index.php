<div class="row">
    <!--快捷方式-->
    <div class="col-12 col-sm-6">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title"><i class="fa fa-gift"></i> 快捷方式</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body" style="display: block;">
                <a class="btn btn-app" href="{:url('system.Config/config')}"><i class="fa fa-cogs"></i>系统设置</a>
                <a class="btn btn-app" href="{:url('module.Database/database')}"><i class="fa fa-database"></i>数据备份</a>
                <a class="btn btn-app" href="{:url('system.Module/index')}"><i class="fa fa-th-list"></i>模块管理</a>
                <a class="btn btn-app" href="{:url('content.Category/index')}"><i class="fa fa-th"></i>栏目管理</a>
                <a class="btn btn-app" href="{:url('module.Link/index')}"><i class="fa fa-link"></i>友情链接</a>
                <a class="btn btn-app" href="{:url('module.Ad/index')}"><i class="fa fa-ad"></i>广告管理</a>
                <a class="btn btn-app" href="{:url('plugin.Plugins/index')}"><i class="fas fa-cloud-upload-alt"></i>插件管理</a>
                <a class="btn btn-app" href="{:url('module.Theme/index')}"><i class="fa fa-code"></i>模板管理</a>
            </div>
        </div>
    </div>
    <!--数据统计-->
    <div class="col-12 col-sm-6">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> 数据统计</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: block;">
                <div class="row pt-1 pb-1">
                    <div class="col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{$notify_count}</h3>
                                <p>待处理</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-android-clipboard"></i>
                            </div>
                            <a href="{$messageCatUrl}" class="small-box-footer">更多信息 <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>{$user}</h3>
                                <p>一周用户注册</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="{:url('member.Users/index')}" class="small-box-footer">更多信息 <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!--系统信息-->
    <div class="col-12 col-sm-6">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title"><i class="fa fa-cog"></i> 系统信息</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: block;">
                <table class="table table-striped">
                    <tr>
                        <td>网站域名</td>
                        <td>{$system.url}</td>
                    </tr>
                    <tr>
                        <td>网站目录</td>
                        <td>{$system.document_root}</td>
                    </tr>
                    <tr>
                        <td>服务器操作系统</td>
                        <td>{$system.server_os}</td>
                    </tr>
                    <tr>
                        <td>服务器端口</td>
                        <td>{$system.server_port}</td>
                    </tr>
                    <tr>
                        <td>服务器IP</td>
                        <td>{$system.server_ip}</td>
                    </tr>
                    <tr>
                        <td>WEB运行环境</td>
                        <td>{$system.server_soft}</td>
                    </tr>
                    <tr>
                        <td>MySQL数据库版本</td>
                        <td>{$system.mysql_version}</td>
                    </tr>
                    <tr>
                        <td>运行PHP版本</td>
                        <td>{$system.php_version}</td>
                    </tr>
                    <tr>
                        <td>最大上传限制</td>
                        <td>{$system.max_upload_size}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title"><i class="fas fa-tag"></i> 版本信息</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: block;">
                <table class="table table-bordered">
                    <tr>
                        <td class="text-center">当前版本</td>
                        <td>UKnowing {$system.uk_version}<span id="checkVersion"></span></td>
                    </tr>
                    <tr>
                        <td class="text-center">基于框架</td>
                        <td>ThinkPHP {$system.version} + AdminLTE</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>