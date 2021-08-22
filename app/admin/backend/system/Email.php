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
use think\facade\Request;

/**
 * 邮箱发送记录
 * Class Email
 * @package app\admin\controller\system
 */
class Email extends Backend
{
    public function index()
    {
        $columns = [
            ['id','编号'],
            ['send_to', '发送邮箱'],
            ['subject','主题'],
            ['status', '状态', 'radio', '0',['0' => '发送失败','1' => '发送成功']],
            ['create_time', '创建时间','datetime'],
        ];

        $search = [
            ['text', 'send_to', '发送邮箱', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            return db('email_log')
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
            ->addRightButtons(
                [
                    'preview' => [
                        'title'       => '编辑',
                        'icon'        => 'fa fa-edit',
                        'class'       => 'btn btn-success btn-xs',
                        'href'        => '',
                        'target'      => '',
                        'url'=>(string)url('preview', ['id' => '__id__'])
                    ],
                    'delete'
                ])
            ->addTopButtons(['delete'])
            ->fetch();
    }

    public function preview($id=0)
    {

    }
}