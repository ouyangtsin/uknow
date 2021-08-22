<div class="bg-white p-3">
    <div class="author border-bottom">
        <dl class="overflow-hidden">
            <dt class="float-left"><img src="{$user_info['avatar']|default='/static/common/image/default-avatar.svg'}" width="38" height="38"></dt>
            <dd class="float-right" style="width: calc(100% - 50px)">
                <strong>{$user_info.name}</strong>
                <p class="font-8 text-muted uk-one-line">积分 {$user_info.score} &nbsp;&nbsp;提问 {$user_info.question_count} &nbsp;&nbsp;回答 {$user_info.answer_count}</p>
            </dd>
        </dl>
    </div>
    <form method="post" action="{:url('question/save_answer')}">
        {:token_field()}
        <input type="hidden" name="question_id" value="{$question_id}">
        <input type="hidden" name="id" value="{$answer_id}">
        <div class="form-group">
            {if isset($answer_info)}
            {:hook('editor',['name'=>'content','cat'=>'answer','value'=>$answer_info['content']])}
            {else/}
            {:hook('editor',['name'=>'content','cat'=>'answer','value'=>''])}
            {/if}
        </div>
        <div class="form-group">
            {if !$answer_id}
            <div class="mr-3 dropdown show font9 d-inline-block">
                <a href="javascript:;" class="dropdown-toggle d-none-arrow" style="color: #76839b" data-toggle="dropdown" ><i class="icon-settings"></i> 设置</a>
                <div class="dropdown-menu px-2 font-8" style="z-index: 1000;">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="reprint" checked value="1" class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px">允许转载</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio"  name="reprint" value="2" class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px">付费转载</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="reprint" value="0" class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px">禁止转载</label>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="comment" value="1" checked class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px" >允许评论</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio"  name="comment" value="2" class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px">评论由我筛选</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" name="comment" value="3" class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px" >我关注的人评论</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio"  name="comment" value="0" class="custom-control-input">
                        <label class="custom-control-label" style="line-height: 24px" >关闭评论</label>
                    </div>
                </div>
            </div>
            <label class="font-9" style="color: #76839b">
                <input class="uk-checkbox" type="checkbox" value="1" name="is_anonymous"> 匿名
            </label>
            {/if}
            <button type="button" class="btn btn-primary btn-sm float-right uk-answer-submit px-3">提交回答</button>
        </div>
    </form>
</div>