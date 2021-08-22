<?php

namespace app\admin\backend\system;

use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;

class AuthRule extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\common\model\AuthRule();
        $this->table = 'auth_rule';
        $this->validate = 'AuthRule';
    }

    // 列表
    public function index(){
        // 获取列表数据
        $columns = [
            ['id'  , '编号'],
            ['title', '菜单名称'],
            ['icon', '图标','icon'],
            ['name','控制器/方法'],
            ['auth_open', '验证权限', 'radio', '0',['0' => '否','1' => '是']],
            ['status', '状态', 'radio', '1', ['0' => '否','1' => '是']],
            ['sort', '排序', 'sort'],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];
        // 搜索
        if ($this->request->isAjax())
        {
            $orderByColumn ='id';
            $isAsc = $this->request->param('isAsc')=='asc' ? 'DESC': 'ASC';
            $list = $this->model
                ->order([$orderByColumn => $isAsc,'sort'=>'asc'])
                ->select()
                ->toArray();

            foreach (TreeHelper::tree($list) as $k => $v) {
                $list[$k]['title'] = $v['left_title'];
                $list[$k]['icon'] =  $v['icon'] ? "<i class=\"{$v['icon']}\"></i>" : '';
            }

            // 渲染输出
            return [
                'total' => count($list),
                'per_page' => 10000,
                'current_page' => 1,
                'last_page' => 1,
                'data' => $list,
            ];
        }
        // 构建页面
        return $this->tableBuilder
            ->setUniqueId('id')                              // 设置主键
            ->addColumns($columns)                         // 添加列表字段数据
            ->addColumn('right_button', '操作', 'btn')      // 启用右侧操作列
            ->addRightButton('info', [                      // 添加额外按钮
                'title' => '添加',
                'icon'  => 'fa fa-plus',
                'class' => 'btn btn-success btn-xs',
                'href'  => url('add', ['pid' => '__id__'])
            ])
            ->addRightButtons(['edit','delete'])        // 设置右侧操作列
            ->addTopButtons(['add','delete','export'])            // 设置顶部按钮组
            ->addTopButton('default', [
                'title'       => '展开/折叠',
                'icon'        => 'fas fa-exchange-alt',
                'class'       => 'btn btn-info treeStatus',
                'href'        => '',
                'onclick'     => 'UK.operate.treeStatus()'
            ]) // 自定义按钮
            ->setPagination('false')                        // 关闭分页显示
            ->setParentIdField('pid')                       // 设置列表树父id
            ->fetch();
    }

    // 添加
    public function add()
    {
        if ($this->request->isPost())
        {
            $data = $this->request->except(['file'], 'post');
            $result = $this->validate($data, $this->validate);
            if (!$result) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                if ($data) {
                    foreach ($data as $k => $v) {
                        if (is_array($v)) {
                            $data[$k] = implode(',', $v);
                        }
                    }
                }
                $result = $this->model->create($data);
                if ($result) {
                    $this->success('添加成功','index');
                } else {
                    $this->error('添加失败');
                }
            }
        }

        $result = \app\common\model\AuthRule::getPidOptions();
        return $this->formBuilder
            ->addSelect('pid','父级标题','选择父级标题',$result)
            ->addIcon('icon','选择图标','选择图标')
            ->addText('name','控制器/方法','控制器/方法,如system.AuthRule/index')
            ->addText('title','菜单名称','请输入菜单名称')
            ->addText('param','附加参数','URL地址后的参数，如 type=button&name=my')
            ->addRadio('auth_open','验证权限','选择验证权限',['1' => '是','0' => '否'],1)
            ->addRadio('status','菜单状态','选择菜单状态',['0' => '禁用','1' => '启用'],1)
            ->addText('sort','排序值','',50)
            ->fetch();
    }

    // 修改
    public function edit(string $id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->except(['file'], 'post');
            $result = $this->validate($data, $this->validate);
            if (!$result) {
                // 验证失败 输出错误信息
                $this->error($result);
            } else {
                if ($data) {
                    foreach ($data as $k => $v) {
                        if (is_array($v)) {
                            $data[$k] = implode(',', $v);
                        }
                    }
                }
                $result = $this->model->update($data);
                if ($result) {
                    $this->success('修改成功', 'index');
                } else {
                    $this->error('修改失败');
                }
            }
        }

        $info =$this->model->find($id)->toArray();
        $result = \app\common\model\AuthRule::getPidOptions();
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('pid','父级标题','选择父级标题',$result,$info['pid'])
            ->addIcon('icon','选择图标','选择图标',$info['icon'])
            ->addText('name','控制器/方法','控制器/方法,如system.AuthRule/index',$info['name'])
            ->addText('title','菜单名称','请输入菜单名称',$info['title'])
            ->addText('param','附加参数','URL地址后的参数，如 type=button&name=my',$info['param'])
            ->addRadio('auth_open','验证权限','选择验证权限',['1' => '是','0' => '否'],$info['auth_open'])
            ->addRadio('status','菜单状态','选择菜单状态',['0' => '禁用','1' => '启用'],$info['status'])
            ->addText('sort','排序值','',$info['sort'])
            ->fetch();
    }
}
