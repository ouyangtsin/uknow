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

namespace plugins\third\controller;
use app\common\controller\Backend;
use think\facade\Request;

/**
 * 第三方登录管理
 *
 * @icon fa fa-circle-o
 */
class Third extends Backend
{
    protected $model = null;

    public function initialize()
    {
        parent::initialize();
        $this->model = new \plugins\third\model\Third();
    }

    /**
     * 查看
     */
    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['user_name','用户','link',(string)url('member/index//index',['uid'=>'__uid__'])],
            ['platform','绑定平台'],
            ['openid','第三方唯一ID'],
            ['open_username','第三方会员昵称'],
            ['login_time', '登录时间','datetime'],
        ];
        $search = [

        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            return $this->model
                ->with(['user'])
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
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }
}
