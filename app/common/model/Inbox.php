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

namespace app\common\model;

class Inbox extends BaseModel
{
	protected $name ='inbox';

    /**
     * 根据当前用户获取对话列表
     * @param $uid
     * @param string $where
     * @param int $page
     * @param int $per_page
     * @param string $pjax
     * @return array|false
     */
	public static function getDialogListByUid($uid, string $where='', $page=1, $per_page=10, string $pjax='uk-index-main')
	{
		$list = db('inbox_dialog')
			->whereRaw($where.'(sender_uid = ' . intval($uid) . ' AND sender_count > 0) OR (recipient_uid = ' . intval($uid) . ' AND recipient_count > 0)')
			->order('update_time','DESC')
			->paginate([
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param(),
                'pjax'=>$pjax
			]);
		$total = $list->toArray()['last_page'];
		$pageVar = $list->render();
		$list = $list->all();
		$dialogUidArr = $dialogIds =  array();

		foreach ($list as $key => $val)
		{
			$dialogIds[] = $val['id'];
			$dialogUidArr[] = $uid == $val['recipient_uid'] ? $val['sender_uid'] : $val['recipient_uid'];
		}

		if (!$dialogUidArr || !$dialogIds)
		{
			return false;
		}

		$userInfos =Users::getUserInfoByIds($dialogUidArr);
		$lastMessageInfo = self::getLastDialogMessage($dialogIds);
		$data = array();
		foreach ($list as $key => $value)
		{
			if ($value['recipient_uid'] == $uid AND $value['recipient_count']) // 当前处于接收用户
			{
				$data[$key]['user'] = $userInfos[$value['sender_uid']];
				$data[$key]['unread'] = $value['recipient_unread'];
				$data[$key]['count'] = $value['recipient_count'];
				$data[$key]['uid'] = $value['sender_uid'];
			}
			else if ($value['sender_uid'] == $uid AND $value['sender_count']) // 当前处于发送用户
			{
				$data[$key]['user'] = $userInfos[$value['recipient_uid']];
				$data[$key]['unread'] = $value['sender_unread'];
				$data[$key]['count'] = $value['sender_count'];
				$data[$key]['uid'] = $value['recipient_uid'];
			}
			$data[$key]['last_message'] = $lastMessageInfo[$value['id']]['message'];
            $data[$key]['last_message_uid'] = $lastMessageInfo[$value['id']]['uid'];
			$data[$key]['update_time'] = $value['update_time'];
			$data[$key]['id'] = $value['id'];
		}
		return ['list'=>$data,'page'=>$pageVar,'total'=>$total];
	}

    /**
     * 获取对话最后信息
     * @param $dialog_ids
     * @return array|false
     */
	public static function getLastDialogMessage($dialog_ids)
	{
		if (!is_array($dialog_ids))
		{
			return false;
		}
		$last_message = array();
		foreach ($dialog_ids as $dialog_id)
		{
			$dialog_message = db('inbox')->where(['dialog_id'=>$dialog_id])->order('id','DESC')->field('message,uid')->find();
            $dialog_message['message']=str_cut($dialog_message['message'], 0, 60, 'UTF-8', '...');
            $last_message[$dialog_id] = $dialog_message;
		}
		return $last_message;
	}

    /**
     * @param $dialog_id
     * @return mixed
     */
	public static function getDialogById($dialog_id)
	{
		return  db('inbox_dialog')->where(['id'=> (int)$dialog_id])->find();
	}

    /**
     * 获取对话消息
     * @param $dialog_id
     * @param $uid
     * @param int $page
     * @param int $per_page
     * @return array|false
     */
	public static function getMessageByDialogId($dialog_id,$uid,$page=1,$per_page=5)
	{
		if (!$dialog = self::getDialogById($dialog_id))
		{
			return false;
		}

		$inbox =db('inbox')
            ->where(['dialog_id'=>intval($dialog_id)])
            ->order('id','DESC')
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ]);
        $pageVar = $inbox->render();
        $inbox = $inbox->toArray();
        $total = $inbox['last_page'];
		if (!$inbox)
		{
			return false;
		}

		$message = array();

		foreach ($inbox['data'] AS $key => $val)
		{
			$message[$val['id']] = $val;
		}

		foreach ($message as $key => $val)
		{
            $recipient_user = $val['uid'] == $dialog['sender_uid'] ? Users::getUserInfo($dialog['sender_uid']) : Users::getUserInfo($dialog['recipient_uid']);
			if ($dialog['sender_uid'] == $uid AND $val['sender_remove'])
			{
				unset($message[$key]);
			}
			else if ($dialog['sender_uid'] != $uid AND $val['recipient_remove'])
			{
				unset($message[$key]);
			}
			else
			{
				$message[$key]['user'] = $recipient_user;
			}
		}

        return ['list'=>$message,'page'=>$pageVar,'total'=>$total];
	}

    /**
     * @param $sender_uid
     * @param $recipient_uid
     * @return mixed
     */
	public static function getDialogByUser($sender_uid, $recipient_uid)
	{
		return db('inbox_dialog')
			->whereRaw("(`sender_uid` = " . (int)$sender_uid . " AND `recipient_uid` = " . (int)$recipient_uid . ") OR (`recipient_uid` = " . (int)$sender_uid . " AND `sender_uid` = " . (int)$recipient_uid . ")")
			->find();
	}

    /**
     * 发送私信
     * @param $sender_uid
     * @param $recipient_uid
     * @param $message
     * @return false
     */
	public static function sendMessage($sender_uid, $recipient_uid, $message)
	{
		if (!$sender_uid OR !$recipient_uid OR !$message)
		{
			return false;
		}

		if(is_string($recipient_uid))
        {
            $recipient_uid = db('users')->where('user_name',$recipient_uid)->value('uid');
        }

		if (trim($message) == '')
		{
			self::setError('请输入私信内容');
		}

		if (!$recipient_user = Users::getUserInfo($recipient_uid))
		{
			self::setError('接收私信的用户不存在');
		}

		if ($recipient_user['uid'] == $sender_uid)
		{
			self::setError('不能给自己发私信');
		}

		if (!$inbox_dialog = self::getDialogByUser($sender_uid, $recipient_uid))
		{
			$inbox_dialog_id = db('inbox_dialog')->insertGetId(array(
				'sender_uid' => $sender_uid,
				'sender_unread' => 0,
				'recipient_uid' => $recipient_uid,
				'recipient_unread' => 0,
				'create_time' => time(),
				'update_time' => time(),
				'sender_count' => 0,
				'recipient_count' => 0
			));
		}
		else
		{
			$inbox_dialog_id = $inbox_dialog['id'];
		}

		$message_id = db('inbox')->insertGetId(array(
			'dialog_id' => $inbox_dialog_id,
			'message' => htmlspecialchars($message),
			'send_time' => time(),
			'uid' => $sender_uid
		));

		//更新私信对话数量
		self::updateDialogCount($inbox_dialog_id, $sender_uid);
        $recipient_unread = db('inbox_dialog')->whereRaw('recipient_uid = ' . intval($recipient_uid))->sum('recipient_unread');
        $sender_unread = db('inbox_dialog')->where('sender_uid = ' . intval($recipient_uid))->sum('sender_unread');
		Users::updateUserFiled($recipient_uid,['inbox_unread'=>$recipient_unread+$sender_unread]);
		/*if ($user_info = Users::getUserInfo($sender_uid))
		{
			//发送邮件
			MailHelper::sendEmail($user_info['email'],'有用户在'.get_setting('site_name').'给你发了一条私信','');
		}*/
		return $message_id;
	}

    /**
     * 更新对话数量
     * @param $dialog_id
     * @param $uid
     * @return false
     */
	public static function updateDialogCount($dialog_id, $uid)
    {
		if (! $inbox_dialog = self::getDialogById($dialog_id))
		{
			return false;
		}
		db('inbox_dialog')->where(['id'=>intval($dialog_id)])->update(
		    [
                'sender_count' =>db('inbox')->whereRaw( 'uid IN(' . $inbox_dialog['sender_uid'] .','.$inbox_dialog['recipient_uid'].') AND sender_remove = 0 AND dialog_id = ' . intval($dialog_id))->count(),
                'recipient_count' => db('inbox')->whereRaw( 'uid IN(' . $inbox_dialog['sender_uid'] .','.$inbox_dialog['recipient_uid'].') AND recipient_remove = 0 AND dialog_id = ' . intval($dialog_id))->count(),
                'update_time' => time()
            ]);
		$updateField = $inbox_dialog['sender_uid'] == $uid ? 'recipient_unread' : 'sender_unread';
		return db('inbox_dialog')->where(['id'=>intval($dialog_id)])->inc($updateField)->update();
	}

    /**
     * 更新消息状态
     * @param $dialog_id
     * @param $uid
     * @return false
     */
	public static function updateRead($dialog_id,$uid)
    {
        if (!$dialog = self::getDialogById($dialog_id))
        {
            return false;
        }
        $update_data =  $uid == $dialog['sender_uid'] ? ['sender_unread'=>0] : ['recipient_unread'=>0];
        db('inbox_dialog')->where(['id'=>$dialog['id']])->update($update_data);
        return Users::updateInboxUnread($uid);
    }
}