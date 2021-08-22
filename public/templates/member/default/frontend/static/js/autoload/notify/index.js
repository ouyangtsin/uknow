//用户资料设置侧边栏导航点击
$(document).on('click','.uk-notify-pjax a',function (){
    $(this).addClass('btn-primary text-white').removeClass('btn-outline-primary text-primary');
    $(this).siblings().removeClass('btn-primary text-white').addClass('btn-outline-primary text-primary');
});