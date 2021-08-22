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

namespace app\ask\wap;
use app\common\controller\Frontend;
use app\ask\model\Notify as NotifyModel;
use think\App;

/**
 * 通知模块控制器
 * Class Notify
 * @package app\ask\wap
 */
class Notify extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new NotifyModel();
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
	}

	/**
	 * 通知列表
	 * @return mixed
	 */
	public function index()
	{
		return $this->fetch();
	}

	/*Ajax通知列表*/
	public function notify_list()
	{
		$page = $this->request->param('page',1);
		$data = NotifyModel::getNotifyList($this->user_id,$page);
		$this->assign($data);
		return $this->fetch();
	}
}