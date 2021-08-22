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

namespace app\common\model;

use think\Model;

class UsersPowerGroup extends Model
{
	protected $name = 'users_power_group';
	//获取全部用户组信息
	public static function getGroupAll()
    {
		$where = ['status'=>1];
		return db('users_power_group')->where($where)->select()->toArray();
	}
}