<?php
namespace app\common\traits;
use app\common\library\helper\ExcelHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Trait Curd
 * @package app\common\traits
 */
trait Curd
{
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
        if ($this->request->isPost())
        {
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
    public function state($id)
    {
        if ($this->request->isPost())
        {
            $info = $this->model->find($id);
            $info['status'] = $info['status'] == 1 ? 0 : 1;
            $info->save();
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }

    // 导出
    public function export()
    {
        $pk = $this->makeBuilder->getPrimarykey($this->table);
        $columns = $this->makeBuilder->getListColumns($this->table);
        $where = $this->makeBuilder->getListWhere($this->table);
        $orderByColumn = $this->request->param('orderByColumn') ?? $pk;

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

        $moduleName = '';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $moduleName . '导出' . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        return ExcelHelper::exportData($list,$columns,$moduleName . '导出');
    }
}
