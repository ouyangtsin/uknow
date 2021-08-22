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
use app\ask\model\Answer;
use app\ask\model\Article;
use app\ask\model\Notify;
use app\ask\model\Question;
use think\Model;

class Approval extends Model
{
	/**
	 * 添加审核数据
	 * @param $type
	 * @param $data
	 * @param $uid
	 * @return int|string
	 */
	public static function saveApproval($type, $data, $uid)
	{
		$insertData = array(
			'type' => $type,
			'data' => json_encode($data,JSON_UNESCAPED_UNICODE),
			'uid' => intval($uid),
			'create_time' => time()
		);
		return db('approval')->insertGetId($insertData);
	}

    /**
     * 审核通过
     * @param $id
     * @return bool
     */
	public static function approval($id)
    {
		$id = is_array($id) ? $id : explode(',',$id);

		if (!$approval_list = db('approval')->whereIn('id',$id)->select()->toArray())
		{
			return false;
		}

		foreach ($approval_list as $key=>$val)
		{
			//更新审核
			self::update(['status'=>1],['id'=>$val['id']]);
			$val['data'] = json_decode($val['data'],true);
            $val['data']['from']='approval';
			switch ($val['type'])
			{
				case 'question':
					$question_id = Question::saveQuestion($val['uid'],$val['data']);
					Notify::send(0, $val['uid'], 'TYPE_APPROVAL',  '问题审核通过',$question_id, array('item_type' =>'question'));
					break;

                case 'modify_question':
                    $question_id = Question::saveQuestion($val['uid'],$val['data']);
                    Notify::send(0, $val['uid'], 'TYPE_APPROVAL',  '问题修改审核通过',$question_id, array('item_type' =>'question'));
                    break;

				case 'answer':
                    $answer_id = Answer::saveAnswer($val['data']);
                    Notify::send(0, $val['uid'], 'TYPE_APPROVAL',  '回答审核通过',$answer_id, array('item_type' =>'answer'));
                    break;

                case 'modify_answer':
                    $answer_id = Answer::saveAnswer($val['data']);
                    Notify::send(0, $val['uid'], 'TYPE_APPROVAL',  '修改回答审核通过',$answer_id, array('item_type' =>'answer'));
                    break;

				case 'article':
                    $article_id = Article::saveArticle($val['uid'],$val['data']);
                    Notify::send(0, $val['uid'], 'TYPE_APPROVAL',  '文章审核通过',$article_id, array('item_type' =>'answer'));
                    break;

				case 'article_comment':

					break;
			}
		}

		return true;
	}

    /**
     * 拒绝审核
     * @param $id
     * @return mixed
     */
	public static function decline($id)
    {
		if (!$approval_item = db('approval')->where('id',$id)->find())
		{
			return false;
		}

		switch ($approval_item['type'])
		{
			case 'question':
			case 'answer':
			case 'article':
				break;

			case 'article_comment':
				break;
		}
		return true;
	}

    /**
     * 获取审核列表
     * @param $where
     * @param $page
     * @param $per_page
     * @return array
     */
	public static function getApprovalListByPage($where,$page=1,$per_page=10)
    {
		$list = db('approval')->where($where)->order('create_time','DESC')->paginate(
			[
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param()
			]
		);
		$pageVar = $list->render();
		$list = $list->all();

		foreach ($list as $key=>$val)
		{
			$list[$key]['data'] = json_decode($val['data'],true);
		}

		return ['list'=>$list,'page'=>$pageVar];
	}
}