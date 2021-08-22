<style>
    .pay-type .flex-fill{border: #eee 1px solid}
    .pay-type .flex-fill.active{border: #0062cc 1px solid}
</style>
<form method="post">
    <p class="text-center p-2 bg-light rounded">支付金额：<span class="text-danger font-12">{$amount}</span> 元</p>
    <div class="p-3" style="min-width: 400px">
        <div class="form-group pay-type">
            <div class="d-flex mt-2 text-center">
                <dl class="flex-fill mr-1 p-2 rounded active check-pay" data-type="wechat">
                    <dt><img src="/static/common/image/wepay.svg" height="50"></dt>
                    <dd>微信支付</dd>
                </dl>
                <dl class="flex-fill mr-1 p-2 rounded check-pay" data-type="alipay">
                    <dt><img src="/static/common/image/alipay.svg" height="50"></dt>
                    <dd>支付宝支付</dd>
                </dl>
                {if $amount<=$user_info['money']}
                <dl class="flex-fill mr-1 p-2 rounded check-pay" data-type="balance" data-money="{$user_info.money}">
                    <dt><img src="/static/common/image/yepay.svg" height="50"></dt>
                    <dd>余额支付</dd>
                </dl>
                {/if}
            </div>
        </div>
    </div>
    <div class="pay-footer p-2 overflow-hidden bg-light rounded">
        <span class="float-left balance-money d-block" style="height: 38px;line-height: 38px;"></span>
        <a href="javascript:;" class="btn btn-primary float-right uk-pay-form">扫码支付</a>
    </div>
</form>
<script>
    $('.check-pay').each(function(){
        let that = $(this);
        that.click(function (){
            $('.check-pay').removeClass('active');
            that.addClass('active');
            if(that.data('type')=='balance')
            {
                $('.uk-pay-form').text('余额支付');
                $('.balance-money').text('您当前可用余额为： {$user_info.money} 元');
            }else{
                $('.uk-pay-form').text('扫码支付');
                $('.balance-money').text('');
            }
        })
    })
    $('.uk-pay-form').click(function (){
        let payType = $('dl.active').data('type');
        if(payType=='balance')
        {
            if(parseInt("{$user_info.money}")<parseInt("{$amount}"))
            {
                layer.msg('您的余额不足，无法发起支付！');
                return false;
            }

            layer.prompt({title: '请输入您的交易密码', formType: 1}, function(pass, index){
                $.ajax({
                    url: baseUrl+'/pay/balance_pay',
                    dataType: 'json',
                    type: 'post',
                    data:{password:pass,order_id:parseInt("{$id}")},
                    success: function (result) {
                        if(result.code===1)
                        {

                        }
                        layer.msg(result.msg)
                    }
                })
            });
        }else{
            $.ajax({
                url: baseUrl+'/pay/pay_img?pay_type='+payType+'&order_id='+parseInt("{$id}"),
                dataType: 'json',
                type: 'get',
                success: function (result) {
                    if(result.code===1)
                    {
                        //window.parent.layer.closeAll();
                        layer.open({
                            type: 1,
                            title:'使用'+result.data.text+'扫一扫支付',
                            closeBtn: 1,
                            area: ['auto'],
                            shadeClose: true,
                            content: result.data.html
                        });
                    }else{
                        layer.msg(result.msg)
                    }
                }
            })
        }
    })
</script>
