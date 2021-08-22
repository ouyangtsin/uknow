<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="bg-white">
                    <div class="uk-nav-container py-2 px-3">
                        <ul class="uk-pjax-tab">
                            <li class="{$type=='log'?'active':''} mr-3"><a data-pjax="uk-index-main" href="{:url('member/score/index',['type'=>'log'])}">积分记录</a></li>
                            <!--<li class="{$type=='exchange'?'active':''}"><a data-pjax="uk-index-main" href="{:url('member/score/index',['type'=>'exchange'])}">积分兑换</a></li>-->
                        </ul>
                    </div>
                    <div id="uk-index-main" class="p-3">
                        {if $list}
                        <table class="table table-divider">
                            <thead>
                            <tr>
                                <!--<th class="row-selected row-selected">
                                    <label><input class="check-all" type="checkbox"/></label>
                                </th>-->
                                <th>积分描述</th>
                                <th>积分变化</th>
                                <th>当前余额</th>
                                <th>变动时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="v"}
                            <tr>
                                <!--<td>
                                    <label>
                                        <input class="ids" type="checkbox" name="id[]" value="{$v.id}" />
                                    </label>
                                </td>-->
                                <td class="text-muted">{$v.remark}</td>
                                <td>{$v.score}</td>
                                <td>{$v.balance}</td>
                                <td>{:date('Y-m-d H:i:s',$v['create_time'])}</td>
                            </tr>
                            {/volist}
                            </tbody>
                        </table>
                        {$page|raw}
                        {else/}
                        <p class="text-center mt-4 text-meta">
                            <img src="/static/common/image/empty.svg" alt="暂无记录">
                            <span class="mt-3 d-block ">暂无积分记录</span>
                        </p>
                        {/if}
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>