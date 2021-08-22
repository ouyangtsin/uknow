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

namespace plugins\editor;
use think\facade\Request;
use think\Plugins;

class Plugin extends Plugins
{
    public $info = [
        'name' => 'editor',    // 插件标识唯一
        'title' => '编辑器插件',    // 插件名称
        'description' => '编辑器插件',    // 插件简介
        'status' => 0,    // 状态
        'install'     => 0,
        'author' => 'UKnowing',
        'version' => '1.0',
    ];

    public $menu = [];

    /**
     * 安装前的业务处理，可在此方法实现，默认返回true
     */
    public function install()
    {
        return true;
    }

    /**
     * 卸载前的业务处理，可在此方法实现，默认返回true
     */
    public function uninstall()
    {
        return true;
    }

    public function editor($param)
    {
        $info = $this->getInfo();
        if(!$info['status'] || !$info['install'])
        {
            return  false;
        }
        $this->view->assign('config',$this->getConfig());
        $this->view->assign($param);
        $isMobile = Request::isMobile() && get_setting('mobile_enable')==1 ? 1 : 0;
        $this->view->assign('isMobile',$isMobile);
        return $this->view->fetch('./editor');
    }
}