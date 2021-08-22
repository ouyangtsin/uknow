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

namespace app\member\frontend;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\common\logic\common\FocusLogic;
use app\common\model\Users;
use think\App;

/**
 * 创作者中心
 * Class Creator
 * @package app\member\frontend
 */
class Creator extends Frontend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new Users();
    }

    public function index()
    {
        return $this->fetch();
    }
}