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
namespace app\member\backend;

use app\common\controller\Backend;
use app\common\library\helper\ArrayHelper;
use think\facade\Request;

/**
 * 用户认证
 * Class Verify
 * @package app\admin\controller\member
 */
class Verify extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\Verify();
        $this->table = 'users_verify';
    }

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['type', '认证类型','radio', '', get_setting('user_verify_type')],
            ['reason','审核理由'],
            ['create_time', '创建时间','datetime'],
        ];
        $type =  $this->request->param('type','');
        $search = [
            ['select', 'type', '审核类型', '=',$type,get_setting('user_verify_type')]
        ];
        $status = $this->request->param('status',1);
        $buttons = [
            'config' => [
                'title'       => '预览',
                'icon'        => '',
                'class'       => 'btn btn-success btn-xs uk-ajax-open',
                'href'        => (string)url('preview', ['id' => '__id__']),
            ],
            'approval'=>[
                'title'       => '通过',
                'icon'        => '',
                'class'       => 'btn btn-warning btn-xs uk-ajax-get',
                'url'        => (string)url('manager', ['type'=>'approval','id' => '__id__']),
                'href' =>''
            ],
            'refuse'=>[
                'title'       => '拒绝',
                'icon'        => '',
                'class'       => 'btn btn-danger btn-xs uk-ajax-get',
                'url'        => (string)url('manager', ['type'=>'refuse','id' => '__id__']),
                'href' =>''
            ]
        ];

        if($status>0)
        {
            unset($buttons['approval'],$buttons['refuse']);
        }

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            return db($this->table)
                ->where($where)
                ->where(['status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->setPagination('false')
            ->addRightButtons($buttons)
            ->addTopButtons(['delete'])
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

    /**
     * 预览认证内容
     */
    public function preview($id)
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post()) {
                foreach ($data as $k => $v)
                {
                    if (is_array($v)) {
                        if(isset($v['key']) && isset($v['value']))
                        {
                            $value = [];
                            foreach ($v['key'] as $k1=>$v1)
                            {
                                $value[$v1] = $v['value'][$k1];
                            }
                            $data[$k] = $value;
                        }
                    }
                }
                $result= db($this->table)->where(['id'=>$id])->update(['data'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }

        $info = db($this->table)->where(['id'=>$id])->find();
        $data = json_decode($info['data'],true);
        $list = db('verify_field')->where('verify_type',$info['type'])->column('type,name,title,tips,option,value');

        $columns = [];
        foreach ($list as $key=>$val)
        {
            $list[$key]['option'] = json_decode($val['option'],true);
            if(!in_array($val['type'],['radio','checkbox','select','array']))
            {
                unset($list[$key]['option']);
            }

            if(isset($data[$val['name']]))
            {
                $list[$key]['value'] = $data[$val['name']];
            }
        }

        foreach ($list as $key=>$val)
        {
            $columns[$key] = array_values($val);
        }

        // 构建页面
        return $this->formBuilder
            ->addFormItems($columns)
            ->setFormUrl((string)url('preview',['id'=>$id]))
            ->fetch();
    }

    /**
     * 认证审核管理
     * @param $type
     * @param $id
     */
    public function manager($type,$id)
    {
        if(! db($this->table)->find($id))
        {
            $this->error('审核数据不存在');
        }

        if(!in_array($type,['refuse','approval']))
        {
            $this->error('操作类型错误');
        }

        $status = $type=='approval' ? 1 : 2;

        db($this->table)->where(['id'=>$id])->update(['status'=>$status]);

        $this->success('操作成功');
    }

    /**
     * 认证字段管理
     * @return string
     */
    public function field()
    {
        $columns = [
            ['id'  , '编号'],
            ['name', '变量名'],
            ['verify_type','认证类型','radio','',get_setting('user_verify_type')],
            ['title','变量标题'],
            ['tips','描述'],
            ['type','变量类型'],
            ['value','变量值'],
            ['sort','排序'],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];
        $search = [
            ['select', 'verify_type', '认证类型', '=','',get_setting('user_verify_type')]
        ];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            $result = db('verify_field')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ;
            return $result->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->setSearch($search)
            ->addColumns($columns)
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
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $data['create_time'] = time();
            $result = db('verify_field')->insert($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }

        return $this->formBuilder
            ->addSelect('verify_type','认证类型','选择认证类型',get_setting('user_verify_type'))
            ->addSelect('type','变量类型','选择变量类型',get_setting('module_field_type'))
            ->addText('name','变量名','填写变量名')
            ->addText('title','变量标题','填写变量标题')
            ->addText('value','变量值','填写变量值')
            ->addTextarea('option','字段选项','填写字段选项,填写格式如：键|值')
            ->addNumber('sort','排序值','默认为0',0)
            ->addTextarea('tips','描述','填写描述')
            ->fetch();
    }

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file']);
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $data['update_time'] = time();
            $result = db('verify_field')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info = db('verify_field')->where('id',$id)->find();
        $info['option'] = $info['option'] ? json_decode($info['option'],true) : [];
        $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('verify_type','认证类型','选择认证类型',get_setting('user_verify_type'),$info['verify_type'])
            ->addSelect('type','变量类型','选择变量类型',get_setting('module_field_type'),$info['type'])
            ->addText('name','变量名','填写变量名',$info['name'])
            ->addText('title','变量标题','填写变量标题',$info['title'])
            ->addText('value','变量值','填写变量值',$info['value'])
            ->addTextarea('option','字段选项','填写字段选项,填写格式如：键|值', $info['option'])
            ->addNumber('sort','排序值','默认为0',$info['sort'])
            ->addTextarea('tips','描述','填写描述',$info['tips'])
            ->fetch();
    }
}