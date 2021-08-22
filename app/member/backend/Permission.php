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
use app\admin\model\Permission as PermissionModel;
use app\common\library\helper\ArrayHelper;
use think\App;
use think\facade\Request;

class Permission extends Backend
{
	public function initialize()
    {
        parent::initialize();
        $this->model = new PermissionModel();
        $this->table = 'permission';
    }

	public function index()
	{
        $columns = [
            ['id','编号'],
            ['name', '配置名称'],
            ['title','配置标题'],
            ['type','配置类型','radio',0,get_setting('module_field_type')],
            ['group_type','分组类型','radio',0,['system'=>'系统组','score'=>'积分组','power'=>'声望组']],
            ['value','默认值'],
            ['sort','排序'],
        ];
        $search = [
            ['text', 'name', '配置名称', 'LIKE'],
            ['select', 'type', '配置类型', '=','',get_setting('module_field_type')]
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('uid',$search);
            // 排序处理
            return $this->model
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
            ->setPagination('false')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
	}

	public function add()
	{
        if ($this->request->isPost())
        {
            $data = $this->request->except(['file'], 'post');
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $list = db('users_permission')->where('group_type',$data['group_type'])->order('sort','asc')->column('type,name,title,option,value');
                $dbName = $data['group_type'] == 'system' ? 'auth_group' : 'users_'.$data['group_type'].'_group';
                $permission = db($dbName)->column('id,permission');

                foreach ($permission as $k=>$v)
                {
                    $permission_option = json_decode($v['permission'],true);
                    foreach ($list as $key=>$val)
                    {
                        if(!isset($permission_option[$val['name']]))
                        {
                            $permission_option[$val['name']] = $val['value'];
                        }
                    }
                    db($dbName)->where(['id'=>$v['id']])->update(['permission'=>json_encode($permission_option,JSON_UNESCAPED_UNICODE)]);
                }

                $this->success('添加成功','index');
            }
        }
        return $this->formBuilder
            ->addText('name','配置名称','填写配置名称')
            ->addText('title','配置标题','填写配置标题')
            ->addSelect('group_type','分组类型','请选择配置类型',['system'=>'系统组','score'=>'积分组','power'=>'声望组'])
            ->addSelect('type','配置类型','请选择配置类型',get_setting('module_field_type'))
            ->addTextarea('tips','配置简介','填写配置简介')
            ->addText('value','默认值','填写默认值')
            ->addTextarea('option','配置选项','填写配置选项')
            ->addText('sort','排序值','填写排序值','0')
            ->fetch();
	}

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'], 'post');
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $result = $this->model->update($data);
            if ($result) {
                $list = db('users_permission')->where('group_type',$data['group_type'])->order('sort','asc')->column('type,name,title,option,value');
                $dbName = $data['group_type'] == 'system' ? 'auth_group' : 'users_'.$data['group_type'].'_group';
                $permission = db($dbName)->column('id,permission');

                foreach ($permission as $k=>$v)
                {
                    $permission_option = json_decode($v['permission'],true);
                    foreach ($list as $key=>$val)
                    {
                        if(!isset($permission_option[$val['name']]))
                        {
                            $permission_option[$val['name']] = $val['value'];
                        }
                    }
                    db($dbName)->where(['id'=>$v['id']])->update(['permission'=>json_encode($permission_option,JSON_UNESCAPED_UNICODE)]);
                }

                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->find($id)->toArray();
        $info['option'] = json_decode($info['option'],true);
        $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','配置名称','填写配置名称',$info['name'])
            ->addText('title','配置标题','填写配置标题',$info['title'])
            ->addSelect('group_type','分组类型','请选择配置类型',['system'=>'系统组','score'=>'积分组','power'=>'声望组'],$info['group_type'])
            ->addSelect('type','配置类型','请选择配置类型',get_setting('module_field_type'),$info['type'])
            ->addTextarea('tips','配置简介','填写配置简介',$info['tips'])
            ->addText('value','默认值','填写默认值',$info['value'])
            ->addTextarea('option','配置选项','填写配置选项',$info['option'])
            ->addText('sort','排序值','填写排序值',$info['sort'])
            ->fetch();
    }

}