<style>
    .uk-focus-dropdown li:hover a{color: #007bff}
</style>
<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-3 col-sm-12 px-xs-0 mb-1">
                {:widget('member/user_nav',['uid'=>input('uid')])}
            </div>
            <div class="col-md-9 col-sm-12 px-0" id="uk-center-main">
                <div class="uk-mod bg-white px-3 pt-3">
                    <div class="uk-mod-head mb-0">
                        <p class="mod-head-title font-weight-bold">{$user['uid']==$user_id ? '我的关注' : 'Ta的关注'}</p>
                        <div class="mod-head-more dropdown show">
                            <a href="javascript:;" class="dropdown-toggle d-none-arrow uk-focus-show" data-toggle="dropdown">
                                <span>关注的问题</span> <i class="icon-select-arrows"></i>
                            </a>
                            <div class="dropdown-menu text-center uk-focus-dropdown">
                                <span class="arrow"></span>
                                <ul>
                                    <li class="active dropdown-item" data-type="question"><a href="javascript:;">关注的问题</a></li>
                                    <li class="dropdown-item" data-type="friend"><a href="javascript:;">关注的人</a></li>
                                    <li class="dropdown-item" data-type="fans"><a href="javascript:;">{$user['uid']==$user_id ? '关注我的' : '关注Ta的'}</a></li>
                                    <li class="dropdown-item" data-type="column"><a href="javascript:;">关注的专栏</a></li>
                                    <li class="dropdown-item" data-type="topic"><a href="javascript:;">关注的话题</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="uk-index-main" class="px-3 pb-2 bg-white"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function (){
        let url = baseUrl + '/member/focus/focus_list/';
        let uid = parseInt("{$user.uid}");
        UK.api.ajaxLoadMore('#uk-index-main',url,{type:'question',uid:uid},null,'json');
        $('.uk-focus-dropdown ul li').click(function ()
        {
            let type =  $(this).data('type');
            $('.uk-focus-dropdown ul li').removeClass('active');
            $(this).addClass('active');
            $('.uk-focus-show').find('span').text($(this).find('a').text());
            $('#uk-index-main').empty();
            UK.api.ajaxLoadMore('#uk-index-main',url,{type:type,uid:uid},null,'json');
        });
    })
</script>
