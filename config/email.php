<?php
return [
    'TYPE_FOLLOW_ME'=>[
        'user_setting' => 1,
        'title' => lang('关注我的'),
        'subject' => "[#from_username#] 在 [#site_name#] 上关注了你",
        'message' => "[#from_username#] 在 [#site_name#] 上关注了你",
    ],
    'TYPE_QUESTION_INVITE'=>[
        'user_setting' => 1,
        'title' => lang('有人邀请我回答问题'),
        'subject' => "[#from_username#] 在 [#site_name#] 上关注了你",
        'message' => "[#from_username#] 在 [#site_name#] 上关注了你",
    ],
    'TYPE_NEW_ANSWER'=> [
            'user_setting' => 1,
            'title' => lang('我关注的问题有了新回复'),
            'subject' => "[#from_username#] 在 [#site_name#] 上关注了你",
            'message' => "[#from_username#] 在 [#site_name#] 上关注了你",
    ],
    'TYPE_NEW_MESSAGE'=>[
        'user_setting' => 1,
        'title' => lang('有人向我发送私信'),
        'subject' => "[#from_username#] 在 [#site_name#] 上关注了你",
        'message' => "[#from_username#] 在 [#site_name#] 上关注了你",
    ],
    'TYPE_QUESTION_MOD'=>[
        'user_setting' => 1,
        'title' => lang('我的问题被编辑'),
        'subject' => "[#from_username#] 在 [#site_name#] 上关注了你",
        'message' => "[#from_username#] 在 [#site_name#] 上关注了你",
    ],
    'TYPE_VALID_EMAIL'=>[
        'user_setting' => 0,
        'title' => lang('验证邮件'),
        'subject' => "在 [#site_name#] 上的验证邮件",
        'message' => '此邮件为验证邮箱邮件，请点击 <a href="[#link#]">[#link_title#]</a> 进行验证！',
    ],
    'TYPE_EMAIL_TEST'=>[
        'user_setting' => 0,
        'title' => lang('测试邮件'),
        'subject' => "在 [#site_name#] 上的测试邮件",
        'message' => '此邮件为测试邮件',
    ]
];