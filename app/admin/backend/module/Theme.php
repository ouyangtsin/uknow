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

namespace app\admin\backend\module;
use app\common\controller\Backend;
use app\common\library\helper\ThemeHelper;
use think\App;
use think\facade\Request;

class Theme extends Backend
{
	public function index()
	{
        $name = $this->request->param('name');
        session('theme_type',$name);
        if ($this->request->param('_list')) {
            // 获取模板列表
            $list = ThemeHelper::instance($name)->localTemplates();
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
                templateInstall = function(id) {
                    var url = \''.url('install'). '\'' .url('uninstall').'\';
                    UK.modal.confirm("确认要卸载?", function () {
                        var data = {"id": id};
                        UK.operate.submit(url, "post", "json", data);
                    });
                }
            </script>';
        return $this->tableBuilder
            ->addColumns([ // 批量添加列
                ['name', '模板目录'],
                ['title', '模板名称'],
                ['description', '模板介绍'],
                ['status', '默认模板', 'status', '0',['0' => '否','1' => '是']],
                ['author', '作者'],
                ['version', '版本'],
                ['button', '操作', 'text']
            ])
            ->setUniqueId('name')
            ->setDataUrl( Request::baseUrl().'?_list=1&name='.$name)
            ->setEditUrl((string)url('config', ['module'=>$name,'name' => '__id__']))
            ->setExtraJs($js)
            ->fetch();
	}

    // 模板配置信息预览
    public function config($module,string $name='')
    {
        if ($this->request->isPost())
        {
            $result = ThemeHelper::instance($module)->configPost(Request::except(['file'], 'post'));
            if ($result['code'] == 1) {
                $this->success($result['msg'], 'index');
            } else {
                $this->error($result['msg']);
            }
        }

        $config = ThemeHelper::instance($module)->config($name);
        // 获取字段数据
        $columns = ThemeHelper::instance($module)->makeAddColumns($config);
        // 判断是否分组
        $group = ThemeHelper::instance($module)->checkConfigGroup($config);
        // 构建页面
        $this->formBuilder->setFormUrl((string)url('config'))->addHidden('id', $name);
        $group ? $this->formBuilder->addGroup($columns) : $this->formBuilder->addFormItems($columns);
        return $this->formBuilder->fetch();
    }

    // 是否设为默认模板
    public function state($id)
    {
        return ThemeHelper::instance(session('theme_type'))->state($id);
    }

    // 安装模板
    public function install($id)
    {
        return ThemeHelper::instance(session('theme_type'))->install($id);
    }

    // 卸载模板
    public function uninstall($id)
    {
        return ThemeHelper::instance(session('theme_type'))->uninstall($id);
    }
}