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
use app\admin\model\Route as RouteModel;
use think\App;
use think\facade\Request;

/**
 * 路由管理
 * Class Nav
 * @package app\admin\controller\system
 */
class Route extends Backend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new RouteModel();
        $this->table = 'route_rule';
    }

    public function index()
    {
        $columns = [
            ['id','编号'],
            ['title', '路由标题'],
            ['url','路由url'],
            ['rule','路由规则'],
            ['module','路由模块'],
        ];

        $search = [

        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            return db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');

            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('title','路由标题','输入路由标题')
            ->addText('url','路由url','输入路由url')
            ->addText('rule','路由规则','输入路由规则')
            ->addText('module','路由模块','输入路由所属模块')
            ->fetch();
    }

    // 修改
    public function edit(string $id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->except(['file'],'post');
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->find($id)->toArray();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('title','路由标题','输入路由标题',$info['title'])
            ->addText('url','路由url','输入路由url',$info['url'])
            ->addText('rule','路由规则','输入路由规则',$info['rule'])
            ->addText('module','路由模块','输入路由所属模块',$info['module'])
            ->fetch();
    }
}