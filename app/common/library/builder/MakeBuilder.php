<?php

namespace app\common\library\builder;
use app\common\library\helper\DateHelper;
use app\common\model\AuthRule;
use app\common\model\Module;
use app\common\library\helper\ArrayHelper;
use app\common\library\helper\TreeHelper;
use app\common\model\ModuleField;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\helper\Str;

class MakeBuilder
{
    /**
     * @var
     */
    private static $instance;

    public static function getInstance(): MakeBuilder
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取搜索条件
     * @param $pk
     * @param array $search
     * @return array
     */
    public function getWhere($pk,$search=[]): array
    {
        $searchWhere=[];
        foreach ($search as $key=>$val)
        {
            $searchWhere[$key]=[
                $val[0],
                $val[1],
                $val[2],
                $val[3]??'=',
                $val[4]??'',
                $val[5]??[],
                $val[6]??0,
                $val[7]??'',
                $val[8]??'',
                $val[9]??0
            ];
        }
        //全局查询条件
        $where = [];
        // 循环所有搜索字段，看是否有传递
        foreach ($searchWhere as $k => $v) {
            if (Request::param($v[1]) || Request::param($v[1]) === "0") {
                $searchKeywords = Request::param($v[1]);
                // 判断字段类型，默认为=
                if (isset($v[3]) && !empty($v[3])) {
                    $option = $v[3];
                } else {
                    $option = '=';
                }

                // 模型关联的数据需要考虑转化
                if ($v[6] == 2) {
                    // 需要转化的字段类型
                    $arr = ['text', 'textarea', 'number', 'hidden'];
                    if (in_array($v[0], $arr)) {
                        // 尝试查找关联的值
                        if (strtoupper($option) == 'LIKE') {
                            $relationFieldValue = db($v[7])->where($v[8], $option, '%' . $searchKeywords . '%')->value($pk);
                            // 重定义查询表达式
                            $option = '=';
                        } else {
                            $relationFieldValue = db($v[7])->where($v[8], $option, $searchKeywords)->value($pk);
                        }
                        // 重新定义搜索词
                        $searchKeywords = $relationFieldValue ?: '-1';
                    }
                }
                switch ($v[0]) {
                    case 'select':
                    case 'text':
                        if (strtoupper($option) == 'LIKE') {
                            $where[] = [$v[1], $option, '%' . $searchKeywords . '%'];
                        } else {
                            $where[] = [$v[1], $option, $searchKeywords];
                        }
                        break;
                    case 'time':
                    case 'datetime':
                    case 'date':
                        $getDateRange = DateHelper::dateRange($searchKeywords);
                        $where[] = [$v[1], 'between', $getDateRange];
                        break;
                    // 默认都当作文本框
                    default:
                        if (strtoupper($option) == 'LIKE') {
                            $where[] = [$v[1], $option, '%' . $searchKeywords . '%'];
                        } else {
                            $where[] = [$v[1], $option, $searchKeywords];
                        }
                }
            }
        }
        return $where;
    }

}
