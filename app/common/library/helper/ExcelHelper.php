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

namespace app\common\library\helper;

use Exception;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelHelper
{
    /**
     * 导出Excel
     *
     * @param array $list
     * @param array $columns
     * @param string $filename
     * @param string $suffix
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function exportData($list = [], $columns = [], $filename = '', $suffix = '.xlsx')
    {
        $filename       .= "_" . date("Y_m_d", time());
        // 初始化表头数组
        $str = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($columns as $k => $v) {
            $sheet->setCellValue($str[$k] . '1', $v[1]);
        }
        foreach ($list as $key => $value) {
            foreach ($columns as $k => $v) {
                if (isset($v[4]) && is_array($v[4]) && !empty($v[4])) {
                    $value[$v[0]] = $v[4][$value[$v[0]]];
                }
                $sheet->setCellValue($str[$k].(intval($key)+2),$value[$v[0]]);
            }
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . $suffix);
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        //删除清空：
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    /**
     * 导出的另外一种形式(不建议使用)
     *
     * @param array $list
     * @param array $header
     * @param string $filename
     * @return bool
     */
    public static function exportCsvData($list = [], $header = [], $filename = '')
    {
        if (!is_array($list) || !is_array($header)) {
            return false;
        }

        // 清除之前的错误输出
        ob_end_clean();
        ob_start();

        !$filename && $filename = time();

        $html = "\xEF\xBB\xBF";
        foreach ($header as $k => $v) {
            $html .= $v[0] . "\t ,";
        }

        $html .= "\n";

        if (!empty($list)) {
            $info = [];
            $size = ceil(count($list) / 500);

            for ($i = 0; $i < $size; $i++) {
                $buffer = array_slice($list, $i * 500, 500);

                foreach ($buffer as $k => $row) {
                    $data = [];

                    foreach ($header as $key => $value) {
                        // 解析字段
                        $realData = self::formatting($header[$key], trim(self::formattingField($row, $value[1])), $row);
                        $data[] = str_replace(PHP_EOL, '', $realData);
                    }

                    $info[] = implode("\t ,", $data) . "\t ,";
                    unset($data, $buffer[$k]);
                }
            }

            $html .= implode("\n", $info);
        }

        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename={$filename}.csv");
        echo $html;
        exit();
    }

    /**
     * 导入
     *
     * @param $filePath
     * @param int $startRow
     * @return array|mixed
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public static function import($filePath, $startRow = 1): array
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        if (!$reader->canRead($filePath)) {
            $reader = new Xls();
            // setReadDataOnly Set read data only 只读单元格的数据，不格式化 e.g. 读时间会变成一个数据等
            $reader->setReadDataOnly(true);

            if (!$reader->canRead($filePath)) {
                throw new Exception('不能读取Excel');
            }
        }

        $spreadsheet = $reader->load($filePath);
        $sheetCount = $spreadsheet->getSheetCount();// 获取sheet的数量

        // 获取所有的sheet表格数据
        $exileData = [];
        $emptyRowNum = 0;
        for ($i = 0; $i < $sheetCount; $i++) {
            $currentSheet = $spreadsheet->getSheet($i); // 读取excel文件中的第一个工作表
            $allColumn = $currentSheet->getHighestColumn(); // 取得最大的列号
            $allColumn = Coordinate::columnIndexFromString($allColumn); // 由列名转为列数('AB'->28)
            $allRow = $currentSheet->getHighestRow(); // 取得一共有多少行

            $arr = [];
            for ($currentRow = $startRow; $currentRow <= $allRow; $currentRow++) {
                // 从第1列开始输出
                for ($currentColumn = 1; $currentColumn <= $allColumn; $currentColumn++) {
                    $val = $currentSheet->getCellByColumnAndRow($currentColumn, $currentRow)->getValue();
                    $arr[$currentRow][] = trim($val);
                }

                // $arr[$currentRow] = array_filter($arr[$currentRow]);
                // 统计连续空行
                if (empty($arr[$currentRow]) && $emptyRowNum <= 50) {
                    $emptyRowNum++;
                } else {
                    $emptyRowNum = 0;
                }
                // 防止坑队友的同事在excel里面弄出很多的空行，陷入很漫长的循环中，设置如果连续超过50个空行就退出循环，返回结果
                // 连续50行数据为空，不再读取后面行的数据，防止读满内存
                if ($emptyRowNum > 50) {
                    break;
                }
            }

            $exileData[$i] = $arr; // 多个sheet的数组的集合
        }

        // 这里我只需要用到第一个sheet的数据，所以只返回了第一个sheet的数据
        $returnData = $exileData ? array_shift($exileData) : [];

        // 第一行数据就是空的，为了保留其原始数据，第一行数据就不做array_fiter操作；
        $returnData = $returnData && isset($returnData[$startRow]) && !empty($returnData[$startRow]) ? array_filter($returnData) : $returnData;
        return $returnData;
    }

    /**
     * 格式化内容
     * @param array $array 头部规则
     * @param $value
     * @param $row
     * @return false|mixed|null|string 内容值
     */
    protected static function formatting(array $array, $value, $row)
    {
        !isset($array[2]) && $array[2] = 'text';
        switch ($array[2]) {
            // 文本
            case 'text' :
                return $value;
                break;
            // 日期
            case 'date' :
                return !empty($value) ? date($array[3], $value) : null;
                break;
            // 选择框
            case 'selectd' :
                return $array[3][$value] ?? null;
                break;
            // 匿名函数
            case 'function' :
                return isset($array[3]) ? call_user_func($array[3], $row) : null;
                break;
            // 默认
            default :
                break;
        }

        return null;
    }

    /**
     * 解析字段
     *
     * @param $row
     * @param $field
     * @return mixed
     */
    protected static function formattingField($row, $field): bool
    {
        $newField = explode('.', $field);
        if (count($newField) == 1) {
            return $row[$field];
        }

        foreach ($newField as $item) {
            if (isset($row[$item])) {
                $row = $row[$item];
            } else {
                break;
            }
        }
        return is_array($row) ? false : $row;
    }
}