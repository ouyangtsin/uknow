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
use app\common\library\helper\IpHelper;
use app\common\library\helper\MailHelper;
use app\common\model\Users;
use app\common\model\UsersActive;
use app\ask\model\Topic as TopicModel;

class Account extends Frontend
{
	/**
	 * 用户登录
	 */
	public function login()
	{
		if($this->user_id)
		{
			$this->redirect('/');
		}

		if($this->request->isPost())
		{
			$user_name = $this->request->post('user_name');
			$password = $this->request->post('password');
            $return_url = base64_decode($this->request->post('url'));
            if(!$user_name)
            {
                $this->error('请输入用户名');
            }

			if(!$password)
            {
                $this->error('请输入用户密码');
            }

			if(!Users::getLogin($user_name,$password))
			{
				$this->error(Users::getError());
			}

			$this->success('登录成功',$return_url);
		}
		$this->assign('return_url',session('return_url'));
		$this->layout=false;
		$this->view->engine()->layout(false);
		return $this->fetch();
	}

	/**
	 * 用户注册
	 */
	public function register()
	{
	    if($this->settings['register_type']=='close')
        {
            $this->error('网站关闭注册');
        }

		if($this->user_id)
		{
            $this->redirect('/');
		}

		if($this->request->isPost())
		{
            $data = $this->request->post();
			$user_name =$data['user_name'];
			$password = $data['password'];
			$re_password = $data['re_password'];
            $return_url = base64_decode($this->request->post('url'));
			unset($data['user_name'],$data['password'],$data['re_password'],$data['token']);

            /*if (!$this->request->checkToken()) {
                $this->error('请勿重复提交');
            }*/

			if($password!=$re_password)
			{
				$this->error('两次输入的密码不一致');
			}

			if(!$data['email'] || !MailHelper::isEmail($data['email']))
            {
                $this->error('请填写正确的邮箱地址');
            }

			//验证用户名长度
            if(mb_strlen($user_name) < get_setting('username_min_length') || mb_strlen($user_name) > get_setting('username_max_length'))
            {
                $this->error('请输入'.get_setting('username_min_length').' - '.get_setting('username_max_length').' 位的用户名');
            }

            //验证密码长度
			if(mb_strlen($password) < get_setting('password_min_length') || mb_strlen($password) > get_setting('password_max_length'))
            {
                $this->error('请输入'.get_setting('password_min_length').' - '.get_setting('password_max_length').' 位的密码');
            }

			//验证密码
			if(!empty(get_setting('password_type')))
            {
                if(in_array('number',get_setting('password_type')) && !preg_match("/[0-9]+/",$password))
                {
                    $this->error('密码需包含数字');
                }

                if(in_array('special',get_setting('password_type')) && !preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/",$password))
                {
                    $this->error('密码需包含特殊字符');
                }

                if(in_array('letter',get_setting('password_type')) && !preg_match("/[a-zA-Z]+/",$password))
                {
                    $this->error('密码需包含大小写字母');
                }
            }

            if (in_array('mobile',$this->settings['register_valid_type']) && get_plugins_info('sms')['status'] && !isset($data['sms_code']))
            {
                $this->error('请输入手机验证码');
            }

			if(isset($data['sms_code']) && cache('sms_'.$data['mobile'])!=$data['sms_code'])
            {
                $this->error('手机验证码不正确');
            }


            if($this->settings['register_type']=='invite')
            {
                if(!$data['invite_code']){
                    $this->error('本站开启了邀请注册，请输入您的邀请码');
                }
                $invite_code_info = db('invite')->where(['invite_code'=>$data['invite_code']])->find();
                if($invite_code_info['active_status']==1 || $invite_code_info['active_expire']<time())
                {
                    $this->error('邀请码已使用或已过期');
                }
            }

			if(!$uid=Users::registerUser($user_name,$password,$data))
			{
				$this->error(Users::getError());
			}

            if($this->settings['register_type']=='invite')
            {
                //更新激活码状态
                db('invite')->where(['invite_code'=>$data['invite_code']])->update(['active_status'=>1]);
            }

			$user_info = Users::getLogin($user_name,$password);

			//发送注册成功欢迎语
            \app\ask\model\Notify::send(0,$uid,'TYPE_SYSTEM_NOTIFY','亲爱的用户您好,欢迎注册'.get_setting('site_name'),$uid,array(
                'message'=>str_replace(['{username}', '{time}', '{sitename}'],[$user_info['user_name'], date('Y-m-d H:i:s',time()), get_setting('site_name')],get_setting('user_register_welcome'))
            ));

			$url = !$data['email'] ? $return_url : url('member/account/send_valid_mail');
			$this->success('注册成功',$url);
		}
        $return_url = $_SERVER['HTTP_REFERER'];
        $this->assign('return_url',base64_encode($return_url));
        $this->assign('agreement',nl2br(get_setting("register_agreement")));
		$this->layout=false;

		$this->view->engine()->layout(false);
		return $this->fetch();
	}

    /**
     * 找回密码
     * @return mixed
     */
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
        session('admin_user_info',null);
		$this->success('退出成功',url('/'));
	}

    /**
     * 保存用户资料
     */
	public function save_profile()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }
            unset($postData['uid']);
            $postData['birthday'] = strtotime($postData['birthday']);
            if(Users::updateUserFiled($this->user_id,$postData))
            {
                $this->success('资料更新成功','setting/profile');
            }
            $this->error('资料更新失败');
        }
    }

    /**
     * 修改邮箱
     */
    public function modify_email()
    {
        if(!$this->user_id)
        {
            $this->redirect('/');
        }
        return $this->fetch();
    }

    /**
     * 修改手机号
     */
    public function modify_mobile()
    {
        if(!$this->user_id)
        {
            $this->redirect('/');
        }

        $step = $this->request->param('step',0);
        $this->assign('step',$step);

        if($this->request->isPost())
        {
            $postData = $this->request->post();
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }
            $cache_code = cache('sms_'.$this->user_info['mobile']);

            switch ($step)
            {
                case 0:
                    if($this->user_info['mobile'])
                    {
                        if($cache_code == $postData['code'])
                        {
                            $this->success('验证成功','modify_mobile?step=1&_ajax_open=1');
                        }
                        $this->error('短信验证码不正确');
                    }else{
                        $password = db('users')->where('uid',$this->user_id)->value('password');
                        if(!password_verify($postData['password'],$password))
                        {
                            $this->error('密码不正确');
                        }
                        $this->success('验证成功','modify_mobile?step=1&_ajax_open=1');
                    }
                    break;
                case 1:
                    if(Users::checkUserExist($postData['mobile'],'uid') && $postData['mobile']!=$this->user_info['mobile'])
                    {
                        $this->error('该手机号已存在');
                    }
                    Users::updateUserFiled($this->user_id,['mobile'=>$postData['mobile']]);
                    $this->success('修改成功','setting/profile');
                    break;
            }
        }
        return $this->fetch();
    }

    /**
     * 验证手机号
     */
    public function check_mobile()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $uid = Users::checkUserExist($postData['mobile'],'uid');
            if($uid && $uid!=$this->user_id)
            {
                $this->error('该手机号已存在');
            }

            $this->success('手机号正确');
        }
    }

    /*发送验证邮件*/
    public function send_valid_mail()
    {
        if(!$this->user_id)
        {
            $this->error('您访问的页面不存在');
        }

        $sendResult = UsersActive::newValidEmail($this->user_id,$this->user_info['email']);
        if(!$sendResult['code'])
        {
            $this->error($sendResult['message']);
        }
        $this->success('验证邮件发送成功,请登录邮箱 '.$this->user_info['email'].' 进行验证');
    }

    /**
     * 验证邮箱激活码
     */
    public function valid_email_verify()
    {
        $active_code_hash = $this->request->param('active_code_hash');
        if(!$active_code_hash)
        {
            $this->error('激活链接不正确,请重新发送验证链接','/');
        }

        $codeInfo = db('users_active')->where(['active_code'=>$active_code_hash])->find();
        if(!$codeInfo)
        {
            $this->error('激活链接不正确,请重新发送验证链接','/');
        }

        $valid_email_code = cache('valid_email_code_'.$codeInfo['uid']);

        if($valid_email_code!=$active_code_hash)
        {
            $this->error('激活链接不正确');
        }

        if($codeInfo['expire_time']<time())
        {
            $this->error('验证链接已过期，请登录后重新发送验证邮件');
        }

        //更新邮箱验证状态
        if(Users::updateUserFiled($codeInfo['uid'],['is_valid_email'=>1]))
        {
            //更新用户组
            db('auth_group_access')->where(['uid'=>$codeInfo['uid']])->update(['group_id'=>3]);

            //更新用户激活时间
            db('users_active')->where(['id'=>$codeInfo['id']])->update([
                'active_time'=>time(),
                'active_ip'=>IpHelper::getRealIp()
            ]);

            cache('valid_email_code_'.$codeInfo['uid'],null);
        }

        $this->success('验证成功','login');
    }

    /**
     * 首次登陆
     */
    public function welcome_first_login()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $step = $postData['step'];
            unset($postData['step']);
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }
            switch ($step)
            {
                case 1:
                    unset($postData['uid']);
                    $postData['birthday'] = strtotime($postData['birthday']);
                    $postData['is_first_login'] = 0;
                    Users::updateUserFiled($this->user_id,$postData);
                    $this->result(['url'=>(string)url('welcome_first_login',['step'=>2,'_ajax_open'=>1],true,true)],1,'资料更新成功');
                    break;

                case 2:
                    $this->result(['url'=>(string)url('welcome_first_login',['step'=>3,'_ajax_open'=>1],true,true)],1);
                    break;
            }
        }
        $step = $this->request->param('step',1);

        if($step==2)
        {
            $topic_list = TopicModel::getHotTopics($this->user_id,[],['focus'=>'desc','discuss'=>'desc'],4,1);
            $this->assign($topic_list);
        }

        if($step==3)
        {
            $list = Users::getHotUsers($this->user_id,[],['power'=>'desc','fans_count'=>'desc'],4,1);
            $this->assign($list);
        }

        $this->assign('step',$step);
        return $this->fetch();
    }

    /**
     * 修改密码
     */
    public function modify_password()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }

            $cache_code = cache('sms_'.$this->user_info['mobile']);

            if($this->user_info['mobile'])
            {
                if($cache_code == $postData['code'])
                {
                    $this->success('验证成功',url('member/account/modify_password?step=1&_ajax_open=1'));
                }
                $this->error('短信验证码不正确');
            }else{
                $password = db('users')->where('uid',$this->user_id)->value('password');
                if(!password_verify($postData['password'],$password))
                {
                    $this->error('密码不正确');
                }
                $this->success('验证成功',url('member/account/modify_password?step=1&_ajax_open=1'));
            }
        }

        return $this->fetch();
    }

    /**
     * 修改交易密码
     */
    public function modify_deal_password()
    {
        return $this->fetch();
    }
}