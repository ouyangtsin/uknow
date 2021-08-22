        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>
    <button id="totop" title="返回顶部" style="display: none;"><i class="fa fa-chevron-up"></i></button>
    {include file="global/footer_meta" /}
    <script>
        // 清空缓存
        $(".js_clear_cash").click(function () {
            var url = "{:url('admin/Index/clear')}";
            layer.confirm('确定要清除缓存吗？', {
                icon: 3,
                title: "系统提示",
                btn: ['确认', '取消']
            }, function (index) {
                layer.close(index);
                $.post(url, {
                    del: true
                }, function (result) {
                    if (!result.error) {
                        layer.msg(result.msg,{},function (index) {
                            layer.close(index);
                            //$.pjax.reload('.content-wrapper');
                        });
                    } else {
                        layer.msg(result.msg);
                    }
                });
            });
        })
    </script>
</body>
</html>