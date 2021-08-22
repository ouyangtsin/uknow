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


namespace app\admin\backend\module;

use app\common\controller\Backend;
use app\common\library\helper\BackupHelper;
use think\App;
use think\facade\Request;

class Database extends Backend
{
    protected $db = '', $dataDir;
    protected $config;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->config=array(
            'path'     => './backup/', // 数据库备份路径
            'part'     => 20971520,  // 数据库备份卷大小
            'compress' => 0,         // 数据库备份文件是否启用压缩 0不压缩 1 压缩
            'level'    => 9          // 数据库备份文件压缩级别 1普通 4 一般  9最高
        );
        $this->db = BackupHelper::getInstance($this->config);
    }

    // 数据列表
    public function database()
    {
        // 设置主键
        $pk = 'name';
        // 字段信息
        $columns = [
            ['name'  , '数据表'],
            ['engine', '存储引擎'],
            //['version', '版本'],
            ['row_format', '行格式'],
            ['rows', '行数', '', '', '', '', 'true'],
            //['avg_row_length', '平均每行包括的字节数'],
            ['data_size', '字节数', '', '', '', '', 'true'],
            ['data_length', '数据量'],
            //['max_data_length', '可容纳的最大数据量'],
            //['index_length', '索引占用磁盘的空间大小'],
            //['data_free', '已经分配，但目前没有使用的空间'],
            //['auto_increment', 'auto_increment'],
            ['comment', '额外信息'],
            ['create_time', '创建时间'],
            ['update_time', '更新时间'],
            //['check_time', '最后一次检查表的时间'],
            //['collation', '表的默认字符集和字符排序规则'],
            //['checksum', '整个表的实时校验和'],
            //['create_options', '创建表时指定的其他选项'],
        ];
        // 数据信息
        $list = $this->db->dataList();
        // 计算总大小
        $total = 0;
        foreach ($list as $k => $v) {
            $total += $v['data_length'];
            $list[$k]['data_size'] = $v['data_length'];
            $list[$k]['data_length'] = formatBytes($v['data_length']);
        }
        // 提示信息
        $pageTips = '数据库中共有 ' . count($list) . ' 张表，共计 ' . formatBytes($total);
        // 可搜索的字段
        $search = [
            ['text', 'name', '数据表', 'LIKE'],
        ];
        // 搜索
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? $pk;

            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $isAsc = $isAsc == 'desc' ? SORT_DESC : SORT_ASC;
            // 排序处理
            $date = array_column($list, $orderByColumn);
            array_multisort($date, $isAsc, $list);
            if (Request::param('name')) {
                foreach ($list as $k => $v) {
                    if (strpos($v['name'], Request::param('name')) == false) {
                        unset($list[$k]);
                    }
                }
            }
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
            ->setUniqueId($pk)                              // 设置主键
            ->addColumns($columns)                         // 添加列表字段数据
            ->setSearch($search)                            // 添加头部搜索
            ->setPageTips($pageTips, 'success')             // 提示信息
            ->setPagination('false')                        // 关闭分页显示
            ->addTopButtons([
                'backup' => [
                    'title'       => '备份',
                    'icon'        => 'fa fa-server',
                    'class'       => 'btn btn-success multiple disabled',
                    'href'        => '',
                    'target'      => '',
                    'onclick'     => 'UK.operate.database(\''. (string)url('backup').'\' , \'备份\')',
                ],
                'optimize' => [
                    'title'       => '优化',
                    'icon'        => 'fa fa-medkit',
                    'class'       => 'btn btn-primary multiple disabled',
                    'href'        => '',
                    'target'      => '',
                    'onclick'     => 'UK.operate.database(\''. (string)url('optimize').'\', \'优化\')',
                ],
                'repair' => [
                    'title'       => '修复',
                    'icon'        => 'fa fa-user-md',
                    'class'       => 'btn btn-warning multiple disabled',
                    'href'        => '',
                    'target'      => '',
                    'onclick'     => 'UK.operate.database(\''. (string)url('repair').'\', \'修复\')',
                ],
            ])
            ->fetch();
    }

    // 备份
    public function backup()
    {
        $tables = Request::param('id');
        if (!empty($tables)) {
            $tables = explode(',', $tables);
            foreach ($tables as $table) {
                $this->db->setFile()->backup($table, 0);
            }
            return json(['error' => 0, 'msg' => '备份成功！']);
        } else {
            return json(['error' => 1, 'msg' => '请选择要备份的表！']);
        }
    }

    // 优化
    public function optimize() {
        $tables = Request::param('id');
        if (empty($tables)) {
            return json(['error'=>1,'msg'=>'请选择要优化的表！']);
        }
        $tables = explode(',',$tables);
        if ($this->db->optimize($tables)) {
            return json(['error'=>0, 'msg'=>'数据表优化成功！']);
        } else {
            return json(['error'=>1, 'msg'=>'数据表优化出错请重试！']);
        }
    }

    // 修复
    public function repair() {
        $tables = Request::param('id');
        if (empty($tables)) {
            return json(['error'=>1,'msg'=>'请选择要修复的表！']);
        }
        $tables = explode(',',$tables);
        if ($this->db->repair($tables)) {
            return json(['error'=>0, 'msg'=>'数据表修复成功！']);
        } else {
            return json(['error'=>1, 'msg'=>'数据表修复出错请重试！']);
        }
    }

    // 还原
    public function restore()
    {
        $list = $this->db->fileList();
        $total = 0;
        $listNew = [];
        foreach ($list as $k => $v) {
            $total += substr($v['size'], 0, strlen($v['size']) - 2);
            $listNew[] = $list[$k];
        }
        $list = $listNew;
        array_multisort(array_column($list, 'time'), SORT_DESC, $list);
        // 设置主键
        $pk = 'time';
        // 字段信息
        $columns = [
            ['time', '编号',],
            ['name', '文件名称'],
            ['part', '分卷'],
            ['size', '文件大小'],
            ['compress', '分隔符'],
            ['addtime', '创建时间', '', '', '', '', 'true'],
        ];

        // 提示信息
        $pageTips = '备份文件列表中共有 ' . count($list) . ' 个文件，共计 ' . formatBytes($total * 1024);

        // 搜索
        if (Request::param('_list') == 1) {
            // 排序规则
            $orderByColumn = Request::param('orderByColumn') ?? $pk;
            $isAsc = Request::param('isAsc') ?? 'desc';
            $isAsc = $isAsc == 'desc' ? SORT_DESC : SORT_ASC;
            // 排序处理
            $date = array_column($list, $orderByColumn);
            array_multisort($date, $isAsc, $list);
            if (Request::param('name')) {
                foreach ($list as $k => $v) {
                    if (strpos($v['name'], Request::param('name')) == false) {
                        unset($list[$k]);
                    }
                }
            }
            // 渲染输出
            return [
                'total' => count($list),
                'per_page' => 1000,
                'current_page' => 1,
                'last_page' => 1,
                'data' => $list,
            ];
        }
        // 构建页面
        return $this->tableBuilder
            ->setUniqueId($pk)// 设置主键
            ->addColumns($columns)// 添加列表字段数据
            ->setPageTips($pageTips, 'warning')// 提示信息
            ->setPagination('false')// 关闭分页显示
            ->addColumn('right_button', '操作', 'btn')
            ->addTopButtons('del')
            ->addRightButton('info', [
                'title' => '恢复',
                'icon'  => 'fa fa-exclamation-triangle',
                'class' => 'btn btn-flat btn-warning btn-xs confirm',
                'href'  =>  (string)url('import', ['id' => '__time__'])
            ]) // 添加额外按钮
            ->addRightButton('info', [
                'title' => '下载',
                'icon'  => 'fa fa-download',
                'target'=> '_blank',
                'class' => 'btn btn-flat btn-success btn-xs confirm',
                'href'  => (string)url('downFile', ['id' => '__time__'])
            ]) // 添加额外按钮
            ->addRightButton('delete')
            ->fetch();
    }

    // 执行还原数据库操作
    public function import($id)
    {
        $list = $this->db->getFile('timeverif', $id);
        $this->db->setFile($list)->import(1);
        return json(['error' => 0, 'msg' => '还原成功！']);
    }

    // 下载
    public function downFile(int $id)
    {
        $this->db->downloadFile($id);
    }

    // 删除sql文件
    public function delete(string $id)
    {
        if (Request::isPost()) {
            if (strpos($id, ',') !== false) {
                $idArr = explode(',', $id);
                foreach ($idArr as $k => $v) {
                    $this->db->delFile($v);
                }
                return json(['error' => 0, 'msg' => "删除成功！"]);
            }
            if ($this->db->delFile($id)) {
                return json(['error' => 0, 'msg' => "删除成功！"]);
            } else {
                return json(['error' => 1, 'msg' => "备份文件删除失败，请检查文件权限！"]);
            }
        }
    }
}