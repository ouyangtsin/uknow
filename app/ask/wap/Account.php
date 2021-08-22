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

class Account extends Frontend
{
	/**
	 * 用户登录
	 */
	public function login()
	{
		if($this->user_id)
		{
			$this->error('您已登录','/');
		}
		if($this->request->isPost())
		{
			if(!$this->request->checkToken())
			{
				$this->error('请不要重复提交');
			}

			$user_name = $this->request->post('user_name');
			$password = $this->request->post('password');

			if(!$user_info=Users::getLogin($user_name,$password))
			{
				$this->error(Users::getError());
			}

			session('login_user_info',$user_info);
			$this->success('登录成功','/');
		}
		return $this->fetch();
	}

	/**
	 * 用户注册
	 */
	public function register()
	{
		if($this->user_id)
		{
			$this->error('您已登录','/');
		}
		if($this->request->isPost())
		{
			$user_name = $this->request->post('user_name');
			$password = $this->request->post('password');
			$re_password = $this->request->post('re_password');

			if($password!=$re_password)
			{
				$this->error('两次输入的密码不一致');
			}

			if(!Users::registerUser($user_name,$password))
			{
				$this->error(Users::getError());
			}
			$this->success('注册成功','/');
		}
		$this->layout=false;
		$this->view->engine()->layout(false);
		return $this->fetch();
	}

	public function find_password()
	{
		return $this->fetch();
	}

	/**
	 * 退出登陆
	 */
	public function logout()
	{
		session('login_uid',null);
		session('login_user_info',null);
		$this->success('退出成功',url('index/index'));
	}
}