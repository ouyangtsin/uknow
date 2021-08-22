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


namespace app\admin\model;

use app\common\library\builder\MakeBuilder;
use think\facade\Request;
use think\Model;

/**
 * 权限节点模型
 * Class AuthRule
 * @package app\admin\model
 */
class AuthGroup extends Model {
	protected $name = 'auth_group';

    public static function getList($where = array(), $order = ['sort', 'id' => 'desc'])
    {
        $list = self::where($where)
            ->order($order)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' => 15,
            ])
            ->toArray();
        return MakeBuilder::getInstance()->changeTableData($list, 'auth_group');
    }
}