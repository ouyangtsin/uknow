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


namespace app\ask\backend;
use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;
use think\App;
use think\facade\Request;

class Category extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\ask\model\Category();
        $this->table = 'category';
    }

    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['icon','分类图标','image'],
            ['title','分类名称'],
            ['type', '分类类型'],
            ['url_token','分类别名'],
            ['sort','排序'],
            ['status', '是否删除', 'radio', '0',['0' => '否','1' => '是']],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];
        $search = [
            ['text', 'title', '分类名称', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            $list = db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();

            $list = TreeHelper::tree($list);
            foreach ($list as $k => $v) {
                $list[$k]['title'] = $v['left_title'];
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
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        $list = db('category')
            ->select()
            ->toArray();

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        return $this->formBuilder
            ->addSelect('pid','父级分类','选择父级分类',$result)
            ->addText('title','分类名称','填写分类名称')
            ->addImage('icon','分类图标','分类导航图标')
            ->addRadio('type','分类类型','选择分类类型',['all' => '通用分类','question' => '问答分类','article' => '文章分类'],'all')
            ->addText('sort','排序值','填写排序值',0)
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file']);
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->find($id)->toArray();

        $list = db('category')
            ->select()
            ->toArray();

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('pid','父级分类','选择父级分类',$result,$info['pid'])
            ->addText('title','分类名称','填写分类名称',$info['title'])
            ->addImage('icon','分类图标','分类导航图标',$info['icon'])
            ->addRadio('type','分类类型','选择分类类型',['all' => '通用分类','question' => '问答分类','article' => '文章分类'],$info['type'])
            ->addText('sort','排序值','填写排序值',$info['sort'])
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }
}