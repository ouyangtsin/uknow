<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UK
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace plugins\third;
use think\Plugins;

/**
 * 第三方登录
 */
class Plugin extends Plugins
{
    public $info = [
        'name' => 'third',    // 插件标识唯一
        'title' => '第三方登录',    // 插件名称
        'description' => '第三方登录',    // 插件简介
        'status' => 0,    // 状态
        'install'     => 0,
        'author' => 'UKnowing',
        'version' => '1.0',
    ];

    public $menu = [
        'is_nav' => 0,//1导航栏；0 非导航栏
        'menu' =>[
            'name' => 'third',
            'title' => '第三方登录',
            'status' => 1,
            'icon' => 'fas fa-comments-dollar',
            'menu_list' => [
                [
                    'name' => 'third/third/index',
                    'title' => '登录记录',
                    'status' => 1,
                    'icon' => 'fas fa-users',
                    'menu_list' =>
                        [
                            ['name' => 'third/third/delete', 'title' => '操作-删除', 'status' => 0],
                        ]
                ]
            ]
        ]
    ];

	/**
	 * 插件安装方法
	 * @return bool
	 */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
}
