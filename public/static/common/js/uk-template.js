let ukTemplate = {
    //选择话题，编辑话题弹窗
    topicBox:
        '<div class="uk-toast-header">' +
        '   <h4> 编辑话题</h4>' +
        '   <span class="uk-toast-close" onclick="$(\'#ajax-box\').hide();" uk-tooltip="title:关闭; pos: left "></span>' +
        '</div>'+
        '<div class="uk-topic-toast">' +
        '   <form class="uk-search uk-search-default">'+
        '       <span class="uk-search-icon-flip" uk-search-icon></span>'+
        '       <input class="uk-search-input" type="search" placeholder="搜索话题">'+
        '   </form>'+
        '   <div class="uk-toast-topic-select">' +
        '       ' +
        '   </div> '+
        '</div>',

    //快捷发起弹窗
    publishBox:'',

    //收藏弹窗
    favoriteBox:''+
        '<div class="uk-toast-header">' +
        '   <h4> 添加收藏</h4>' +
        '   <span class="uk-toast-close" onclick="$(\'#ajax-box\').hide();" uk-tooltip="title:关闭; pos: left "></span>' +
        '</div>'+
        '<form id="favorite_form" action="' + '/member/favorite/ajax/update_favorite_tag/" method="post" onsubmit="return false;" style="min-width: 300px">'+
        '   <input type="hidden" name="item_id" value="" />'+
        '   <input type="hidden" name="item_type" value="" />'+
        '   <input type="hidden" name="tags" id="add_favorite_tags" class="uk-input" />'+
        '   <div class="favorite-tag-list uk-overflow-auto" style="max-height: 50vh">'+
        '       <div class="favorite-body"><ul></ul></div>'+
        '       <a href="javascript:;"class="uk-button uk-button-primary uk-display-block uk-margin-auto mt-3">创建收藏夹</a>'+
        '   </div>'+
        '   <div class="favorite-tag-add mt-3">'+
        '       <input type="text" class="uk-input" placeholder="标签名字" />'+
        '       <input type="checkbox" class="uk-checkbox uk-margin"  /> 公开'+
        '       <div class="mt-3">'+
        '           <a class="button">取消</a>'+
        '           <a href="javascript:;" class="button">确认创建</a>'+
        '       </div>'+
        '   </div>'+
        '</form>',

    //举报弹窗
    'reportBox':
        '<div class="uk-toast-header">' +
        '   <h4> 举报 </h4>' +
        '   <span class="uk-toast-close" onclick="$(\'#ajax-box\').hide();" uk-tooltip="title:关闭; pos: left "></span>' +
        '</div>'+
        '<form  method="post" action="/ask/ajax/save_report" style="width: 350px" id="repoet-select">'+
        '<p class="uk-text-meta mb-3 uk-background-muted uk-padding-small">未经平台允许，禁止使用帐号的任何功能，发布含有产品售卖信息、牟利性外链及违规推广等信息或引导用户至第三方平台进行交易。请在举报时简述理由，感谢你与我们共同维护社区的良好氛围。点击了解更多社区规范。</p>'+
        '   <input type="hidden" name="type" value="" />'+
        '   <input type="hidden" name="item_id" value="" />'+
        '   <select class="uk-select ">' +
        '       <option>'+_t('选择理由')+'</option>' +
        '{{each list as value}}  '+
        '<option>{{value}}</option>' +
        '{{/each}}  '+
        '   </select>'+
        '   <div class="uk-margin">' +
        '       <textarea class="uk-textarea" name="detail" rows="3" placeholder="'+_t('请填写举报理由')+'"></textarea>' +
        '   </div>'+
        '   <a class="uk-button-primary uk-button ajax-form uk-display-block" href="javascript:;"><button class="uk-button-primary">'+ _t('提交举报')+'</button></a>'+
        '</form>',

    //邀请弹窗
    'inviteBox':
        '<div class="uk-toast-header">' +
        '   <h4>'+ _t('邀请回答')+' </h4>' +
        '   <span class="uk-toast-close" onclick="$(\'#ajax-box\').hide();" uk-tooltip="title:关闭; pos: left "></span>' +
        '</div>'+
        '<form  method="post" action="" style="min-width: 450px">'+
        '   <div class="uk-search uk-search-default" style="width: 100%">' +
        '       <span class="uk-search-icon-flip" uk-search-icon></span>'+
        '       <input class="uk-search-input" type="search" placeholder="搜索用户" id="invite-users">'+
        '   </div><div class="search-invite-list">'+
        '{{each list as value}}  '+
        '   <div class="invite-recommend-user uk-overflow-auto" style="max-height: 50vh">' +
        '       <div class="invite-user-item uk-grid uk-padding-small">' +
        '           <div class="uk-width-auto">' +
        '               <img src="/static/libs/uk-ask/images/avatars/avatar-1.jpg" alt="" style="border-radius:50%;width: 50px;height: 50px;">' +
        '           </div>' +
        '           <div class="uk-width-expand">' +
        '               <h4> 首颗 </h4>' +
        '               <p class="uk-text-lighter"> 最近回答过该领域问题 </p>' +
        '           </div>' +
        '           <div class="uk-width-auto">' +
        '               <a href="" class="button">邀请回答</a>' +
        '           </div>' +
        '       </div>' +
        '   </div> '+
        '{{/each}}  '+
        '   </div><div id="invite-page"> '+
        '   </div> '+
        '</form>',

    userCard:'' +
        '<div id="uk-card-tips" class="uk-card-tips uk-card-tips-user">' +
        '   <div class="contact-list-box">' +
        '       <div class="contact-list-box-media">' +
        '           <a href="{{url}}"><img src="{{avatar}}" alt=""></a>' +
        '           <span class="online-dot"></span>' +
        '      </div>' +
        '      <h4><a href="{{url}}">{{user_name}}</a></h4>' +
        '      <p> <i class="icon-users"></i> 与 <strong> 斯特拉·约翰逊 </strong> 有 <strong> 14个</strong> 共同好友</p>' +
        '      <div class="contact-list-box-btns">' +
        '          <a href="#" class="button primary block mr-2"><i class="uil-envelope mr-1"></i> 发送私信</a>' +
        '          <a href="#" class="button secondary button-icon mr-2"><i class="uil-list-ul"> </i> </a>' +
        '          <a href="#" class="button secondary button-icon"> <i class="uil-ellipsis-h"></i> </a>' +
        '      </div>' +
        '  </div>' +
        '</div>',
};
window.ukTemplate = ukTemplate;