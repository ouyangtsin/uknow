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



namespace plugins\systemInfo;
use think\facade\App;
use think\facade\Db;
use think\Plugins;

class Plugin extends Plugins
{
    public $info = [
        'name' => 'systemInfo',    // 插件标识唯一
        'title' => '系统信息',    // 插件名称
        'description' => '后台首页系统信息',    // 插件简介
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

    public function systemInfo($param=[])
    {
        //系统信息
        $mysqlVersion = Db::query('SELECT VERSION() AS ver');
        $system = [
            'url'             => $_SERVER['HTTP_HOST'],
            'document_root'   => $_SERVER['DOCUMENT_ROOT'],
            'server_os'       => PHP_OS,
            'server_port'     => $_SERVER['SERVER_PORT'],
            'server_ip'       => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '',
            'server_soft'     => $_SERVER['SERVER_SOFTWARE'],
            'php_version'     => PHP_VERSION,
            'mysql_version'   => $mysqlVersion[0]['ver'],
            'max_upload_size' => ini_get('upload_max_filesize'),
            'version'         => App::version(),
            'uk_version'    => UK_VERSION,
        ];

        // 查找一周内注册用户信息
        $user = db('users')->where('create_time', '>', time() - 60 * 60 * 24 * 7)->count();

        $view = [
            'config'=>$this->getConfig(),
            'system'        => $system,
            'user'          => $user,
            'message'       => 0,
            'messageCatUrl' => '',
        ];
        $this->view->assign($view);
        $this->view->assign($param);
        return $this->view->fetch('./index');
    }
}