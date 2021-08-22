<div class="uk-main-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="uk-main col-md-9 col-sm-12 px-0 mb-1">
                <div class="uk-mod bg-white px-3 pt-3">
                    <div class="uk-mod-head mb-0">
                        <p class="mod-head-title font-weight-bold">搜索</p>
                        <div class="uk-popover mod-head-more">
                            <a href="javascript:;" class="popover-title uk-type-show">
                                <span>全部</span> <i class="icon-select-arrows"></i>
                            </a>
                            <div class="popover-content">
                                <div class="text-center d-block py-2 uk-type-dropdown" style="min-width: 100px">
                                    <ul class="uk-type-tab">
                                        <li data-type="all" class="dropdown-item active"><a href="javascript:;"> 全部 </a></li>
                                        <li data-type="question" class="dropdown-item"><a href="javascript:;"> 问题 </a></li>
                                        <li data-type="article" class="dropdown-item"><a href="javascript:;"> 文章 </a></li>
                                        <li data-type="users" class="dropdown-item"><a href="javascript:;"> 用户 </a></li>
                                        <li data-type="topic" class="dropdown-item"><a href="javascript:;"> 话题 </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="p-3 bg-white">
                    <div class="search-detail-info mb-2 clearfix">
                        <div class="clearfix mb-2">
                            <div class="search-discuss-info">
                                "<em style="font-style: normal;color: red">{:urldecode($keywords)}</em>" 搜索结果
                            </div>

                            <div class="uk-popover mt-0 px-2 float-right ml-3">
                                <a href="javascript:;" class="popover-title uk-time-show">
                                    <span>最近一年</span> <i class="icon-select-arrows"></i>
                                </a>
                                <div class="popover-content">
                                    <div class="text-center d-block py-2 uk-time-dropdown" style="min-width: 100px">
                                        <ul class="uk-time-tab">
                                            <li class="dropdown-item" data-time="7"><a href="JavaScript:;">最近一周</a> </li>
                                            <li data-time="30" class="dropdown-item"><a href="JavaScript:;">最近一月 </a></li>
                                            <li data-time="365" class="active dropdown-item"><a href="JavaScript:;">最近一年 </a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-sort-tab uk-sort-tab float-left">
                            <ul>
                                <li data-sort="all" class="active"><a href="javascript:;">综合</a></li><li data-sort="new"><a href="javascript:;">最新</a></li><li data-sort="hot"><a href="javascript:;">最热</a></li>
                            </ul>
                        </div>
                    </div>
					<div class="search-detail-list" id="uk-search-result"></div>
				</div>
			</div>

            <div class="uk-right col-md-3 col-sm-12 px-xs-0">
                <!--热搜-->
                <div class="uk-mod bg-white sidebar-hot-topic p-3 mb-1">
                    <div class="uk-mod-head">
                        <p class="mod-head-title">热门搜索</p>
                        <a href="{:url('topic/index')}" class="mod-head-more" target="_blank">More >></a>
                    </div>
                    <div class="sidebar-hot-search">

                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
<script>
    let keywords = '{:urldecode($keywords)}';
    $(function (){
        let type = 'all';
        let sort = 'all';
        let time = '365';
        let url = baseUrl + '/search/search_result';

        //搜索类型点击
        $(document).on('click', '.uk-type-tab  li a', function()
        {
            $('.uk-type-tab li').removeClass('active');
            $(this).parent('li').addClass('active');
            type = $(this).parent('li').attr('data-type');
            sort = $('.uk-sort-tab').find('li.active').attr('data-sort');
            time = $('.uk-time-tab').find('li.active').attr('data-time');
            $('.uk-type-show span').text($(this).parent('li').find('a').text());
            renderHtml();
        });

        //搜索排序点击
        $(document).on('click', '.uk-sort-tab li a', function()
        {
            $('.uk-sort-tab li').removeClass('active');
            $(this).parent('li').addClass('active');
            sort = $(this).parent('li').attr('data-sort');
            type = $('.uk-type-tab').find('li.active').attr('data-type');
            time = $('.uk-time-tab').find('li.active').attr('data-time');
            renderHtml();
        });

        //时间排序
        $(document).on('click', '.uk-time-tab li a', function()
        {
            $('.uk-time-tab li').removeClass('active');
            $(this).parent('li').addClass('active');
            time = $(this).parent('li').attr('data-time');
            type = $('.uk-type-tab').find('li.active').attr('data-type');
            sort = $('.uk-sort-tab').find('li.active').attr('data-sort');
            $('.uk-time-show span').text($(this).text());
            renderHtml();
        });

        //渲染输出页面
        function renderHtml()
        {
            $('#uk-search-result').empty();
            UK.api.ajaxLoadMore('#uk-search-result',url,{
                sort:sort,
                type:type,
                time:time,
                keywords:keywords
            },function (res,page,next)
            {
                var totalPage = res.data.page;
                totalPage = totalPage ? totalPage : 0;
                next(res.data.html, page < totalPage);
            });
            layui.element.init();
        }

        $(".uk-type-tab").find('li').eq(0).find('a').click();
    });
</script>
