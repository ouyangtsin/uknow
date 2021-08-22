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

class People extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new Users();
		if(!$this->user_id)
		{
			$this->redirect('wap/account/login');
		}
	}

	public function index()
	{
		$uid = $this->request->get('uid/i');
		$uid = $uid ? $uid : $this->user_id;
		$user =Users::getUserInfo($uid);

		if($user['status']!=1 && $this->user_id)
		{
			$this->error('当前用户被禁用或仍在审核中');
		}
		$this->assign('type',$this->request->param('type','question'));
		$this->assign('user',$user);
		if(!$this->request->get('uid/i'))
		{
			return $this->fetch('explore');
		}
		return $this->fetch();
	}

	/**
	 * 用户列表
	 */
	public function lists()
	{
		$sort = $this->request->param('sort','default');
		$this->assign('sort',$sort);
		return $this->fetch();
	}
}