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
use app\common\library\helper\IpHelper;
use app\common\library\helper\LogHelper;
use app\common\model\BaseModel;
use app\common\model\PostRelation;
use app\common\model\Users;
use think\facade\Request;

class Answer extends BaseModel
{
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;

    /**
     * 获取回答信息
     * @param $answer_id
     * @return mixed
     */
	public static function getAnswerInfoById($answer_id)
	{
		$answer_info = db('answer')->where(['id'=>$answer_id,'status'=>1])->find();
		$answer_info['content']= htmlspecialchars_decode($answer_info['content']);
		return $answer_info;
	}

    /**
     * 获取回答信息
     * @param $answer_ids
     * @return array
     */
    public static function getAnswerInfoByIds($answer_ids): array
    {
        $answer_ids = is_array($answer_ids) ? $answer_ids : explode(',',$answer_ids);
        $answer_list = db('answer')->whereIn('id',$answer_ids)->where(['status'=>1])->select()->toArray();
        $result = [];

        foreach ($answer_list as $key=>$val)
        {
            $result[$val['id']] = $val;
        }
        return $result;
    }

    /**
     * 保存回答
     * @param $data
     * @return array|false
     */
	public static function saveAnswer($data)
    {
        $data['answer_user_ip'] = IpHelper::getRealIp();
		if ($data['id']) {
            $data['update_time'] = time();
			$ret = db('answer')->where(['id' => $data['id']])->update($data);
			if($ret)
            {
                //添加行为日志
                LogHelper::addActionLog('modify_answer','answer',$data['id'],$data['uid'],$data['is_anonymous']);
            }
		} else {
		    $data['create_time'] = time();
			$ret = db('answer')->insertGetId($data);
			if($ret)
            {
                //添加积分记录
                LogHelper::addScoreLog('publish_question_answer',$ret,'answer',$data['uid']);

                //添加行为日志
                LogHelper::addActionLog('publish_answer','answer',$ret,$data['uid'],$data['is_anonymous']);

                //邀请回答积分记录
                $invite_info = db('question_invite')->where(['question_id'=> (int)$data['question_id'], 'recipient_uid'=> (int)$data['uid']])->value('id');
                $is_answer = db('answer')->where(['question_id'=> (int)$data['question_id'], 'uid'=> (int)$data['uid']])->value('id');

                if($invite_info && !$is_answer)
                {
                    LogHelper::addScoreLog('answer_question_by_invite',$ret,'answer',$data['uid']);
                }
            }
           $quser_name= get_user_info($data['uid'],'user_name');
           $question_info= Question::getQuestionInfo($data['question_id'],'title,uid');
            send_notify($data['uid'],$question_info['uid'],'TYPE_INVITE',$quser_name.'回答了您问题',$data['question_id'],['item_type'=>'question','message'=>$quser_name.'回答了您问题'.$question_info['title'],'item_id'=>$data['question_id'],'title'=>$question_info['title'],'url'=>url('question/detail',['id'=>$data['question_id']])]);
		}

		if ($ret) {
			$answer_count = db('answer')->where(['question_id'=>$data['question_id'],'status'=>1])->count();
			$answer_id = $data['id'] ? : $ret;
            Question::update(['answer_count'=>$answer_count,'last_answer'=>$answer_id],['id'=>$data['question_id']]);
            PostRelation::updatePostRelation($data['question_id'],'question',['answer_count'=>$answer_count]);
            $info = db('answer')->where('id',$answer_id)->find();
            Users::updateUserFiled($info['uid'],['answer_count'=>$answer_count]);
            $info['user_info'] = Users::getUserInfo($info['uid']);

			return ['answer_count'=>$answer_count,'info'=>$info];
		} 
		self::setError('保存失败！');
		return false;
	}

    /**
     * 删除回答
     * @param $answer_id
     * @return Question|false
     */
	public static function deleteAnswer($answer_id)
	{
		$answer_info = self::getAnswerInfoById($answer_id);
		if(!db('answer')->where(['id'=>$answer_id])->update(['status'=>0]))
		{
			self::setError('回答删除失败');
			return false;
		}

		$answer_count = db('answer')->where(['question_id'=>$answer_info['question_id'],'status'=>1])->count();
		return Question::update(['answer_count'=>$answer_count],['id'=>$answer_info['question_id']]);
	}

    /**
     * 根据问题id获取回答
     * @param $data
     * @param array $sort
     * @return mixed
     */
	public static function getAnswerByQid($data,$sort=[])
	{
		if ($data['answer_id']) {
			$where = ['question_id' => $data['question_id'], 'id' => $data['answer_id'],'status'=>1];
		} else {
			$where = ['question_id' => $data['question_id'],'status'=>1];
		}
		$list =db('answer')
			->where($where)
            ->order($sort)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' => intval($data['limit']),
                'page'=>intval($data['page'])
            ]);

        $page_render = $list->render();
        $list = $list->toArray();
        foreach ($list['data'] as $key=>$val)
        {
            $list['data'][$key]['user_info'] = Users::getUserInfo($val['uid']);
            $list['data'][$key]['content'] = htmlspecialchars_decode($val['content']);
        }
        $list['page_render']=$page_render;
		return $list;
	}

	//获取回答列表
    public static function getAnswerByQuestionId($question_id,$answer_id=0,$page=1,$per_page=10,$sort=[],$uninterested=0)
    {
        if ($answer_id) {
            $where = ['question_id' => $question_id, 'id' => $answer_id,'status'=>1];
        } else {
            $where = ['question_id' => $question_id,'status'=>1];
        }

        $list =db('answer')
            ->where($where)
            ->order($sort)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' => $per_page,
                'page'=>intval($page)
            ]);

        $pageRender = $list->render();
        $list = $list->toArray();
        foreach ($list['data'] as $key=>$val)
        {
            $list['data'][$key]['user_info'] = Users::getUserInfo($val['uid']);
            $list['data'][$key]['content'] = htmlspecialchars_decode($val['content']);
        }
        $list['page_render']=$pageRender;
        return $list;
    }

    /**
     * 获取回答评论
     * @param $answer_id
     * @param int $page
     * @return array
     */
	public static function getAnswerComments($answer_id,$page=1)
    {
		$list = db('answer_comment')
			->where(['answer_id' => $answer_id, 'status' => 1])
            ->paginate([
                'list_rows'=> 5,
                'page' => $page,
                'query'=>request()->param()
            ]);

        $pageVar = $list->render();
        $list = $list->all();

		foreach ($list as $key => &$value) {
			$list[$key]['at_info'] = json_decode($value['at_info'], true);
			$list[$key]['user_info'] = Users::getUserInfo($value['uid']);
			$value['message'] = htmlspecialchars_decode($value['message']);
		}
        return ['list'=>$list,'page'=>$pageVar];
	}

    /**
     * 根据问题ids获取最后回答内容列表
     * @param $question_ids
     * @return array|false
     */
	public static function getLastAnswerByIds($question_ids)
	{
		if (!is_array($question_ids) || count($question_ids) === 0)
		{
			return false;
		}

		array_walk_recursive($question_ids, 'intval');
		$last_answer_ids = db('question')->whereIn('id',implode(',', $question_ids))->where([
			['status','=',1]
		])->column('last_answer');

		$result = array();

		if ($last_answer_ids)
		{
			$last_answer = db('answer')->whereIn('id',implode(',', $last_answer_ids))->select()->toArray();
			if ($last_answer)
			{
				foreach ($last_answer AS $key => $val)
				{
					$result[$val['question_id']] = $val;
				}
			}
		}
		return $result;
	}

    /**
     * 保存回答评论
     * @param $data
     * @return Answer|false|null
     */
	public static function saveComments($data)
	{
		$arr['at_info'] = isset($data['at_info']) ? html_entity_decode($data['at_info']) : NUll;
		$arr['answer_id'] = $data['answer_id'];
		$arr['uid'] = $data['uid'];
		$arr['message'] = $data['message'];

		if (isset($data['id']) and $data['id'] > 0) {
			$arr['id'] = $data['id'];
			$arr['update_time'] = time();
		} else {
			$arr['create_time'] = time();
		}
		$result = db('answer_comment')->save($arr);
		if(!$result)
		{
			self::setError('评论失败');
			return false;
		}
        if($arr['at_info']){
            $arr['at_info']=json_decode($arr['at_info'],true);
            send_notify($data['uid'],$arr['at_info']['uid'],'TYPE_PEOPLE_AT_ME',$data['user_name'].'在问题回答评论中@到了您',$data['answer_id'],['item_type'=>'answer_comment','message'=>$data['user_name'].'在问题回答评论中@到了您','item_id'=>$data['answer_id'],'title'=>$data['question_info']['title'],'url'=>url('question/detail',['id'=>$data['question_info']['id']])]);
        }
        $pinfo=self::get_pub_user($data['answer_id']);
        send_notify($data['uid'],$pinfo['uid'],'TYPE_ANSWER_COMMENT',$data['user_name'].'评论了您的回答',$data['answer_id'],['item_type'=>'answer_comment','message'=>$data['user_name'].'评论了您的回答','item_id'=>$data['answer_id'],'title'=>$data['question_info']['title'],'url'=>url('question/detail',['id'=>$data['question_info']['id']])]);
		return self::updateCommentCount($data['answer_id']);
	}
    /**
     * 获取回答发布人
     * @param $item_id
     * @return mixed
     */
    public static function get_pub_user($item_id){
        return db('answer')->field('uid')->find($item_id);
    }
    /**
     * 更新回答评论数
     * @param $answer_id
     * @return Answer|null
     */
	public static function updateCommentCount($answer_id): ?Answer
    {
		$count = db('answer_comment')->where(['answer_id' => $answer_id, 'status' => 1])->count();
		return self::update(['comment_count' => $count],['id' => $answer_id]);;
	}

    /**
     * 删除问题回答评论
     * @param $comment_id
     * @param $uid
     * @return Answer|false
     */
	public static function deleteComment($comment_id, $uid)
	{
		if(!$info = db('answer_comment')->find($comment_id))
		{
			self::setError('回答评论不存在');
			return false;
		}

		$user_info = Users::getUserInfo($uid);
		if ($info['uid']!==$user_info['uid'] || ($user_info['group_id']!==1 && $user_info['group_id']!==2))
		{
			self::setError('您没有删除回答评论的权限');
			return false;
		}

		if(!db('answer_comment')->where(['id'=>$comment_id])->update(['status'=>0]))
		{
			self::setError('问题评论删除失败');
			return false;
		}
		$comment_count = db('answer_comment')->where(['answer_id'=>$info['answer_id'],'status'=>1])->count();
		return self::update(['comment_count'=>$comment_count],['id'=>$info['answer_id']]);
	}
}