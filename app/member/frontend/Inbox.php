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
use app\common\model\Inbox as InboxModel;
use app\common\model\Users;
use think\App;

class Inbox extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
		$this->model = new InboxModel();
	}

	public function index()
	{
	    $page = $this->request->param('page',1);
		if($this->request->param('header',0))
        {
            $dialogList = InboxModel::getDialogListByUid($this->user_id,'(recipient_unread=1 OR sender_unread=1) AND ',1,5);
            $this->assign($dialogList);
            return $this->fetch('header_inbox');
        }

        $dialogList = InboxModel::getDialogListByUid($this->user_id,'',$page);
        $this->assign($dialogList);
		return $this->fetch();
	}

	public function detail()
	{
		$id = $this->request->post('id',0);
		$uid = $this->request->post('uid');

		$user = Users::getUserInfo($uid);
		$this->assign('user',$user);

		$list = InboxModel::getMessageByDialogId($id,$this->user_id);
		$this->assign('list',$list);

		return $this->fetch();
	}

    /**
     * 发送私信
     */
	public function send()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			if(!InboxModel::sendMessage($this->user_id, $postData['recipient_uid'], $postData['message']))
			{
				$this->error(InboxModel::getError());
			}
			$this->success('私信发送成功');
		}
	}
}