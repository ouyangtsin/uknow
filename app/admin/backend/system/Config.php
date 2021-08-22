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
use app\common\library\builder\FormBuilder;
use app\common\library\helper\ArrayHelper;
use app\common\model\Config as ConfigModel;
use think\facade\Request;

class Config extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new ConfigModel();
        $this->table = 'config';
        $this->validate = 'Config';
    }

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['name', '变量名'],
            ['group','变量分组'],
            ['title','变量标题'],
            ['tips','描述'],
            ['type','变量类型'],
            ['value','变量值'],
            ['sort','排序'],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['select', 'group', '配置分组', '=','',get_setting('config_group')],
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

    public function config()
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post()) {
                foreach ($data as $k => $v) {
                    if (is_array($v) && isset($v['key']) && isset($v['value'])) {
                        $value = [];
                        foreach ($v['key'] as $k1=>$v1)
                        {
                            $value[$v1] = $v['value'][$k1];
                        }
                        $data[$k] = $value;
                    }
                }
                $configList = [];
                foreach ($this->model->select()->toArray() as $v)
                {
                    if (isset($data[$v['name']])) {
                        $value = $data[$v['name']];
                        $option = json_decode($v['option'],true);
                        if($v['type']=='array'){
                            $option =$value;
                            $value = 0;
                        } else{
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $v['value'] = $value;
                        $v['option'] = json_encode($option,JSON_UNESCAPED_UNICODE);
                        $v['create_time'] = time();
                        $v['update_time'] = time();
                        $configList[] = $v;
                    }
                }

                $result=$this->model->saveAll($configList);
                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            }
        }

        $list =$this->model->order(['sort'=>'ASC','id'=>'ASC'])->column('type,name,title,tips,option,value');
        $columns = array();
        foreach ($list as $key=>$val)
        {
            $val['option'] = json_decode($val['option'],true);
            if(!in_array($val['type'],['radio','checkbox','select','array']))
            {
                unset($val['option']);
            }
            $columns[$key] = array_values($val);
        }

        $filedGroup = get_setting('config_group');
        $fields = $this->model->select()->toArray();
        $groups = [];
        foreach ($filedGroup as $key => $value) {
            $groups[$value] = [];
            foreach ($fields as $k => $v) {
                if ($v['group'] == $key) {
                    $groups[$value][] = $v['name'];
                }
            }
        }
        $groupsNew = [];
        foreach ($groups as $key => $value) {
            foreach ($columns as $k => $v) {
                if (in_array($v[1], $value)) {
                    $groupsNew[$key][] = $v;
                }
            }
        }
        // 构建页面
        return $this->formBuilder
            ->addGroup($groupsNew)
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
            $result = db($this->table)->insert($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }

        return $this->formBuilder
            ->addSelect('group','变量分组','选择变量分组',get_setting('config_group'))
            ->addSelect('type','变量类型','选择变量类型',get_setting('module_field_type'))
            ->addText('name','变量名','填写变量名')
            ->addText('title','变量标题','填写变量标题')
            ->addText('value','变量值','填写变量值')
            ->addTextarea('option','字段选项','填写字段选项,填写格式如：键|值')
            ->addText('sort','排序值','填写排序值默认0',0)
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
            $result = db($this->table)->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info = db($this->table)->where('id',$id)->find();
        $info['option'] = json_decode($info['option'],true);
        $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('group','变量分组','选择变量分组',get_setting('config_group'),$info['group'])
            ->addSelect('type','变量类型','选择变量类型',get_setting('module_field_type'),$info['type'])
            ->addText('name','变量名','填写变量名',$info['name'])
            ->addText('title','变量标题','填写变量标题',$info['title'])
            ->addText('value','变量值','填写变量值',$info['value'])
            ->addTextarea('option','字段选项','填写字段选项,填写格式如：键|值', $info['option'])
            ->addText('sort','排序值','填写排序值默认0',$info['sort'])
            ->addTextarea('tips','描述','填写描述',$info['tips'])
            ->fetch();
    }
}