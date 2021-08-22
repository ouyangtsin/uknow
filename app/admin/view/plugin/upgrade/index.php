<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header" style="padding: 0 15px;">
                <h4 style="margin: 0">系统信息</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style="text-align: left">
                            <b>当前版本：</b>UKnowing V{$Think.const.UK_VERSION}
                            {if $check_info.code==200}
                            <span class="badge badge-danger">
                                <i class="glyphicon glyphicon-info-sign"></i> {$check_info.msg}
                            </span>
                            {/if}
                            {if $check_info.code==201}
                            <span class="badge badge-success">
                                <i class="glyphicon glyphicon-info-sign"></i> {$check_info.msg}
                            </span>
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">
                            <b>通信状态：</b>
                            {if $check_info.code}
                            <a href="javascript:;" class="badge badge-success">通信正常</a>
                            {else/}
                            <a href="javascript:;" class="badge badge-danger">通信异常</a>
                            {/if}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">
                            <b>官方账号：</b>
                            未绑定账号 <a href="javascript:;" class="badge badge-warning" >绑定账号</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: left">
                            <b>授权认证：</b>
                            <a href="" target="_blank"><span class="text-color-999"><i class="glyphicon glyphicon-info-sign"></i> 查看版本区别</span></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>