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

class Column extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\ask\model\Column();
        $this->table = 'column';
    }

    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['cover','专栏图片','image'],
            ['name', '专栏标题','link',(string)url('ask/column/detail',['id'=>'__id__'])],
            ['user_name','用户','link',(string)url('member/index//index',['uid'=>'__uid__'])],
            ['focus_count','关注数量','number','','','',true],
            ['view_count','浏览数量','number','','','',true],
            ['post_count','文章数量','number','','','',true],
            ['join_count','用户数量','number','','','',true],
            ['description','专栏描述'],
            ['sort','排序'],
            ['verify', '是否审核', 'radio', '0',['0' => '待审核','1'=>'已审核','2'=>'拒绝审核']],
            ['create_time', '创建时间','datetime'],
        ];
        $search = [
            ['text', 'name', '专栏标题', 'LIKE'],
        ];
        $status = $this->request->param('verify',1);
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);

            // 排序处理
            return db($this->table)
                ->alias('c')
                ->where($where)
                ->where('verify',$status)
                ->order([$orderByColumn => $isAsc])
                ->join('users u','c.uid=u.uid')
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setDataUrl(Request::baseUrl().'?_list=1&verify='.$status)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->setLinkGroup([
                [
                    'title'=>'已审核',
                    'link'=>(string)url('index', ['verify' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['verify' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'已拒绝',
                    'link'=>(string)url('index', ['verify' => 2]),
                    'active'=> $status==2
                ]
            ])
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
            ->addText('name','专栏标题','填写专栏标题')
            ->addImage('cover','专栏图片','上传专栏图片')
            ->addTextarea('description','专栏简介','请填写专栏简介')
            ->addNumber('sort','专栏排序','填写专栏排序值')
            ->addRadio('recommend','是否推荐专栏','选择是否推荐专栏',['0' => '不推荐','1' => '推荐'],0)
            ->addRadio('verify','是否审核','是否审核',['0' => '待审核','1'=>'已审核','2'=>'拒绝审核'],0)
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

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','专栏标题','填写专栏标题',$info['name'])
            ->addImage('cover','专栏图片','上传专栏图片',$info['cover'])
            ->addTextarea('description','专栏简介','请填写专栏简介',$info['description'])
            ->addNumber('sort','专栏排序','填写专栏排序值',$info['sort'])
            ->addRadio('recommend','是否推荐专栏','选择是否推荐专栏',['0' => '不推荐','1' => '推荐'],$info['recommend'])
            ->addRadio('verify','是否审核','是否审核',['0' => '待审核','1'=>'已审核','2'=>'拒绝审核'],$info['verify'])
            ->fetch();
    }
}