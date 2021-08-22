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


namespace app\ask\model;

use app\common\model\BaseModel;
use app\common\model\Users;
use think\facade\Db;

/**
 * 系统通知模型
 * Class Notify
 * @package app\ask\model
 */
class Notify extends BaseModel
{
	protected $name = 'notify';
	protected static $notifyType=[];
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        self::$notifyType = config('notify');
    }

    /**
     * 发送通知
     * @param int $sender_uid 发送用户id
     * @param int $recipient_uid 接受用户id
     * @param int $action_type 通知类型
     * @param string $subject 通知标题
     * @param int $item_id 通知内容id
     * @param array $content 通知详细数据
     * @return bool|int|string
     */
	public static function send($sender_uid=0, $recipient_uid=0, $action_type='', $subject='', $item_id = 0, $content = array())
	{
		if (!$recipient_uid || !$action_type) return false;

		$insertData = array(
			'sender_uid' => $sender_uid,
			'recipient_uid' => $recipient_uid,
			'action_type' => $action_type,
			'subject'=>$subject,
			'content'=>json_encode($content),
			'item_id' => $item_id,
			'create_time' => time(),
			'read_flag' => 0
		);

		$notification_id = db('notify')->insertGetId($insertData);
		if(!$notification_id) {
            return false;
        }
        Users::updateNotifyUnread($recipient_uid);
		return $notification_id;
	}

    /**
     * 获得通知列表
     * @param $recipient_uid
     * @param int $page
     * @param int $per_page
     * @param null $read_status
     * @param null $action_type
     * @return array|false
     */
	public static function getNotifyList($recipient_uid,$page=1,$per_page=10,$read_status=null,$action_type=null,$pjax='')
	{
		if(!$recipient_uid) {
            return false;
        }

		$map = array();
		$map[] = ['recipient_uid','=',$recipient_uid];

		if($action_type)
		{
			$map[] = ['action_type','=',$action_type];
		}

		if($read_status)
		{
            $read_status = $read_status==2 ? 0 : $read_status;
			$map[] = ['read_flag','=',$read_status];
		}
		$result = db('notify')->where($map)->order('create_time','DESC')->paginate([
			'list_rows'=> $per_page,
			'page' => $page,
			'query'=>request()->param(),
            'pjax'=>$pjax
		]);
		$pageVar = $result->render();
		$list = $result->all();
		$senderUidArr = array_column($list,'sender_uid');
		$recipientUidArr = array_column($list,'recipient_uid');
		$sender_user_list = Users::getUserInfoByIds($senderUidArr);
		$recipient_user_list = Users::getUserInfoByIds($recipientUidArr);

		foreach ($list as $key=>&$val)
		{
		    $content = json_decode($val['content'],true);
			$list[$key]['content'] = $content;
			$list[$key]['sender_user'] = $val['sender_uid']?$sender_user_list[$val['sender_uid']]:'系统';
			$list[$key]['recipient_user'] = $recipient_user_list[$val['recipient_uid']];
            $list[$key]['message'] = isset($content['message']) ? $content['message'] : '';
			// if(isset($list[$key]['content']['item_type']))
   //          {
   //              switch ($list[$key]['content']['item_type'])
   //              {
   //                  case 'question':
   //                      $questionInfo = Question::getQuestionInfo($list[$key]['item_id']);
   //                      $list[$key]['message'] = $questionInfo ? '<a href="'.url('question/detail',['id'=>$questionInfo['id']]).'" >'.$questionInfo['title'].'</a>' : '';
   //                      break;
   //                  case 'article':
   //                      $articleInfo = Article::getArticleInfo($list[$key]['item_id']);
   //                      $list[$key]['message'] = $articleInfo ?  '<a href="'.url('article/detail',['id'=>$articleInfo['id']]).'" >'.$articleInfo['title'].'</a>' : '';
   //                      break;
   //              }
   //          }
		}
		return ['list'=>$list,'page'=>$pageVar];
	}

    /**
     * 设置消息已读
     * @param $id
     * @param $uid
     * @return false
     */
	public static function setNotifyRead($id,$uid): bool
    {
        if(!$id || !$uid) return false;
        if(db('notify')->where(['id'=>$id,'recipient_uid'=>$uid])->update(['read_flag'=>1]))
        {
            Users::updateNotifyUnread($uid);
            return true;
        }
        return false;
    }

    /**
     * 设置全部消息已读
     * @param $uid
     * @return bool
     */
    public static function setNotifyReadAll($uid): bool
    {
        if(!$uid) return false;
        if(db('notify')->where(['recipient_uid'=>$uid])->update(['read_flag'=>1]))
        {
            Users::updateNotifyUnread($uid);
            return true;
        }
        return false;
    }

    /**
     * 删除通知消息
     * @param $id
     * @param $uid
     * @return bool
     */
    public static function removeNotify($id,$uid): bool
    {
        if(!$id) return false;
        if(db('notify')->where(['id'=>$id,'recipient_uid'=>$uid])->update(['read_flag'=>0]))
        {
            Users::updateNotifyUnread($uid);
            return true;
        }
        return false;
    }

    /**
     * 设置通知类型
     * @param $name
     * @param $value
     * @return mixed
     */
	public static function setNotifyType($name,$value)
    {
        self::$notifyType[$name]=$value;
        $file = root_path() . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR . 'notify.php';
        if ($handle = fopen($file, 'w')) {
            fwrite($handle, "<?php\n\n" . "return " . var_export(self::$notifyType, TRUE) . ";\n");
            fclose($handle);
        }
        return self::$notifyType[$name]=$value;
    }

    /**
     * 获取通知类型
     * @param $name
     * @param $value
     * @return int|mixed
     */
    public static function getNotifyType($name,$value='unique_id')
    {
        return config('notify.'.$name)[$value];
    }
}