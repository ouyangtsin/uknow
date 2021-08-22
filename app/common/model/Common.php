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
use app\common\logic\common\FocusLogic;
use app\ask\model\Column;
use app\ask\model\Question;
use app\ask\model\Topic;
use app\ask\model\Vote;
use think\facade\Db;

use think\Model;

class Common extends BaseModel
{
    /**
     * @param $where
     * @return mixed
     */
	public static function checkFavorite($where){
		return db('favorite')->where($where)->find();
	}

    /**
     * @param $where
     * @param $type
     * @return array|Model|null
     */
	public static function checkVote($where,$type){
		switch ($type) {
			case 'question_comment':
				$info=db('question_vote')->where($where)->find();
				break;
			
			default:

				break;
		}
		return $info;
	}


	public static function getUserFocus($uid,$type,$page=1,$per_page=10)
    {
        if(!$uid || !$type) return false;
        $dbName = 'question_focus';
        $where = [];
        switch ($type)
        {
            case 'question':
                $dbName = 'question_focus';
                $where = ['uid'=>$uid];
                break;

            case 'friend':
                $dbName = 'users_follow';
                $where = ['fans_uid'=>$uid];
                break;

            case 'fans':
                $dbName = 'users_follow';
                $where = ['friend_uid'=>$uid];
                break;

            case 'column':
                $dbName = 'column_focus';
                $where = ['uid'=>$uid];
                break;

            case 'topic':
                $dbName = 'topic_focus';
                $where = ['uid'=>$uid];
                break;

            case 'favorite':
                $dbName = 'favorite_focus';
                $where = ['uid'=>$uid];
                break;
        }

        $result = db($dbName)
            ->where($where)
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ])->toArray();

        foreach ($result['data'] as $key=>$val)
        {
            switch ($type)
            {
                case 'question':
                    $question_ids = array_column($result['data'],'question_id');
                    $question_infos = Question::getQuestionByIds($question_ids);
                    if(!empty($question_infos))
                    {
                        $result['data'][$key] = $question_infos[$val['question_id']];
                        $result['data'][$key]['detail'] = htmlspecialchars_decode($question_infos[$val['question_id']]['detail']);
                        $result['data'][$key]['topics'] = Topic::getTopicByItemType('question',$val['question_id']);
                        $result['data'][$key]['vote_value'] =  Vote::getVoteByType($val['question_id'],'question',$uid);
                    }
                    break;

                case 'friend':
                    $uid_s = array_column($result['data'],'fans_uid');
                    $user_infos = Users::getUserInfoByIds($uid_s);
                    if(!empty($user_infos))
                    {
                        $result['data'][$key] = $user_infos[$val['fans_uid']];
                    }
                    break;

                case 'fans':
                    $uid_s = array_column($result['data'],'friend_uid');
                    $user_infos = Users::getUserInfoByIds($uid_s);
                    $result['data'][$key] = !empty($user_infos) ?$user_infos[$val['friend_uid']] : [];
                    break;

                case 'column':
                    $column_ids = array_column($result['data'],'column_id');
                    $column_infos = Column::getColumnByIds($column_ids);
                    $result['data'][$key] = $column_infos[$val['column_id']];
                    break;

                case 'topic':
                    $topic_ids = array_column($result['data'],'topic_id');
                    $topic_infos = Topic::getTopicByIds($topic_ids);
                    $result['data'][$key] = $topic_infos[$val['topic_id']];
                    break;
            }
        }

        return $result;
    }
}