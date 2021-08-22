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
use think\App;
use think\facade\Request;

class Question extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\ask\model\Question();
        $this->table = 'question';
    }

    public function index()
    {
        $columns = [
            ['id'  , '问题ID'],
            ['title', '问题标题','link',(string)url('ask/question/detail',['id'=>'__id__'])],
            ['answer_count','回答数量','number','','','',true],
            ['comment_count','评论数量','number','','','',true],
            ['view_count','浏览数量','number','','','',true],
            ['user_name','用户','link',(string)url('member/index//index',['uid'=>'__uid__'])],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['text', 'title', '问题标题', 'LIKE'],
        ];

        $status = $this->request->param('status',1);

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            return db($this->table)
                ->alias('q')
                ->where($where)
                ->where(['q.status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->join('users u','q.uid=u.uid')
                ->field('q.*,u.user_name')
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
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons([
                'config' => [
                    'title'       => '编辑',
                    'icon'        => 'fa fa-edit',
                    'class'       => 'btn btn-success btn-xs',
                    'href'        => (string)url('ask/question/publish', ['id' => '__id__']),
                    'target'      => '_blank',
                ],
                'seo' => [
                    'title'       => 'SEO设置',
                    'icon'        => '',
                    'href'       =>'',
                    'class'       => 'btn btn-danger btn-xs uk-ajax-open',
                    'url'        => (string)url('seo', ['id' => '__id__']),
                ],
            ])
            ->addTopButtons(['delete'])
            ->setLinkGroup([
                [
                    'title'=>'列表',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'已删除',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
            ])
            ->fetch();
    }

    // 状态变更
    public function state( $id)
    {
        if ($this->request->isPost())
        {
            $info = db($this->table)->find($id);
            $info['status'] = $info['status'] == 1 ? 0 : 1;
            db($this->table)->where('id',$id)->update(['status'=>$info['status']]);
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }

    // 删除
    public function delete( $id)
    {
        if ($this->request->isPost()) {
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if(db($this->table)->whereIn('id',$ids)->update(['status'=>0])){
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }else{
                    return ['error' => 1, 'msg' => '删除失败'];
                }
            }

            if(db($this->table)->whereIn('id',$id)->update(['status'=>0]))
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => '删除失败'];
        }
    }

    public function seo($id=0)
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = $this->model->update($data);
            if (!$result) {
                $this->error('提交失败');
            } else {
                $this->success('提交成功','index');
            }
        }

        $info =$this->model->find($id)->toArray();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('seo_title','SEO名称','填写SEO名称',$info['seo_title'])
            ->addText('seo_keywords','SEO关键词','填写SEO关键词',$info['seo_keywords'])
            ->addTextarea('seo_description','SEO描述','填写SEO描述',$info['seo_description'])
            ->fetch();
    }
}