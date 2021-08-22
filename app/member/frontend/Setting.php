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
use app\common\library\builder\FormBuilder;
use app\common\model\Users;
use app\common\model\Verify;
use think\App;

/**
 * 公用设置模块
 * Class Setting
 * @package app\ask\controller
 */
class Setting extends Frontend
{
    protected $needLogin=['profile','notify','inbox','security','openid','nav'];
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new Users();
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
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
	    if($this->request->isPost())
        {
            $postData = $this->request->post();
            $setting_id = db('users_setting')->where('uid',$this->user_id)->value('id');
            if($setting_id)
            {
                $res = db('users_setting')->where(['id'=>$setting_id])->update([
                    'email_setting'=>implode(',',$postData['email_setting']),
                    'inbox_setting'=>$postData['inbox_setting'],
                    'notify_setting'=>implode(',',$postData['notify_setting']),
                    'update_time'=>time()
                ]);
            }else{
                $res = db('users_setting')->insert([
                    'uid'=>$this->user_id,
                    'email_setting'=>implode(',',$postData['email_setting']),
                    'inbox_setting'=>$postData['inbox_setting'],
                    'notify_setting'=>implode(',',$postData['notify_setting']),
                    'create_time'=>time()
                ]);
            }

            if($res)
            {
                $this->success('保存成功');
            }
            $this->error('保存失败');
        }

        $user_setting = db('users_setting')->where('uid',$this->user_id)->find();
	    if($user_setting)
        {
            $user_setting['email_setting'] = explode(',',$user_setting['email_setting']);
            $user_setting['notify_setting'] = explode(',',$user_setting['notify_setting']);
        }

	    $this->assign([
	        'notify_setting'=> config('notify'),
            'user_setting'=>$user_setting,
            'email_setting'=> config('email')
        ]);
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

    /**
     * 账号认证
     */
	public function verified()
    {
        if($this->request->isPost()) {
            $params = $this->request->post();
            $type = $params['type'];
            $id = $params['id'];
            unset($params['id'],$params['type']);
            if(!$id)
            {
                db('users_verify')->insert(['create_time'=>time(),'type'=>$type,'data'=>json_encode($params),'status'=>0,'uid'=>intval($this->user_id),'reason'=>'']);
            }else{
                db('users_verify')->where('id',$id)->update(['type'=>$type,'data'=>json_encode($params),'status'=>0]);
            }

            $this->success('提交成功');
        }
        $this->assign([
            'info'=> db('users_verify')->where(['uid'=>intval($this->user_id)])->find(),
            'verify_type'=>$this->settings['user_verify_type']
        ]);
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