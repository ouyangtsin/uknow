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


namespace app\admin\backend\plugin;

use app\common\controller\Backend;
use app\common\library\helper\UpgradeHelper;
use think\App;

class Upgrade extends Backend
{
    protected $upgradeHelper;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->upgradeHelper = UpgradeHelper::instance();
    }

    public function index()
    {
        $this->assign([
            'check_info'=>$this->upgradeHelper->checkVersion(),
        ]);
        return $this->fetch();
    }
}