<?php
return [
    '基础配置' =>[
        'enable'=>[
            'title' => '启用第三方登录',
            'type' => 'checkbox',
            'value' => 'qq,wechat,weibo',
            'options' =>[
                'qq'     => 'QQ',
                'wechat' => '微信',
                'weibo'  => '微博',
            ],
            'tips' => 'AppSecret',
        ],
        'bind_account'=>[
            'title' => '账号绑定',
            'type' => 'radio',
            'value' => '',
            'options' =>[
                '1' => '开启',
                '0' => '关闭',
            ],
            'tips' => '账号绑定',
        ],
    ],
    'QQ配置' =>[
        'qq_app_id'=>[
            'title' => 'AppID',
            'type' => 'text',
            'value' => '',
            'options' =>[],
            'tips' => 'AppID',
        ],
        'qq_app_secret'=>[
            'title' => 'AppSecret',
            'type' => 'text',
            'value' => '',
            'options' =>[],
            'tips' => 'AppSecret',
        ],
        'qq_scope'=>[
            'title' => '授权模式',
            'type' => 'radio',
            'value' => 'get_user_info',
            'options' =>[
                'get_user_info'=>'get_user_info',
            ],
            'tips' => 'AppSecret',
        ]
    ],
    '微信配置' =>[
        'wechat_app_id'=>[
            'title' => 'AppID',
            'type' => 'text',
            'value' => '',
            'options' =>[],
            'tips' => 'AppID',
        ],
        'wechat_app_secret'=>[
            'title' => 'AppSecret',
            'type' => 'text',
            'value' => '',
            'options' =>[],
            'tips' => 'AppSecret',
        ],
        'wechat_scope'=>[
            'title' => '授权模式',
            'type' => 'radio',
            'value' => 'snsapi_base',
            'options' =>[
                'snsapi_userinfo'=>'snsapi_userinfo',
                'snsapi_base'=>'snsapi_base'
            ],
            'tips' => '授权模式',
        ],
    ],
    '微博配置' =>[
        'weibo_app_id'=>[
            'title' => 'AppID',
            'type' => 'text',
            'value' => '',
            'options' =>[],
            'tips' => 'AppID',
        ],
        'weibo_app_secret'=>[
            'title' => 'AppSecret',
            'type' => 'text',
            'value' => '',
            'options' =>[],
            'tips' => 'AppSecret',
        ],
    ],
];
