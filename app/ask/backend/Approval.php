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
use app\common\model\Approval as ApprovalModel;
use think\App;
use think\facade\Request;

class Approval extends Backend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ApprovalModel();
		$this->table = 'approval';
	}

    public function index()
    {;
        $columns = [
            ['id'  , '编号'],
            ['type', '内容类型','radio', 'question',
                [
                    'question' => '问题',
                    'article' => '文章',
                    'answer' => '回答',
                    'modify_question'=>'修改问题',
                    'modify_article'=>'修改文章',
                    'modify_answer'=>'修改回答',
                    'article_comment'=>'文章评论'
                ]
            ],
            ['title'  , '标题'],
            ['user_name','用户','link',(string)url('member/index//index',['uid'=>'__uid__'])],
            ['create_time', '创建时间','datetime'],
        ];
        $type =  $this->request->param('type','');
        $search = [
            ['select', 'type', '审核类型', '=',$type,[
                'question' => '问题审核',
                'article' => '文章审核',
                'answer' => '回答审核',
                'modify_question'=>'修改问题',
                'modify_article'=>'修改文章',
                'modify_answer'=>'修改回答',
                'article_comment'=>'文章评论'
            ]]
        ];
        $status = $this->request->param('status',1);
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            $list = db($this->table)
                ->alias('a')
                ->where($where)
                ->where(['a.status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->join('users u','a.uid=u.uid')
                ->paginate([
                'query'     => Request::get(),
                'list_rows' => 15,
            ])->toArray();

            foreach ($list['data'] as $key=>$val)
            {
                $data = json_decode($val['data'],true);
                $list['data'][$key]['title'] = $data['title'] ?? '';
            }

            return $list;
        }
        $top_button = ['delete',
            'approval'=>[
                'title'   => '通过审核',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-success multiple disabled',
                'href'    => '',
                'onclick' => 'UK.operate.selectAll("'.(string)url('state').'","审核","approval")',
            ],
            'decline'=>[
                'title'   => '拒绝审核',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => '',
                'onclick' => 'UK.operate.selectAll("'.(string)url('state').'","审核","decline")',
            ]
        ];

        if($status==1 || $status==2)
        {
            $top_button = ['delete'];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->setPagination('false')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons($top_button)
            ->setLinkGroup([
                [
                    'title'=>'已审核',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'已拒绝',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ]
            ])
            ->fetch();
    }

    public function edit($id)
    {
        $info = db('approval')->where('id',$id)->find();
        $data = json_decode($info['data'],true);
        $category_list = \app\ask\model\Category::getCategoryListByType();
        $category_list = $category_list ? array_column($category_list,'title','id') : [];
        //发起问题审核
        if($info['type']=='question')
        {
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addText('title','问题标题','',$data['title'])
                ->addEditor('detail','问题详情','',htmlspecialchars_decode($data['detail']))
                ->addSelect('category_id','问题分类','',$category_list,$data['category_id'])
                ->addRadio('question_type','问题类型','',['normal' => '普通问题','reward' => '悬赏问题'],$data['question_type'])
                ->addRadio('is_anonymous','是否匿名','',[0 => '公开',1 => '匿名'],$data['is_anonymous'])
                ->fetch();
        }
        //修改问题审核
        if($info['type']=='modify_question')
        {
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addHidden('id',$data['id'])
                ->addText('title','问题标题','',$data['title'])
                ->addEditor('detail','问题详情','',htmlspecialchars_decode($data['detail']))
                ->addSelect('category_id','问题分类','',$category_list,$data['category_id'])
                ->addRadio('question_type','问题类型','',['normal' => '普通问题','reward' => '悬赏问题'],$data['question_type'])
                ->addRadio('is_anonymous','是否匿名','',[0 => '公开',1 => '匿名'],$data['is_anonymous'])
                ->fetch();
        }
        //发起文章审核
        if($info['type']=='article')
        {
            $column_list = \app\ask\model\Column::getColumnByUid($info['uid']);
            $column_list = $column_list ? array_column($category_list,'name','id') : [];
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addImage('cover','文章封面','',$data['cover']??'')
                ->addText('title','文章标题','',$data['title'])
                ->addEditor('message','文章详情','',htmlspecialchars_decode($data['message']))
                ->addSelect('category_id','文章分类','',$category_list,$data['category_id'])
                ->addSelect('column_id','文章专栏','',$column_list,$data['column_id']??'')
                ->fetch();
        }
        //修改文章审核
        if($info['type']=='modify_article')
        {
            $column_list = \app\ask\model\Column::getColumnByUid($info['uid']);
            $column_list = $column_list ? array_column($category_list,'name','id') : [];
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addHidden('id',$data['id'])
                ->addImage('cover','文章封面','',$data['cover']??'')
                ->addText('title','文章标题','',$data['title'])
                ->addEditor('message','文章详情','',htmlspecialchars_decode($data['message']))
                ->addSelect('category_id','文章分类','',$category_list,$data['category_id'])
                ->addSelect('column_id','文章专栏','',$column_list,$data['column_id']??'')
                ->fetch();
        }
    }

	//审核状态
	public function state($id)
	{
        if ($this->request->isPost()) {
            $ids = $this->request->post('id');
            $type = $this->request->post('type');
            if($type == 'approval')
            {
                if(ApprovalModel::approval($ids))
                {
                    return json(['error'=>0, 'msg'=>'审核成功!']);
                }
                return json(['error'=>1, 'msg'=>'审核失败!']);
            }

            if($type == 'decline')
            {
                if(ApprovalModel::decline($id))
                {
                    return json(['error'=>0, 'msg'=>'审核成功!']);
                }
                return json(['error'=>1, 'msg'=>'审核失败!']);
            }
        }
	}
}

