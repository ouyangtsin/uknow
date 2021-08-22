<?php

namespace app\common\traits;

use app\common\library\builder\FormBuilder;
use app\common\library\builder\MakeBuilder;
use app\common\library\builder\TableBuilder;
use app\common\library\helper\ExcelHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\facade\Request;

/**
 * Trait Curd
 * @package app\traits
 */
trait CurdBack
{
    // 列表
    public function index(){
        // 获取列表数据
        $columns = $this->makeBuilder->getListColumns($this->table);
        // 获取搜索数据
        $search = $this->makeBuilder->getListSearch($this->table);
        // 获取当前模块信息
        $module = db('module')->where('table_name', $this->table)->find();

        // 获取主键
        $pk = $module['pk'] ?? 'id';
        // 搜索
        if ($this->request->param('_list'))
        {
            $where = $this->makeBuilder->getListWhere($this->table);
            $orderByColumn = $this->request->param('orderByColumn') ?? $pk;
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            return $this->model::getList($where,[$orderByColumn => $isAsc]);
        }

        // 构建页面
        return $this->tableBuilder
            ->setUniqueId($pk)                              // 设置主键
            ->addColumns($columns)                         // 添加列表字段数据
            ->setSearch($search)                            // 添加头部搜索
            ->addColumn('right_button', '操作', 'btn')      // 启用右侧操作列
            ->addRightButtons($module['right_button'])        // 设置右侧操作列
            ->addTopButtons($module['top_button'])            // 设置顶部按钮组
            ->fetch();
    }

    // 添加
    public function add()
    {
        if ($this->request->isPost())
        {
            $data = $this->makeBuilder->changeFormData($this->request->except(['file'], 'post'), $this->table);
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
                    $this->error($result['添加失败']);
                } else {
                    $this->success($result['添加成功'],'index');
                }
            }
        }
        // 获取字段信息
        $columns = $this->makeBuilder->getAddColumns($this->table);
        // 获取分组后的字段信息
        $groups = $this->makeBuilder->getAddGroups($this->table, $columns);
        return $groups ? FormBuilder::getInstance()->addGroup($groups)->fetch() : FormBuilder::getInstance()->addFormItems($columns)->fetch();
    }

    // 修改
    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->makeBuilder->changeFormData($this->request->except(['file'], 'post'), $this->table);
            $result = $this->validate($data, $this->validate);
            if (!$result) {
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
        // 获取字段信息
        $columns = $this->makeBuilder->getAddColumns($this->table, $info);
        // 获取分组后的字段信息
        $groups = $this->makeBuilder->getAddGroups($this->table, $columns);

        return $groups ? FormBuilder::getInstance()->addGroup($groups)->fetch() : FormBuilder::getInstance()->addFormItems($columns)->fetch();
    }

    // 删除
    public function delete(string $id)
    {
        if ($this->request->isPost()) {
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if($this->model::destroy($ids)){
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }else{
                    return ['error' => 1, 'msg' => '删除失败'];
                }
            }

            if($this->model::destroy($id))
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => '删除失败'];
        }
    }

    // 排序
    public function sort()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            $info = $this->model->find($data['id']);
            if ($info->sort != $data['sort']) {
                $info->sort = $data['sort'];
                $info->save();
                return json(['error' => 0, 'msg' => '修改成功!']);
            }
        }
    }

    // 状态变更
    public function state( $id)
    {
        if ($this->request->isPost()) {
            $info = $this->model->find($id);
            $info['status'] = $info['status'] == 1 ? 0 : 1;
            $info->save();
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }

    // 导出
    public function export()
    {
        $module = db('module')->where('table_name', $this->table)->find();
        //存在模块
        if($module)
        {
            $pk = $this->makeBuilder->getPrimarykey($this->table);
            $columns = $this->makeBuilder->getListColumns($this->table);
            $where = $this->makeBuilder->getListWhere($this->table);
            $orderByColumn = $this->request->param('orderByColumn') ?? $pk;
        }else{
            $pk = $this->makeBuilder->getPrimarykey($this->table);
            $columns = $this->makeBuilder->getListColumns($this->table);
            $where = $this->makeBuilder->getListWhere($this->table);
            $orderByColumn = $this->request->param('orderByColumn') ?? $pk;
        }

        $isAsc = $this->request->param('isAsc') ?? 'desc';
        $list = $this->model::where($where)
            ->order([$orderByColumn => $isAsc])
            ->select();

        // 初始化表头数组
        $str = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($columns as $k => $v) {
            $sheet->setCellValue($str[$k] . '1', $v['1']);
        }
        foreach ($list as $key => $value) {
            foreach ($columns as $k => $v) {
                // 修正字典数据
                if (isset($v[4]) && is_array($v[4]) && !empty($v[4])) {
                    $value[$v['0']] = $v[4][$value[$v['0']]];
                }
                $sheet->setCellValue($str[$k].($key+2),$value[$v['0']]);
            }
        }

        $moduleName = $module ? $module['module_name'] : '';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $moduleName . '导出' . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        return ExcelHelper::exportData($list,$columns,$moduleName . '导出');
    }
}
