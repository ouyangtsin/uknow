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

namespace app\admin\backend\system;
use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;
use app\common\model\Nav as NavModel;
use think\App;

/**
 * 导航管理
 * Class Nav
 * @package app\admin\controller\system
 */
class Nav extends Backend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new NavModel();
		$this->table = 'nav';
	}

	//导航管理首页
	public function index()
	{
        $columns = [
            ['id'  , '导航ID'],
            ['title', '导航标题'],
            ['module', '模块'],
            ['controller', '控制器'],
            ['action', '方法'],
            ['url','外部连接','link'],
            ['type', '导航类型', 'radio', '1', [0=>'外部链接','1' => '顶部导航','2' => '底部导航']],
            ['sort','排序'],
            ['status', '是否显示', 'radio', '0',['0' => '否','1' => '是']],
            ['target', '新窗口打开', 'radio', '1', ['0' => '否','1' => '是']],
            ['need_login', '登陆可见', 'radio', '1', ['0' => '否','1' => '是']],
            ['is_home', '默认首页', 'radio', '1', ['0' => '否','1' => '是']],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['select', 'type', '导航类型', '=','',['1' => '顶部导航','2' => '底部导航']],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            $list = db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();

            $list = TreeHelper::tree($list);
            foreach ($list as $k => $v) {
                $list[$k]['title'] = $v['left_title'];
                $list[$k]['icon'] =  $v['icon'] ? "<i class=\"{$v['icon']}\"></i>" : '';
            }
            return [
                'total' => count($list),
                'per_page' => 10000,
                'current_page' => 1,
                'last_page' => 1,
                'data' => $list,
            ];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setPagination('false')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->setPagination('false')
            ->setParentIdField('pid')
            ->fetch();
	}

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            if($data['is_home'])
            {
                $this->model->update(['is_home'=>0],['type'=>$data['type']]);
            }
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        $list = db('nav')
            ->select()
            ->toArray();
        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        return $this->formBuilder
            ->addRadio('type','导航类型','选择导航类型',['1' => '顶部导航','2' => '底部导航'],1)
            ->addSelect('pid','父级标题','选择父级标题',$result)
            ->addText('title','导航标题','填写导航标题')
            ->addText('module','模块','填写导航模块','ask')
            ->addText('controller','控制器','填写导航控制器')
            ->addText('action','方法','填写导航方法')
            ->addText('url','外部连接','填写导航连接,链接填写后模块,控制器,方法,参数失效')
            ->addIcon('icon','导航图标','选择导航图标')
            ->addText('sort','排序值','填写排序值',0)
            ->addText('seo_title','SEO标题','填写SEO标题')
            ->addText('seo_keywords','SEO关键词','填写SEO关键词')
            ->addTextarea('seo_description','SEO描述','填写SEO描述')
            ->addRadio('target','新窗口打开','选择新窗口打开',['0' => '否','1' => '是'],0)
            ->addRadio('need_login','登陆可见','是否需要登陆可见',['0' => '否','1' => '是'],0)
            ->addRadio('is_home','默认首页','是否是默认首页',['0' => '否','1' => '是'],0)
            ->addRadio('status','是否显示','是否显示',['0' => '隐藏','1' => '显示'],1)
            ->fetch();
    }

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            if($data['is_home'])
            {
                $this->model->update(['is_home'=>0],['type'=>$data['type']]);
            }
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->find($id)->toArray();

        $list = db('nav')
            ->select()
            ->toArray();
        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addRadio('type','导航类型','选择导航类型',['1' => '顶部导航','2' => '底部导航'],$info['type'])
            ->addSelect('pid','父级标题','选择父级标题',$result,$info['pid'])
            ->addText('title','导航标题','填写导航标题',$info['title'])
            ->addText('module','模块','填写导航模块',$info['module'])
            ->addText('controller','控制器','填写导航控制器',$info['controller'])
            ->addText('action','方法','填写导航方法',$info['action'])
            ->addText('url','导航连接','填写导航连接,链接填写后模块,控制器,方法,参数失效',$info['url'])
            ->addIcon('icon','导航图标','选择导航图标',$info['icon'])
            ->addText('sort','排序值','填写排序值',$info['sort'])
            ->addText('seo_title','SEO标题','填写SEO标题',$info['seo_title'])
            ->addText('seo_keywords','SEO关键词','填写SEO关键词',$info['seo_keywords'])
            ->addTextarea('seo_description','SEO描述','填写SEO描述',$info['seo_description'])
            ->addRadio('target','新窗口打开','选择新窗口打开',['0' => '否','1' => '是'],$info['target'])
            ->addRadio('need_login','登陆可见','是否需要登陆可见',['0' => '否','1' => '是'],$info['need_login'])
            ->addRadio('is_home','默认首页','是否是默认首页',['0' => '否','1' => '是'],$info['is_home'])
            ->addRadio('status','是否显示','是否显示',['0' => '隐藏','1' => '显示'],$info['status'])
            ->fetch();
    }
}