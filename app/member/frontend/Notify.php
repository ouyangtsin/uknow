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
use app\ask\model\Notify as NotifyModel;
use think\App;

/**
 * 通知模块控制器
 * Class Notify
 * @package app\ask\controller
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
		$page = $this->request->param('page',1);
		$type = $this->request->param('type','');
		$header = $this->request->param('header',0);
		$read_status = $header ? 2 : 0;
		$data = NotifyModel::getNotifyList($this->user_id,$page,$header ? 5 : 10,$read_status,$type,'uk-index-main');
		$this->assign($data);
        $notifyType = config('notify');
        $this->assign([
            'type'=>$type,
            'notify'=>$notifyType
        ]);

        if($header)
        {
            return $this->fetch('header_notify');
        }
		return $this->fetch();
	}

    /**
     * 删除通知
     */
	public function delete()
    {
        $id = $this->request->post('id');
        if(NotifyModel::removeNotify($id,$this->user_id))
        {
            $this->result([],1);
        }
        $this->result([],0);
    }

    /**
     * 标记已读
     */
    public function read()
    {
        $id = $this->request->post('id');
        if(NotifyModel::setNotifyRead($id,$this->user_id))
        {
            $this->result([],1);
        }
        $this->result([],0);
    }


    public function read_all()
    {
        if(NotifyModel::setNotifyReadAll($this->user_id))
        {
            $this->result([],1);
        }
        $this->result([],0);
    }
}