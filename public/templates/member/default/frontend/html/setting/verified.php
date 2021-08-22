<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="uk-main col-md-9 col-sm-12 px-0" id="uk-center-main">
                {include file="setting/nav"}
                <div class="bg-white mt-1 py-4 px-3">
                    <form method="post" action="{:url()}">
                        {if isset($info['status'])}
                        <div class="form-group">
                            <label class="uk-form-label">认证状态</label>
                            <div class="uk-form-controls">
                                {if $info['status']==0}
                                <span class="badge badge-danger">正在审核中</span>
                                {/if}

                                {if $info['status']==1}
                                <span class="badge badge-success">审核通过</span>
                                {/if}

                                {if $info['status']==2}
                                <span class="badge badge-info">拒绝山河</span>
                                {/if}
                            </div>
                        </div>
                        {/if}
                        <div class="form-group">
                            <label class="uk-form-label">认证类型</label>
                            <div class="uk-form-controls">
                                {volist name="verify_type" id="v"}
                                <label>
                                    <input value="{$key}" type="radio"  name="type" {if $user_info.verified==$key || $key=='people'} checked {/if}> {$v}
                                </label>
                                {/volist}
                            </div>
                        </div>
                        <div id="field"></div>
                        <div class="form-group">
                            <input type="hidden" name="id" value="{$info['id']|default=0}">
                            <button class="btn btn-primary btn-sm uk-ajax-form px-4" type="button" {if !isset($info['status']) || $info['status']!=2} disabled {/if}>确 定</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let defaultType = "{:isset($info['type']) ? $info['type'] : 'people'}";
    $(document).ready(function() {
        $('input[type=radio][name=type]').change(function() {
            if (this.value) {
                renderHtml(this.value)
            }
        });
    });
    renderHtml(defaultType);
    function renderHtml(type)
    {
        $.ajax({
            type: 'GET',
            url: baseUrl+'/ask/ajax/verify_type?type='+type+'&_ajax=1',
            dataType: '',
            success: function (res) {
                $('#field').html(res)
            }
        });
    }
</script>