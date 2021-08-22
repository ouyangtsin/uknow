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
use app\common\library\helper\ModuleHelper;
use think\App;
use think\facade\Request;

class Module extends Backend
{
    protected $moduleHelper;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->moduleHelper = ModuleHelper::instance();
    }

    //模块列表
    public function index()
    {
        $status = $this->request->param('status',1);

        if ($this->request->param('_list')) {
            // 获取模板列表
            $list = $this->moduleHelper->getModuleList($status);
            // 渲染输出
            return [
                'total'        => count($list),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => $list,
            ];
        }

        return $this->tableBuilder
            ->addColumns([
                ['name', '模块标识'],
                ['title', '模块标题'],
                ['intro', '模块介绍'],
                ['default', '默认模块', 'status', '0',['0' => '否','1' => '是']],
                ['author', '作者'],
                ['version', '版本'],
                ['button', '操作', 'text']
            ])
            ->setUniqueId('name')
            ->setDataUrl( Request::baseUrl().'?_list=1&status='.$status)
            ->addTopButtons([
                'design'=>[
                    'title'   => '设计模块',
                    'class'   => 'btn btn-primary uk-ajax-open',
                    'href'    => (string)url('design'),
                ],
                'import'=>[
                    'title'   => '导入模块',
                    'class'   => 'btn btn-primary uk-ajax-open',
                    'href'    => (string)url('import'),
                ],
            ])
            ->setEditUrl((string)url('config', ['name' => '__id__']))
            ->setLinkGroup([
                [
                    'title'=>'已启用',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'未启用',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ],
                [
                    'title'=>'未安装',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
            ])
            ->fetch();
    }

    //模块配置
    public function config(string $name='')
    {
        if ($this->request->isPost()) {
            $result = $this->moduleHelper->configPost(Request::except(['file'], 'post'));
            if ($result['code'] == 1) {
                $this->success($result['msg'], 'index');
            } else {
                $this->error($result['msg']);
            }
        }

        $config = $this->moduleHelper->config($name);
        if(!$config)
        {
            $this->error('该模块无需配置','index');
        }

        // 如果模块自带配置模版的话加载模块自带的，否则调用表单构建器
        $file = app()->getRootPath() . 'app' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.html';
        if (file_exists($file)) {
            $this->assign([
                'config' => $config
            ]);
            return $this->fetch($file);
        }
        // 获取字段数据
        $columns = $this->moduleHelper->makeAddColumns($config);
        // 判断是否分组
        $group = $this->moduleHelper->checkConfigGroup($config);
        // 构建页面
        $this->formBuilder->setFormUrl((string)url('config'))->addHidden('id', $name);

        $group ? $this->formBuilder->addGroup($columns) : $this->formBuilder->addFormItems($columns);
        return $this->formBuilder->fetch();
    }

    //安装模块
    public function install($name)
    {
        return $this->moduleHelper->install($name);
    }


    //卸载模块
    public function uninstall($name)
    {
        return $this->moduleHelper->uninstall($name);
    }

    //更改模块状态
    public function status($name,$status)
    {
        return $this->moduleHelper->changeStatus($name,$status);
    }

    //设置默认模块
    public function state($id)
    {
        if ($this->request->isPost())
        {
            $info = db('module')->where('name', $id)->find();
            if($info['default'] && db('module')->where('default',1)->find()) {
                return json(['error'=>1, 'msg'=>'请保留一个默认模块!']);
            }
            db('module')->where([['default','<>', 0]])->update(['default' => 0]);
            db('module')->where('name', $id)->update(['default' => 1]);
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }

    //设计模块
    public function design()
    {
        $groups = [
            '基础信息' =>[
                ['text','title','模块名称','模块名称'],
                ['text','name','模块标识','模块标识'],
                ['text','author','模块作者','模块作者'],
                ['text','url','作者主页','作者主页'],
                ['text','version','模块版本','模块版本'],
                ['textarea','intro','模块简介','模块简介'],
            ],
            '模块配置'=>[],
            '模块菜单'=>[]
        ];
        return $this->formBuilder->addGroup($groups)->fetch();
    }

    //导入模块
    public function import()
    {

    }
}