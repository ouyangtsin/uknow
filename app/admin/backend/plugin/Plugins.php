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


namespace app\admin\backend\plugin;

use app\common\controller\Backend;
use app\common\library\helper\PluginsHelper;
use think\App;
use think\facade\Request;

class Plugins extends Backend
{
    protected $pluginServer;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->pluginServer = PluginsHelper::instance();
    }

    // 列表
    public function index()
    {
        if ($this->request->param('_list')) {
            // 获取插件列表
            $list = $this->pluginServer->localAddons();
            // 渲染输出
            return [
                'total'        => count($list),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => $list,
            ];
        }

        $js = '<script type="text/javascript">
                // 安装
                pluginInstall = function(id) {
                    var url = \''.url('install'). '\';
                    UK.modal.confirm("确认要安装插件?", function () {
                        var data = {"id": id};
                        UK.operate.submit(url, "post", "json", data);
                    });
                }
                pluginUninstall = function(id) {
                    var url = \''.url('uninstall').'\';
                    UK.modal.confirm("确认要卸载?", function () {
                        var data = {"id": id};
                        UK.operate.submit(url, "post", "json", data);
                    });
                }
            </script>';

        return $this->tableBuilder
            ->addColumns([ // 批量添加列
                ['name', '编号'],
                ['title', '插件名称'],
                ['description', '插件介绍'],
                ['status', '状态(启用/禁用)', 'status', '0',[
                    ['0' => '禁用'],
                    ['1' => '启用']
                ]],
                ['author', '作者'],
                ['version', '版本'],
                ['button', '操作', 'text']
            ])
            ->setUniqueId('name')
            ->setEditUrl((string)url('config', ['name' => '__id__']))
            ->addTopButtons([
                'add'=>[
                    'title'   => '设计插件',
                    'icon'    => 'fa fa-plus',
                    'class'   => 'btn btn-success uk-ajax-open',
                    'href'    => '',
                    'onclick' => 'UK.operate.add()',
                ]])
            ->setAddUrl((string)url('design'))
            ->setExtraJs($js)
            ->fetch();
    }

    // 插件配置信息预览
    public function config(string $name='')
    {
        if ($this->request->isPost()) {
            $result = $this->pluginServer->configPost(Request::except(['file'], 'post'));
            if ($result['code'] == 1) {
                $this->success($result['msg'], 'index');
            } else {
                $this->error($result['msg']);
            }
        }

        $config = $this->pluginServer->config($name);
        if(!$config)
        {
            $this->error('该插件无需配置','index');
        }

        // 如果插件自带配置模版的话加载插件自带的，否则调用表单构建器
        $file = app()->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.html';
        if (file_exists($file)) {
            $this->assign([
                'config' => $config
            ]);
            return $this->fetch($file);
        }

        // 获取字段数据
        $columns = $this->pluginServer->makeAddColumns($config);
        // 判断是否分组
        $group = $this->pluginServer->checkConfigGroup($config);
        // 构建页面
        $this->formBuilder->setFormUrl((string)url('config'))->addHidden('id', $name);
        $group ? $this->formBuilder->addGroup($columns) : $this->formBuilder->addFormItems($columns);
        return $this->formBuilder->fetch();
    }

    //设计插件
    public function design()
    {
        $groups = [
            '基础信息' =>[
                ['text','title','插件名称','插件名称'],
                ['text','name','插件标识','插件标识'],
                ['text','author','插件作者','插件作者'],
                ['text','url','作者主页','作者主页'],
                ['text','version','插件版本','插件版本'],
                ['textarea','intro','插件简介','插件简介'],
            ],
            '插件配置'=>[],
            '插件菜单'=>[]
        ];
        return $this->formBuilder->addGroup($groups)->fetch();
    }


    // 更改插件状态 [启用/禁用]
    public function state($id)
    {
        return $this->pluginServer->state($id);
    }

    // 安装插件
    public function install($id)
    {
        return $this->pluginServer->install($id);
    }

    // 卸载插件
    public function uninstall($id)
    {
        return $this->pluginServer->uninstall($id);
    }
}
