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
use app\common\model\Users;
use think\App;

class Setting extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new Users();
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
	}

	public function index()
	{
		return $this->fetch();
	}

	/**
	 * 个人资料设置
	 */
	public function profile()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			$result = Users::updateUserFiled($this->user_id,$postData);
			if(!$result)
			{
				$this->error('基本信息更新失败');
			}
			$this->success('更新成功');
		}
		return $this->fetch();
	}

	/**
	 * 通知设置
	 */
	public function notify()
	{
		return $this->fetch();
	}

	/**
	 * 私信设置
	 */
	public function inbox()
	{
		return $this->fetch();
	}

	/**
	 * 安全设置
	 */
	public function security()
	{
		return $this->fetch();
	}

	/**
	 * 账号绑定
	 */
	public function openid()
	{
		return $this->fetch();
	}

	//设置导航
	private function nav()
	{
		$this->layout=false;
		$this->view->engine()->layout(false);
		return $this->fetch();
	}
}