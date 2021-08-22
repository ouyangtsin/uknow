<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 bg-white px-0 rounded">
                <div class="p-3">
					<form  id="question_form" novalidate="novalidate"  method="post" action="{:url('question/publish')}">
                        {:token_field()}
                        <input type="hidden" name="id" value="{$question_info['id']|default=0}">
						<div class="form-group d-flex">
                            <div class="flex-fill">
                                <input id="ctitle" name="title" value="{$question_info.title|default=''}" class="form-control" type="text" placeholder="问题标题">
                            </div>
                            {if !empty($category_list) && $setting.enable_category}
                            <div class="flex-fill ml-2">
                                <select class="form-control" name="category_id" title="请选择一项分类" required>
                                    <option value="0">选择分类</option>
                                    {volist name="category_list" id="v"}
                                    <option value="{$v.id}" {if isset($question_info['category_id']) && $question_info['category_id']==$v['id']}selected {/if}>{$v.title}</option>
                                    {/volist}
                                </select>
                            </div>
                            {/if}
                        </div>
                        {if !isset($question_info['id'])}
                        <div class="form-group mb-3">
                            <div class="page-detail-topic">
                                <ul class="d-inline p-0" id="uk-topic-list">
                                    {if !empty($question_info['topics'])}
                                    {volist name="question_info['topics']" id="v"}
                                    <li class="d-inline uk-tag"><a href="{:url('ask/topic/detail',['id'=>$v['id']])}">{$v.title}</a></li>
                                    {/volist}
                                    <input type="hidden" name="topics" value="{:implode(',',array_column($question_info['topics'],'id'))}">
                                    {/if}
                                </ul>
                                <a href="javascript:;" class="text-primary font-9 uk-ajax-open d-inline" data-url="{:url('ask/ajax/topic',['item_type'=>'question','item_id'=>isset($question_info['id']) ? $question_info['id'] : 0])}" data-title="编辑话题"><i class="icon-add"></i> 添加话题</a>&nbsp;&nbsp;<span class="font-9 text-muted">(最多输入5个)</span>
                            </div>
                        </div>
                        {/if}
						<div class="form-group mb-3">
                            {:hook('editor',['name'=>'detail','cat'=>'question','value'=>isset($question_info['detail']) ? $question_info['detail'] : ''])}
						</div>
                        {if !isset($question_info['id'])}
						<div class="form-group mb-3">
                            <label>
                                <input value="1" name="is_anonymous" type="checkbox"  {$question_info.is_anonymous ? 'checked' : ''}> 匿名提问
                            </label>
						</div>
                        {/if}
                        {if $setting.reward_question_enable}
                        <div class="form-group">
                            <label>问题类型：</label>
                            <label class="mr-3">
                                <input value="normal" name="question_type" type="radio" checked> 普通问题
                            </label>
                            <label>
                                <input value="reward" name="question_type" type="radio"> 悬赏问题
                            </label>
                        </div>
                        <div class="form-group reward-question" style="display: none !important;">
                            <div class="d-flex">
                                <div class="flex-fill">
                                    <label> 悬赏金额： </label>
                                    <label><input type="number" value="1" name="reward_money" class="form-control"></label>
                                </div>

                                <div class="flex-fill">
                                    <label> 截止日期： </label>
                                    <label><input type="text" id="rewardDay" value="{:date('Y-m-d',time()+7*24*60*60)}" name="reward_day" class="form-control"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group reward-question" style="display: none !important;">
                            <label>付费围观：</label>
                            <label class="mr-3">
                                <input value="1" name="look_enable" type="radio" checked> 是
                            </label>
                            <label class="mr-3">
                                <input value="0" name="look_enable" type="radio"> 否
                            </label>
                            (<label class="font-9 text-muted">他人围观需付费 1 元</label>)
                        </div>
                        {/if}

                        <!--问题发布附加钩子-->
                        {:hook('publish_question_extend',$question_info)}
                        <!--问题发布附加钩子-->

						<div class="mt-6">
							<button type="button" class="btn btn-primary px-3 btn-sm uk-question-form mr-3">发表问题</button>
                            <button type="button" onclick="UK.User.draft(this,'question','{$question_info.id|default=0}')" class="btn btn-outline-primary px-3 btn-sm uk-save-draft">存草稿</button>
						</div>
					</form>
				</div>
			</div>

            <div class="uk-right col-md-3 col-sm-12">
                <div class="uk-mod bg-white p-3">
                    <div class="uk-mod-head">
                        <p class="mod-head-title font-12 font-weight-bold">问题规则</p>
                    </div>
                    <div class="sidebar-topic-list">
                        <dl class="text-muted font-9">
                            <dt>问题标题：</dt>
                            <dd>请用准确的语言描述您发布的问题思想</dd>
                        </dl>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
{if $setting.reward_question_enable}
<script type="application/javascript">
    $(document).ready(function() {
        $('input[type=radio][name=question_type]').change(function() {
            if ($(this).val()=='reward') {
                $('.reward-question').show();
                layui.laydate.render({
                    elem: '#rewardDay' //指定元素
                });
            }else{
                $('.reward-question').hide();
            }
        });
    });
</script>
{/if}