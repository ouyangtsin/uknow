<div class="bg-white p-3">
    <form method="post" action="{:url()}">
        <input type="hidden" name="item_id" value="{$item_id}">
        <input type="hidden" name="item_type" value="{$item_type}">
        <p class="text-muted mb-3 bg-light p-3 font-8">
            未经平台允许，禁止使用帐号的任何功能，发布含有产品售卖信息、牟利性外链及违规推广等信息或引导用户至第三方平台进行交易。请在举报时简述理由，感谢你与我们共同维护社区的良好氛围。点击了解更多社区规范。
        </p>
        <div class="form-group">
            <select class="form-control" name="report_type">
                <option value="">选择理由</option>
                {volist name="$report_category" id="v"}
                <option value="{$v}">{$v}</option>
                {/volist}
            </select>
        </div>
        <div class="form-group">
            <textarea class="form-control" name="reason" rows="3" placeholder="请填写举报理由"></textarea>
        </div>
        <div class="form-group">
            <a class="btn btn-primary save-report-form d-block" href="javascript:;">提交举报</a>
        </div>
    </form>
</div>