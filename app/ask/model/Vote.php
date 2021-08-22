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
use app\common\library\helper\LogHelper;
use app\common\model\BaseModel;
use app\common\model\Follow;
use app\common\model\Users;

class Vote extends BaseModel
{
	//添加投票信息
	public static function saveVote($uid, $item_id, $item_type, $vote_value, $weigh_factor = 0)
    {
		if (!$item_id || !$item_type) {
			return false;
		}
        $vote_value = intval($vote_value);
		if (!in_array($vote_value, array(-1, 0, 1), true)) {
			return false;
		}

        $vote_uid = 0;
		$dbName = 'article_vote';
		$title='';
		switch ($item_type) {
            case 'question':
                $vote_uid = app('db')->name('question')->where('id', $item_id)->value('uid');
                $dbName = 'question_vote';
                $title = '问题';
                break;
            case 'answer':
                $vote_uid = app('db')->name('answer')->where('id', $item_id)->value('uid');
                $dbName = 'question_vote';
                $title = '回答';
                break;
            case 'article':
                $vote_uid = app('db')->name('article')->where('id', $item_id)->value('uid');
                $dbName = 'article_vote';
                $title = '文章';
                break;
            case 'article_comment':
                $vote_uid = app('db')->name('article_comment')->where('id', $item_id)->value('uid');
                $dbName = 'article_vote';
                $title = '文章评论';
                break;
            case 'answer_comment':
                $vote_uid = app('db')->name('answer_comment')->where('id', $item_id)->value('uid');
                $dbName = 'question_vote';
                $title = '回答评论';
                break;
            case 'question_comment':
                $vote_uid = app('db')->name('question_comment')->where('id', $item_id)->value('uid');
                $dbName = 'question_vote';
                $title = '问题评论';
                break;

		}

		if ($vote_uid == $uid) {
			self::setError('您不能对自己进行投票');
			return false;
		}

		$vote_info = db($dbName)->where([
			['item_id', '=', $item_id],
			['item_type', '=', $item_type],
			['uid', '=', $uid]]
		)->find();

		$insertData = array(
			'item_id' => $item_id,
			'item_type' => $item_type,
			'item_uid' => $vote_uid,
			'create_time' => time(),
			'vote_value' => $vote_value,
			'uid' => $uid,
			'weigh_factor' => $weigh_factor,
		);

		if (!$vote_info) //没有投票信息，增加
		{
			$insertData['vote_value'] = $vote_value;
			db($dbName)->insert($insertData);
			$vote_result = 1;
		}
		else // 有记录，不相同，则更新记录
		{
			db($dbName)->where(['id' => $vote_info['id']])->update(["create_time" => time(), "vote_value" => $vote_value]);
			$vote_result = $vote_value;
		}

		//是点赞操作发送通知给被操作的用户
		if ($vote_value == 1 && $uid != $vote_uid) {
            LogHelper::addActionLog('agree_'.$item_type,$item_type,$item_id,$uid);
			send_notify($uid,$vote_uid,'TYPE_AGREE','有用户点赞了你的'.$title,$item_id,['item_type'=>$item_type,'title'=>$title,'message'=>'有用户点赞了你的'.$title,'url'=>url('article/detail',['id'=>$item_id])]);
		}

		// 更新赞踩数量
		$agree_count = db($dbName)->where(['vote_value'=>1,'item_id'=>$item_id,'item_type'=>$item_type])->count();
		$against_count = db($dbName)->where(['vote_value'=>-1,'item_id'=>$item_id,'item_type'=>$item_type])->count();
		db($item_type)->where(['id' => $item_id])->update(['agree_count' => $agree_count, 'against_count' => $against_count]);

		// 更新作者的被赞同数
		$user_agree_count = db($dbName)->where(['vote_value'=>1,'item_uid'=>$vote_uid])->count();

		Users::updateUserFiled(intval($vote_uid), ['agree_count' => intval($user_agree_count)]);

		return ['vote_value' => $vote_result, 'agree_count' => $agree_count, 'against_count' => $against_count];
	}

	/**
	 * 获取赞踩信息
	 * @param $item_id
	 * @param $item_type
	 * @param int $uid
	 * @param string $value
	 * @return mixed
	 */
	public static function getVoteByType($item_id, $item_type, $uid = 0, $value = 'vote_value')
    {
		$where = array();
		$where[] = ['uid', '=', intval($uid)];
		$where[] = ['item_id', '=', intval($item_id)];
		$where[] = ['item_type', '=', $item_type];
		if ($item_type == 'article' || $item_type == 'article_comment') {
			$dbName = 'article_vote';
		} else {
			$dbName = 'question_vote';
		}
        return db($dbName)->where($where)->value($value);
	}

	/*获取踩赞用户列表*/
	public static function getVoteUserByType($item_id, $item_type, $uid = 0, $page = 1, $per_page = 10) {
		$where = array();
		$where[] = ['item_id', '=', intval($item_id)];
		$where[] = ['item_type', '=', $item_type];
		$where[] = ['vote_value', '=', 1];
		if ($item_type == 'article' || $item_type == 'article_comment') {
			$dbName = 'article_vote';
		} else {
			$dbName = 'question_vote';
		}

		$uid_ids = db($dbName)->where($where)->column('uid');
		if (!$uid_ids) {
			return false;
		}
		$user_list = Users::getUserInfoByIds($uid_ids);
		foreach ($user_list as $key => $val) {
			$user_list[$key]['has_focus'] = $uid ? Follow::checkUserFollow($uid, $val['uid']) : false;
		}
		return $user_list;
	}

    /**
     * @param $item_type
     * @param $item_ids
     * @param null $rating
     * @param null $uid
     * @return array|false
     */
    public static function getVoteByItemIds($item_type, $item_ids, $rating = null, $uid = null)
    {
        if (!is_array($item_ids) || sizeof($item_ids) == 0)
        {
            return false;
        }
        if ($item_type == 'article' || $item_type == 'article_comment') {
            $dbName = 'article_vote';
        } else {
            $dbName = 'question_vote';
        }

        $where[] = 'item_id IN(' . implode(',', $item_ids) . ')';

        if ($rating)
        {
            $where[] = 'vote_value = ' . intval($rating);
        }

        if ($uid)
        {
            $where[] = 'uid = ' . intval($uid);
        }

        $result = [];
        if ($votes = db($dbName)->whereRaw( implode(' AND ', $where))->select()->toArray())
        {
            foreach ($votes AS $key => $val)
            {
                $result[$val['item_id']] = $val;
            }
        }
        return $result;
    }
}