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
use app\ask\model\Answer;
use app\ask\model\Article;
use app\ask\model\Category;
use app\ask\model\Question;
use app\ask\model\Topic;
use app\ask\model\Vote;
use think\Model;

class PostRelation extends Model
{
	public static $totalPage = 0;
	//添加数据到聚合表
	public static function savePostRelation($item_id, $item_type, $data = [])
	{
		$result = array();
		if (!$data)
		{
			switch ($item_type)
			{
				case 'question':
					$result = db('question')->where(['id'=>$item_id,'status'=>1])->find();
					break;
				case 'article':
					$result = db('article')->where(['id'=>$item_id,'status'=>1])->find();;
					break;
			}
		}
		switch ($item_type)
		{
			case 'question':
				$data = array(
					'item_id' => intval($item_id),
					'item_type' => 'question',
					'create_time' => $result['create_time'],
					'update_time' => $result['update_time'],
					'category_id' => $result['category_id'],
					'is_recommend' => $result['is_recommend'],
					'view_count' => $result['view_count'],
					'is_anonymous' => $result['is_anonymous'],
					'popular_value' => $result['popular_value'],
					'uid' => $result['uid'],
					'agree_count' => $result['agree_count'],
					'answer_count' => $result['answer_count'],
					'status' =>$result['status']
				);
				break;
			case 'article':
				$data = array(
					'item_id' => intval($item_id),
					'item_type' => 'article',
					'create_time' => $result['create_time'],
					'update_time' => $result['create_time'],
					'category_id' => $result['category_id'],
					'view_count' => $result['view_count'],
					'is_anonymous' => 0,
					'uid' => $result['uid'],
					'agree_count' => $result['agree_count'],
					'answer_count' => $result['comment_count'],
					'is_recommend' => $result['is_recommend'],
					'status' =>$result['status']
				);
				break;
		}
		db('post_relation')->where(['item_id'=>$item_id, 'item_type'=>$item_type])->delete();
		return db('post_relation')->insertGetId($data);
	}

    /**
     * 获取聚合数据列表
     * @param null $uid
     * @param null $item_type
     * @param null $sort
     * @param null $topic_ids
     * @param null $category_id
     * @param int $page
     * @param int $per_page
     * @param int $relation_uid
     * @return array
     */
	public static function getPostRelationList($uid=null,$item_type=null, $sort = null, $topic_ids = null, $category_id = null,$page=1, $per_page=10,$relation_uid=0,$pjax='uk-index-main')
    {
		$order = $where = array();
        $order['set_top_time'] = 'DESC';
		$where[] = ['status','=',1];
		if($relation_uid)
		{
			$where[] = ['uid','=',$relation_uid];
		}

		//关注单独处理
		if($sort == 'focus')
        {
            return FocusLogic::getUserFocus($uid,[
                'publish_question',
                'publish_article',
                'publish_answer',
                'agree_question',
                'agree_article',
                'agree_answer',
                'focus_question',
                'modify_answer',
                'modify_question',
                'modify_article'
            ],$page,$per_page);
        }

		switch ($sort)
		{
			//等待回答
			case 'unresponsive':
				$where[] = ['answer_count','=',0];
				break;

			//最新
			case 'new' :
				$order['set_top_time'] = 'DESC';
				$order['update_time'] = 'DESC';
				break;

			//推荐
			case 'recommend':
				$where[] = ['is_recommend','=', 1];
				break;

			//热门
			case 'hot':
				$order['popular_value'] = 'desc';
				break;
		}
        $order['create_time'] = 'DESC';
		if ($item_type)
		{
			$where[] = ['item_type','=', $item_type];
		}

		if ($topic_ids)
		{
			$topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
			$topic_where = $item_type ? 'item_type="'.$item_type.'" AND status=1' : 'status=1';
			$topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
			$relationInfo = db('topic_relation')
				->whereRaw($topic_where.$topicIdsWhere)
				->column('item_id,item_type');
			$item_ids = array_column($relationInfo,'item_id');
			$item_types = array_column($relationInfo,'item_type');
            $where[] = ['item_id','in',implode(',', $item_ids)];
            if(!$item_type)
            {
                $where[] = ['item_type','in',implode(',', $item_types)];
            }
		}

		if ($category_id)
		{
			$category_ids = Category::getCategoryWithChildIds($category_id,null,true);
			if($category_ids)
			{
				$where[] = ['category_id','in', implode(',',$category_ids )];
			}else{
                $where[] = ['category_id','=', $category_id];
            }
		}
		$list = db('post_relation')->where($where)->order($order)->paginate(
			[
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param(),
                'pjax'=>$pjax
			]
		);
		$pageVar = $list->render();
		$allList = $list->toArray();
		$list = self::processPostList($list->all(),$uid);
		return ['list'=>$list,'page'=>$pageVar,'total'=>ceil($allList['last_page']/$per_page)];
	}

    /**
     * 获取置顶数据
     * @param null $uid
     * @param null $item_type
     * @param null $category_id
     * @return array[]|bool[]
     */
    public static function getPostTopList($uid=null,$item_type=null, $category_id = null)
    {
        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        $where[] = ['set_top','=', 1];

        if ($item_type)
        {
            $where[] = ['item_type','=', $item_type];
        }

        if ($category_id)
        {
            $category_ids = Category::getCategoryWithChildIds($category_id,null,true);
            if($category_ids)
            {
                $where[] = ['category_id','in', implode(',',$category_ids )];
            }else{
                $where[] = ['category_id','=', $category_id];
            }
        }

        $list = db('post_relation')->where($where)->order($order)->select()->toArray();
        return self::processPostList($list,$uid);
    }

    /**
     * 通用解析聚合数据列表
     */
	public static function processPostList($contents,$uid)
	{
		if (!$contents) {
            return false;
        }

        $last_answers = $topic_infos = $question_ids = $article_ids = $data_list_uid = $question_infos = $article_infos =  array();

		foreach ($contents as $key => $data)
		{
			switch ($data['item_type'])
			{
				case 'question':
					$question_ids[] = $data['item_id'];
					break;

				case 'article':
					$article_ids[] = $data['item_id'];
					break;
			}
			$data_list_uid[$data['uid']] = $data['uid'];
		}

		if ($question_ids)
		{
			if ($last_answers = Answer::getLastAnswerByIds($question_ids))
			{
				foreach ($last_answers as $key => $val)
				{
					$data_list_uid[$val['uid']] = $val['uid'];
				}
			}
			$topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
			$question_infos = Question::getQuestionByIds($question_ids);
		}

		if ($article_ids)
		{
			$topic_infos['article'] = Topic::getTopicByItemIds($article_ids, 'article');
			$article_infos = Article::getArticleByIds($article_ids);
		}

		$users_info = Users::getUserInfoByIds($data_list_uid);
		$result_list = array();
		foreach ($contents as $key => $data)
		{
			switch ($data['item_type'])
			{
				case 'question':
					if($question_infos && isset($question_infos[$data['item_id']]))
					{
						$result_list[$key] = $question_infos[$data['item_id']];
						$result_list[$key]['answer_info'] = $last_answers[$data['item_id']] ?? false;

						if($result_list[$key]['answer_info']){
							$result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$data['item_id']]['uid']];
							//$result_list[$key]['img_list'] =  HtmlHelper::parseImg($last_answers[$data['item_id']]['content']);
                            $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($last_answers[$data['item_id']]['id'],'answer',$uid);
						}else{
							//$result_list[$key]['img_list'] =HtmlHelper::parseImg($question_infos[$data['item_id']]['detail']);
                            $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'question',$data['item_id']);
						}
						$result_list[$key]['vote_value'] = Vote::getVoteByType($data['item_id'],'question',$uid);
                        $result_list[$key]['detail'] = $result_list[$key]['answer_info'] ?  '<a href="'.$result_list[$key]['answer_info']['user_info']['url'].'" class="uk-username" >'.$result_list[$key]['answer_info']['user_info']['nick_name'].'</a> :'.str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['answer_info']['content'])),0,150) : str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);
                        //$result_list[$key]['is_favorite'] = Favorite::checkFavorite($uid,'question',$data['item_id']);
						$result_list[$key]['item_type'] = 'question';
						$result_list[$key]['topics'] = $topic_infos['question'][$data['item_id']] ?? [];
						$result_list[$key]['user_info'] = $users_info[$data['uid']];
					}
					break;
				case 'article':
					if($article_infos)
					{
						$result_list[$key] = $article_infos[$data['item_id']];
						//$result_list[$key]['img_list'] = HtmlHelper::parseImg($article_infos[$data['item_id']]['message']);
						$result_list[$key]['message'] = str_cut(strip_tags($result_list[$key]['message']),0,100);
						$result_list[$key]['item_type'] = 'article';
                        //$result_list[$key]['is_favorite'] = Favorite::checkFavorite($uid,'article',$data['item_id']);
                        //$result_list[$key]['is_report'] = Report::getReportInfo($data['item_id'],'article',$uid);
						$result_list[$key]['topics'] = $topic_infos['article'][$data['item_id']] ?? [];
						$result_list[$key]['user_info'] = $users_info[$data['uid']];
						//$result_list[$key]['column_info'] = $article_infos[$data['item_id']]['column_id'] ? Column::where(['verify'=>1])->column('name,cover,uid,post_count,join_count') : false;
						$result_list[$key]['vote_value'] = Vote::getVoteByType($data['item_id'],'article',$uid);
					}
					break;
			}
		}
		return $result_list;
	}

	//更新关联表
	public static function updatePostRelation($item_id,$item_type,$data)
    {
		return db('post_relation')->where(['item_id'=>$item_id,'item_type'=>$item_type])->update($data);
	}
}