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
use think\App;
use think\facade\Request;

class Links extends Backend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
        $this->table = 'links';
	}

	public function index()
	{
        $columns = [
            ['id'  , '编号'],
            ['name', '网站名称'],
            ['url','网站地址','text'],
            ['logo','网站logo','image'],
            ['description','描述'],
            ['sort','排序'],
            ['status', '状态', 'radio', '0',[
                ['0' => '否'],
                ['1' => '是']
            ]],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['text', 'name', '网站名称', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
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
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
	}

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $data['create_time'] = time();
            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('name','网站名称','填写网站名称')
            ->addText('url','网站地址','填写网站地址')
            ->addImage('logo','网站logo','上传网站logo')
            ->addTextarea('description','描述','填写描述')
            ->addText('sort','排序','填写排序')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file']);
            $data['update_time'] = time();
            $result = db($this->table)->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info = db($this->table)->where('id',$id)->find()->toArray();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','网站名称','填写网站名称',$info['name'])
            ->addText('url','网站地址','填写网站地址',$info['url'])
            ->addImage('logo','网站logo','上传网站logo',$info['logo'])
            ->addTextarea('description','描述','填写描述',$info['description'])
            ->addText('sort','排序','填写排序',$info['sort'])
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }
}