<link rel="stylesheet" href="/static/libs/jquery-treetable/jquery.treetable.css">
<div class="uk-padding-small uk-background-default">
    <a href="javascript:;" class="button primary queue-start"  title="开始监听">开始监听</a>
    <a href="javascript:;" class="button primary queue-stop" title="停止监听">停止监听</a>
    <a href="javascript:;" class="button primary " title="队列测试" data-queue="{:url('test')}">队列测试</a>
<div style="padding: 20px" class="queue-message">进程运行状态</div>
</div>
<div class="uk-background-default mt-2">
    <nav class="responsive-tab style-5">
        <ul>
            <li class="uk-active"><a href="{:url('index')}">任务队列</a></li>
        </ul>
    </nav>
    <div class="uk-padding-small">
        <table id="treeTable" class="uk-table uk-table-hover uk-table-responsive uk-table-middle uk-table-divider treetable">
            <thead>
            <tr>
                <th>任务名称</th>
                <th>任务编号</th>
                <th>创建时间</th>
                <th>耗时</th>
                <th>执行时间</th>
                <th>状态</th>
            </tr>
            </thead>
            <tbody>
                {volist name="list" id="v"}
                <tr>
                    <td>{$v.title}</td>
                    <td>{$v.code}</td>
                    <td>{$v.exec_time|date='Y-m-d H:i:s'}</td>
                    <td>
                        {if $v.enter_time>0 and $v.outer_time>0} 耗时 <b class="color-blue">{:sprintf("%.4f",$v.outer_time-$v.enter_time)}</b> 秒 {elseif $v.status eq 2} 执行时间：{$v.enter_time|format_datetime}（ 任务执行中 ）{else}执行时间：<span class="color-desc">任务还没有执行，等待执行...</span>{/if}
                    </td>
                    <td>{$v.create_at}</td>
                    <td>
                    <!--{if $v.status eq 1}-->
                    <a href="javascript:;" class="button primary small"  title="等待处理">等待处理</a>
                    <!--{elseif $v.status eq 2}-->
                    <a href="javascript:;" class="button primary small"  title="正在处理">正在处理</a>
                    <!--{elseif $v.status eq 3}-->
                      <a href="javascript:;" class="button success small"  title="处理完成">处理完成</a>
                    <!--{elseif $v.status eq 4 }-->
                    <a href="javascript:;" class="button small danger ajax-open" data-del="1" data-confirm="确定要删除吗？" data-url="{:url('delete?id='.$v['id'])}">处理失败</a>
                    <a class="button small layui-bg-green" title="重新执行" data-confirm="确定要重置该任务吗？" data-queue="{:url('redo')}?code={$v.code}">
                        <i class="layui-icon font-s12">&#xe669;</i>
                    </a>
                    <!--{/if}-->

                    </td>

                </tr>
                {/volist}
            </tbody>
        </table>
    </div>
</div>