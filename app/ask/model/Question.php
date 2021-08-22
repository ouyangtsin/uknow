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

use app\common\library\helper\ImageHelper;
use app\common\library\helper\IpHelper;
use app\common\library\helper\LogHelper;
use app\common\model\BaseModel;
use app\common\model\Draft;
use app\common\model\PostRelation;
use app\common\model\Users;
use app\ask\model\Report;
use app\common\model\Common;
use think\facade\Db;
use WordAnalysis\Analysis;

/**
 * 问题模型类
 * Class Question
 * @package app\ask\model
 */
class Question extends BaseModel
{
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;

	//根据问题id获取问题详情
	public static function getQuestionInfo($question_id,$field="*")
	{
        $question_info = db('question')->field($field)->where(['id'=>intval($question_id),'status'=>1])->find();
        if($question_info && isset($question_info['detail']))
        {
            $question_info['detail'] = htmlspecialchars_decode($question_info['detail']);
        }
        return $question_info;
	}

	//根据问题ids获取问题列表
	public static function getQuestionByIds($question_ids)
	{
		if (!$question_ids) return false;
		$questions_list = db('question')->where(['status'=>1])->whereIn('id',implode(',', $question_ids))->select()->toArray();
		$result = array();
		if ($questions_list)
		{
			foreach ($questions_list AS $key => $val)
			{
				$result[$val['id']] = $val;
			}
		}
		return $result;
	}

    /**
     * 新增保存问题
     * @param $uid
     * @param array $postData 保存数据
     * @return mixed
     */
	public static function saveQuestion($uid, array $postData)
    {
        $status = $postData['status'] ?? 1;
		$insertData = array(
			'title' => $postData['title'],
			'detail' =>ImageHelper::fetchContentImagesToLocal($postData['detail'],'question',$uid),
			'uid' => (int)$uid,
			'is_anonymous' => (int)$postData['is_anonymous'],
			'user_ip' => IpHelper::getRealIp(),
			'category_id' => $postData['category_id'] ?? 0,
			'question_type' => $postData['question_type'] ?? 'normal',
			'create_time'=>time(),
			'update_time'=>time(),
			'status'=>$status
		);
        if(isset($postData['id']) && $postData['id'])
        {
            $question_id = $postData['id'];
            unset($insertData['uid'],$insertData['create_time']);
            db('question')->where('id',$postData['id'])->update($insertData);
            $result = db('question')->where('id',$postData['id'])->inc('modify_count',1)->update();
            if($result)
            {
                //添加行为日志
                LogHelper::addActionLog('modify_question','question',$question_id,$uid,$postData['is_anonymous']);
                Draft::deleteDraftByItemID($uid,'question',$question_id);
            }
        }else{
            $question_id = db('question')->insertGetId($insertData);
            if($question_id)
            {
                //添加行为日志
                LogHelper::addActionLog('publish_question','question',$question_id,$uid,$insertData['is_anonymous']);
                //添加积分日志
                LogHelper::addScoreLog('publish_question',$question_id,'question',$uid);
                Draft::deleteDraftByItemID($uid,'question');
            }
        }

        if (!$question_id) {
            return  false;
        }

        //存储话题问题关联
        if(isset($postData['topics'])) {
            $topics = is_array($postData['topics']) ? array_filter($postData['topics']) : explode(',',trim($postData['topics']));
            if (!empty($topics))
            {
                foreach ($topics as $key => $title)
                {
                    if($title)
                    {
                        $topic_id =isset($postData['from'])?$title:Topic::saveTopic($title, $uid);
                        Topic::saveTopicRelation($uid, $topic_id, $question_id, 'question');
                        Topic::save_log(['uid'=>$uid,'type'=>4,'title' => $postData['title'],'question_id'=>$question_id, 'topic_id'=>$topic_id,'topic_title'=>$title]);
                    }
                }
            }
        }

        //更新用户问题数量
        if(!$postData['id'])
        {
            $question_count = db('question')->where(['uid' => $uid, 'status' => 1])->count();
            Users::updateUserFiled($uid, ['question_count' => $question_count]);
        }

        //加入内容聚合表
        PostRelation::savePostRelation($question_id,'question');
        return $question_id;
	}

    /**
     * 更新问题浏览量
     * @param $question_id
     * @param int $uid
     * @return bool
     */
	public static function updateQuestionViews($question_id,$uid=0)
    {
		$cache_key = md5('cache_question_'.$question_id.'_'.$uid);
		$cache_result = cache($cache_key);
		if($cache_result) {
            return true;
        }
		cache($cache_key,$cache_key,['expire'=>60]);
		return db('question')->where(['id'=>$question_id])->inc('view_count')->update();
	}

	//获取问题评论列表
	public static function getQuestionComments($question_id,$page,$sort=['create_time'=>'desc']): array
    {
		$list = db('question_comment')
			->where([['question_id','=', (int)$question_id], ['status','=', 1]])
            ->order($sort)
			->paginate([
				'list_rows'=> 5,
				'page' => $page,
				'query'=>request()->param()
			]);
		$pageVar = $list->render();
		$list = $list->all();
		
		foreach ($list as $key => $value) {
			$list[$key]['at_info'] = json_decode($value['at_info'], true);
			$list[$key]['user_info'] = Users::getUserInfo($value['uid']);
			$list[$key]['check'] = Common::checkVote([
					'uid'=>intval(session('login_uid')),
					'item_type'=>'question_comment',
					'item_id'=>$value['id'],
					'vote_value'=>1,
				],'question_comment')?1:0;
			$list[$key]['report'] = Report::getReportInfo($value['id'],'question_comment',intval(session('login_uid')));
			$list[$key]['vote_count'] = self::getQuestionCommentVoteCount($value['id']);
		}

		return ['list'=>$list,'page'=>$pageVar];
	}

    /**
     * 获取问题评论列表
     * @param $item_id
     * @return mixed
     */
	public static function getQuestionCommentVoteCount($item_id){
        return db('question_vote')
            ->where([['item_id','=', (int)$item_id], ['item_type','=', 'question_comment'],['vote_value','=',1]])
            ->count();
	}

    /**
     * 获取问题评论列表
     * @param $item_id
     * @param $uid
     * @param $comment
     * @return bool
     */
	public static function comment_vote($item_id,$uid,$comment){
		$vote = db('question_vote')
			->where([['item_id','=', (int)$item_id], ['item_type','=', 'question_comment'],['uid','=',$uid]])
			->find();

		if($vote){
			db('question_vote')
			->where([['item_id','=', (int)$item_id], ['item_type','=', 'question_comment'],['uid','=',$uid]])
			->update(['vote_value'=>$vote['vote_value']==1?0:1]);
		}else{
			db('question_vote')
			->save(['create_time'=>time(),'vote_value'=>1,'item_id'=>$item_id,'item_type'=>'question_comment','item_uid'=>$comment['uid'],'uid'=>$uid]);
		}
		return true;
	}

    /**
     * 获取问题评论列表
     * @param $item_id
     * @return mixed
     */
	public static function comment($item_id){
        return db('question_comment')->find($item_id);
	}
	
	//保存问题评论
	public static function saveComments($data)
	{
		$arr['at_info'] = isset($data['at_info']) ? html_entity_decode($data['at_info']) : NUll;
		$arr['question_id'] = $data['question_id'];
        $arr['uid'] = $data['uid'];
		$question_info = $data['question_info'];
		// $arr['message'] = Users::parseAtUser($data['message'])[0];

		if (isset($data['id']) and $data['id'] > 0) {
			$arr['id'] = $data['id'];
			$arr['update_time'] = time();
		} else {
			$arr['create_time'] = time();
		}
		$result = db('question_comment')->save($arr);
		if(!$result)
		{
			self::setError('评论失败');
			return false;
		}
        if($arr['at_info']){
            $arr['at_info']=json_decode($arr['at_info'],true);
            send_notify($data['uid'],$arr['at_info']['uid'],'TYPE_PEOPLE_AT_ME',$data['user_name'].'在问题评论中@到了您',$data['question_id'],['item_type'=>'question_comment','message'=>$data['user_name'].'在问题评论中@到了您','item_id'=>$data['question_id'],'title'=>$question_info['title'],'url'=>url('question/detail',['id'=>$data['question_id']])]);
        }
        $pinfo=self::get_pub_user($data['question_id']);
        send_notify($data['uid'],$pinfo['uid'],'TYPE_ANSWER_COMMENT',$data['user_name'].'评论了您的问题',$data['question_id'],['item_type'=>'question_comment','message'=>$data['user_name'].'评论了您的问题','item_id'=>$data['question_id'],'title'=>$question_info['title'],'url'=>url('question/detail',['id'=>$data['question_id']])]);
		return self::updateCommentCount($data['question_id']);
	}
    /**
     * 获取问题发布人
     * @param $item_id
     * @return mixed
     */
    public static function get_pub_user($item_id,$field="uid,user_name"){
        $info=db('question')->field('uid')->find($item_id);
        return ['uid'=>$info['uid'],'user_name'=>get_user_info($info['uid'],'user_name')]; 
    }
    /**
     * 删除问题评论
     * @param $comment_id
     * @param $uid
     * @return Question|false
     */
	public static function deleteComment($comment_id, $uid)
	{
		if(!$info = db('question_comment')->find($comment_id))
		{
			self::setError('问题评论不存在');
			return false;
		}
		$user_info = Users::getUserInfo($uid);
		if ($info['uid']!==$user_info['uid'] || ($user_info['group_id']!==1 && $user_info['group_id']!==2))
		{
			self::setError('您没有删除评论的权限');
			return false;
		}

		if(!db('question_comment')->where(['id'=>$comment_id])->update(['status'=>0]))
		{
			self::setError('问题评论删除失败');
			return false;
		}
		$comment_count = db('question_comment')->where(['question_id'=>$info['question_id'],'status'=>1])->count();
		return self::update(['comment_count'=>$comment_count],['id'=>$info['question_id']]);
	}

    /**
     * 更新问题评论数
     * @param $question_id
     * @return Question
     */
	public static function updateCommentCount($question_id)
    {
		$count = db('question_comment')->where(['question_id' => $question_id, 'status' => 1])->count();
		return self::update(['comment_count' => $count],['id' => $question_id]);
	}

    /**
     * 获取邀请用户列表
     * @param $uid
     * @param $where
     * @param int $question_id
     * @param int $page
     * @return array
     */
	public static function getQuestionInvite($uid,$where,$question_id=0, $page=1)
	{
		$data = Users::getUserList($where,'hot',request()->param(),$page,5);
		$list = $data['list'];
		foreach ($list as $key=>$val)
		{
			$list[$key]['has_invite'] = 0;
			if( db('question_invite')->where(['sender_uid'=>$uid,'recipient_uid'=>$val['uid'],'question_id'=>intval($question_id)])->value('id'))
			{
				$list[$key]['has_invite'] = 1;
			}
		}
		$data['list'] = $list;
		return $data;
	}

    /**
     * 保存更新邀请用户
     * @param $question_info
     * @param $sender_uid
     * @param $recipient_uid
     * @return false
     */
	public static function saveQuestionInvite($question_info,$sender_uid,$recipient_uid)
	{
        $question_id=$question_info['id'];
		if(!$question_id || !$sender_uid || !$recipient_uid) return false;
		$where[] = ['question_id','=', (int)$question_id];
		$where[] = ['sender_uid','=', (int)$sender_uid];
		$where[] = ['recipient_uid','=', (int)$recipient_uid];

		$invite_id = db('question_invite')->where($where)->value('id');

		if($invite_id)
		{
			return false;
		}

		$insert_data = array(
			'question_id'=> (int)$question_id,
			'sender_uid'=> (int)$sender_uid,
			'recipient_uid'=> (int)$recipient_uid,
			'create_time'=>time()
		);
		$result = db('question_invite')->insert($insert_data);
		if($result)
        {
            LogHelper::addScoreLog('invite_user_answer_question',$question_id,'question',$sender_uid);
        }
        send_notify($sender_uid,$recipient_uid,'TYPE_INVITE','有人邀请您回答问题',$question_id,['item_type'=>'invite_answer','message'=>'有人邀请您回答问题','item_id'=>$question_id,'title'=>$question_info['title'],'url'=>url('question/detail',['id'=>$question_id])]);

		return  $result;
	}

    /**
     * 获取相关问题
     * @param $question_id
     * @param int $limit
     * @return array|false
     */
    public static function getRelationQuestion($question_id,$limit=10)
    {
        if(!$question_id) {
            return false;
        }
        $question_info = self::getQuestionInfo($question_id);
        $keywords = Analysis::getKeywords($question_info['title']);
        $keywords = explode(',', $keywords);
        $questionIds = db('question')
            ->whereRaw("status=1 AND (`title` regexp '".implode('|', $keywords)."' OR `detail` regexp '".implode('|', $keywords)."')")
            ->order('view_count','DESC')
            ->page(1,$limit)
            ->select()
            ->toArray();
        if($questionIds = array_column($questionIds,'id'))
        {
           unset($questionIds[array_search($question_id, $questionIds, true)]);
           return self::getQuestionByIds($questionIds);
        }
        return false;
    }

    //问题操作管理
    public static function manger($question_id,$type)
    {
        if(!$question_info = self::getQuestionInfo($question_id))
        {
            self::setError('问题不存在');
            return false;
        }

        if(!$question_info['status'])
        {
            self::setError('问题已被删除');
            return false;
        }

        switch ($type)
        {
            case 'recommend':
                self::update(['is_recommend'=>1],['id'=>$question_id]);
                PostRelation::updatePostRelation($question_id,'question',['is_recommend'=>1]);
                break;
            case 'un_recommend':
                self::update(['is_recommend'=>0],['id'=>$question_id]);
                PostRelation::updatePostRelation($question_id,'question',['is_recommend'=>0]);
                break;
            case 'set_top':
                self::update(['set_top'=>1,'set_top_time'=>time()],['id'=>$question_id]);
                PostRelation::updatePostRelation($question_id,'question',['set_top'=>1,'set_top_time'=>time()]);
                break;
            case 'unset_top':
                self::update(['set_top'=>0,'set_top_time'=>0],['id'=>$question_id]);
                PostRelation::updatePostRelation($question_id,'question',['set_top'=>0,'set_top_time'=>0]);
                break;
        }

        return true;
    }

    /**
     * 删除问题
     * @param $id
     * @return bool
     */
    public static function removeQuestion($id): bool
    {
        if(!$question_info = self::getQuestionInfo($id))
        {
            self::setError('问题不存在');
            return false;
        }

        if(!self::update(['status'=>0],['id'=>$id]))
        {
            self::setError('删除失败');
            return false;
        }
        //更新首页表
        PostRelation::updatePostRelation($id,'question',['status'=>0]);
        return true;
    }

    /**
     * 获取问题的最佳回答信息
     * @param $question_id
     * @return false
     */
    public static function getQuestionBestAnswerById($question_id)
    {
        if(!$question_info = self::getQuestionInfo($question_id))
        {
            self::setError('问题不存在');
            return false;
        }
        $best_info = db('answer')->where(['is_best'=>1,'status'=>1,'question_id'=>$question_id])->find();
        $question_info['best_info'] = $best_info ? : [];
        return $question_info;
    }

    /**
     * 获取问题的最佳回答信息
     * @param $question_ids
     * @param string $array_key
     * @return array|false
     */
    public static function getQuestionBestAnswerByIds($question_ids, string $array_key='id')
    {
        if(!$question_ids)
        {
            return false;
        }
        $best_infos = db('answer')->where(['is_best'=>1,'status'=>1])->whereIn('question_id',$question_ids)->select()->toArray();
        $infos = [];
        foreach($best_infos as $key=>$val)
        {
            $infos[$val['question_id']] = $val;
        }

        $question_infos = self::getQuestionByIds($question_ids);

        $return = [];
        foreach ($question_infos as $key=>$val)
        {
            $val['best_info'] = $infos[$val['id']];
            $return[$val[$array_key]][] = $val;
        }

        return $return;
    }
}