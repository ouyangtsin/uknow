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

namespace app\ask\frontend;

use app\common\controller\Frontend;

/**
 * 活动培训控制器
 * Class Activity
 * @package app\ask\controller
 */
class Activity extends Frontend
{
    //活动首页
    public function index()
    {
        return $this->fetch();
    }

    //发起活动
    public function publish()
    {
        return $this->fetch();
    }
}