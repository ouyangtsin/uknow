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
use app\admin\model\AuthGroup as UserGroup;
use app\common\controller\Backend;
use think\App;

/**
 * 菜单权限节点管理
 * Class AuthGroup
 * @package app\admin\controller\system
 */
class AuthGroup extends Backend
{
	public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $this->model = new UserGroup();
        $this->table = 'auth_group';
        $this->validate = 'AuthGroup';
    }

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['title', '组名称'],
            ['status', '是否启用', 'status', '0',[
                ['0' => '禁用'],
                ['1' => '启用']
            ]],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['text', 'title', '组名称', 'LIKE'],
        ];
        // 搜索
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'asc';
            // 排序处理
            $list = db($this->table)
                ->order([$orderByColumn => $isAsc,'create_time'=>'asc'])
                ->select()
                ->toArray();
            // 渲染输出
            return [
                'total'        => count($list),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => $list,
            ];
        }
        // 构建页面
        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setPagination('false')
            ->addRightButtons(['edit','delete','permission'=>[
                'type'          =>'permission',
                'title'         => '权限管理',
                'icon'          => 'fa fa-cogs',
                'class'         => 'btn btn-success btn-xs uk-ajax-open',
                'url'           => (string)url('permission',['id'=>'__id__']),
                'href'          =>''
            ]])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }

    public function add()
    {
        if ($this->request->isPost())
        {
            $data = $this->request->post();
            $result = $this->validate($data, $this->validate);
            $permission = db('users_permission')->where('group_type','system')->column('name,value');
            foreach ($permission as $key=>$val)
            {
                $data['permission'][$val['name']]=$val['value'];
            }
            $data['permission'] = json_encode($data['permission'],JSON_UNESCAPED_UNICODE);

            if (!$result) {
                $this->error($result);
            } else {
                $result = $this->model->create($data);
                if ($result) {
                    $this->success('添加成功','index');
                } else {
                    $this->error('添加失败');
                }
            }
        }
        return $this->formBuilder->addFormItems([
                ['text', 'title', '系统组名称', '系统组名称', '', [], '', '', '', true],
                ['checkbox2', 'rules', '权限规则', '', json_encode($this->auth->getGroupAuthRule()), '', '', '', true],
                ['radio', 'status', '启用状态', '', ['0' => '禁用','1' => '启用'], 0, '', '', true],
            ])
            ->fetch();
    }

	public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data = $this->request->post();

            $result = $this->validate($data, $this->validate);
            if (!$result) {
                $this->error($result);
            } else {
                $result = $this->model->update($data);
                if ($result) {
                    $this->success('修改成功', 'index');
                } else {
                    $this->error('修改失败');
                }
            }
        }

        $info =$this->model->find($id)->toArray();

        return $this->formBuilder->addFormItems([
                ['hidden', 'id', $info['id']],
                ['text', 'title', '系统组名称', '系统组名称', $info['title'], [], '', '', '', true],
                ['checkbox2', 'rules', '权限规则', '', json_encode($this->auth->getGroupAuthRule($id)), $info['rules'], '', '', true],
                ['radio', 'status', '启用状态', '', ['0' => '禁用','1' => '启用'], $info['status'], '', '', true],
            ])
            ->fetch();
    }

    public function delete(string $id)
    {
        if ($this->request->isPost())
        {
            if (strpos($id, ',') !== false) {
                $ids = explode(',',$id);

                if(in_array(1,$ids) || in_array(2,$ids))
                {
                    return ['error' => 1, 'msg' => '管理员组不能删除'];
                }

                if($this->model::destroy($ids)){
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }else{
                    return ['error' => 1, 'msg' => '删除失败'];
                }
            }

            if($id==1 || $id==2)
            {
                return ['error' => 1, 'msg' => '管理员组不能删除'];
            }

            if($this->model::destroy($id))
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => '删除失败'];
        }
    }

    public function permission($id=0)
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post())
            {
                foreach ($data as $k => $v) {
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

                $data = json_encode($data,JSON_UNESCAPED_UNICODE);
                $result= $this->model->where('id',$id)->update(['permission'=>$data]);

                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }

        $permission = $this->model->where(['id'=>$id])->value('permission');
        $permission = json_decode($permission,true);
        $list = db('users_permission')->where('group_type','system')->order('sort','asc')->column('type,name,title,tips,option,value');
        $columns = array();
        foreach ($list as $key=>$val)
        {
            $list[$key]['option'] = json_decode($val['option'],true);
            if(!in_array($val['type'],['radio','checkbox','select','array']))
            {
                unset($list[$key]['option']);
            }
            if(isset($permission[$val['name']]))
            {
                $list[$key]['value'] = $permission[$val['name']];
            }
        }
        foreach ($list as $key=>$val)
        {
            $columns[$key] = array_values($val);
        }

        // 构建页面
        return $this->formBuilder
            ->addFormItems($columns)
            ->setFormUrl((string)url('permission',['id'=>$id]))
            ->fetch();
    }
}