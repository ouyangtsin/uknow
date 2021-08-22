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
use app\common\library\helper\LogHelper;
use app\common\model\BaseModel;
use app\common\model\PostRelation;
use app\common\model\Users;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\DbManager;
use think\facade\Db;
use WordAnalysis\Analysis;

/**
 * 文章模型
 * Class Article
 * @package app\ask\model
 */
class Article extends BaseModel
{
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;

	/**
	 * 文章详情获取器
	 * @param $value
	 * @return string
	 */
	public function getMessageAttr($value) {
		return htmlspecialchars_decode($value);
	}

	/**
	 * 获取文章详情
	 * @param $id
	 * @return mixed
	 */
	public static function getArticleInfo($id)
	{
        return self::getById($id);
	}

    /**
     * 根据文章id获取文章列表
     * @param $article_ids
     * @return array|false
     */
	public static function getArticleByIds($article_ids)
	{
		if (!is_array($article_ids) OR sizeof($article_ids) == 0) return false;
		array_walk_recursive($article_ids, 'intval');
		$articles_list = db('article')->where(['status'=>1])->whereIn('id',implode(',', $article_ids))->select()->toArray();
		$result = array();
		if ($articles_list)
		{
			foreach ($articles_list AS $key => $val)
			{
			    $val['message'] = htmlspecialchars_decode($val['message']);
				$result[$val['id']] = $val;
			}
		}
		return $result;
	}

    /**
     * 保存文章
     * @param $uid
     * @param $postData
     * @return false|int|string
     */
	public static function saveArticle($uid,$postData)
	{
		$column_id = $postData['column_id'] ?? 0;
		$data = array(
			'uid' => (int)$uid,
			'title' => $postData['title'],
			'message' => ImageHelper::fetchContentImagesToLocal($postData['message'],'article',$uid),
			'category_id' => $postData['category_id'] ?? 0,
			'column_id' => (int)$column_id,
			'cover' => $postData['cover'],
			'create_time'=>time(),
			'update_time'=>time(),
			'status' =>(isset($postData['wait_time']) && $postData['wait_time']) ? 3 : 1
		);

		$article_id = db('article')->insertGetId($data);

		if(!$article_id) {
            return false;
        }
        //添加行为日志
        LogHelper::addActionLog('publish_article','article',$article_id,$uid);
        //添加积分记录
        LogHelper::addScoreLog('publish_article',$article_id,'article',$uid);
		//添加话题关联
        if(isset($postData['topics']))
        {
            $topics = is_array($postData['topics']) ? array_filter($postData['topics']) : explode(',',trim($postData['topics']));
            if (!empty($topics))
            {
                foreach ($topics as $key => $title)
                {
                    if($title)
                    {
                        $topic_id = Topic::saveTopic($title, $uid, true);
                        Topic::saveTopicRelation($uid, $topic_id, $article_id, 'article');
						Topic::save_log(['uid'=>$uid,'type'=>3,'title' => $postData['title'],'article_id'=>$article_id, 'topic_id'=>$topic_id,'topic_title'=>$title]);
                    }
                }
            }
        }

		//更新用户文章数
		$article_count = self::where(['uid'=>$uid,'status'=>1])->count();
		Users::updateUserFiled($uid,['article_count'=>$article_count]);

		//更新专栏文章数量
		if($column_id)
		{
			$column_post_count = self::where(['column_id'=>$column_id,'status'=>1])->count();
			Column::update(['post_count'=>$column_post_count],['id'=>$column_id]);
		}

		//TODO 写入操作历史记录

		//加入内容聚合表
		PostRelation::savePostRelation($article_id,'article');
		return $article_id;
	}

    /**
     * 更新文章
     * @param $uid
     * @param $postData
     * @return mixed
     */
	public static function updateArticle($uid,$postData)
	{
		$column_id = $postData['column_id'] ?? 0;
		$data = array(
			'uid' => (int)$uid,
			'title' => $postData['title'],
			'message' => ImageHelper::fetchContentImagesToLocal($postData['message'],'article',$uid),
			'category_id' => $postData['category_id'] ?? 0,
			'column_id' => (int)$column_id,
			'cover' => $postData['cover'],
			'update_time'=>time(),
			'status' =>1
		);

		self::update($data,['id'=>$postData['id']]);

        //添加行为日志
        LogHelper::addActionLog('modify_article','article',$postData['id'],$uid);

		if(isset($postData['topics'])){
            $topics = is_array($postData['topics']) ? $postData['topics'] : explode(',',$postData['topics']);
            //更新话题关联
            if($topics && db('topic_relation')->where(['item_type' => 'article', 'item_id' => $postData['id']])->delete()) {
                foreach (array_unique($topics) as $key => $title)
                {
                    $topic_id = Topic::saveTopic($title, $uid);
                    Topic::saveTopicRelation($uid, $topic_id, $postData['id'], 'article');
					Topic::save_log(['uid'=>$uid,'type'=>3,'title' => $postData['title'],'article_id'=>$postData['id'], 'topic_id'=>$topic_id,'topic_title'=>$title]);
                }
            }
        }

		return $postData['id'];
	}

    /**
     * 删除文章
     * @param $id
     * @return bool
     */
	public static function removeArticle($id): bool
    {
		if(!$article_info = self::getArticleInfo($id))
		{
			return false;
		}

		if(!self::update(['status'=>0],['id'=>$id]))
		{
			return false;
		}

		//更新专栏文章数
		if($article_info['column_id'])
		{
			$post_count = db('article')->where(['column_id'=>$article_info['column_id'],'status'=>1])->count();
			Column::update(['post_count'=>$post_count],['id'=>$article_info['column_id']]);
		}

		//更新首页表
		PostRelation::updatePostRelation($id,'article',['status'=>0]);
		return true;
	}

	/**
	 * 更新文章浏览量
	 * @param $article_id
	 * @param int $uid
	 * @return mixed
	 */
	public static function updateArticleViews($article_id,$uid=0)
	{
		$cache_key = md5('cache_article_'.$article_id.'_'.$uid);
		$cache_result = cache($cache_key);
		if($cache_result) return true;
		cache($cache_key,$cache_key,['expire'=>60]);
		return db('article')->where(['id'=>$article_id])->inc('view_count')->update();
	}

    /**
     * 获取文章详情
     * @param $id
     * @param string $field
     * @return mixed
     */
    public static function getArticleInfoField($id,$field="*")
    {
        $article_info=cache('article_'.$id);
        if(!$article_info){
            $article_info=db('article')->field($field)->find($id);
            cache('article_'.$id,$article_info);
        }
        return $article_info;
    }

    /**
     * 保存文章评论
     */
	public static function saveArticleComment($article,$message,$user_info,$at_uid='',$pid=0)
	{
        $parseAtUser = json_decode($at_uid,true);
        $comment_pid= 0;
        if($pid)
        {
            $comment_pid = db('article_comment')->where('id',$pid)->value('pid');
        }
        $pid = $comment_pid ? : $pid;

		$data = array(
			'uid' => (int)$user_info['uid'],
			'message' => $message,
			'at_uid' => $at_uid,
			'article_id' => $article['id'],
			'create_time' => time(),
            'pid' => $at_uid ? $pid : 0
		);
		$comment_id = db('article_comment')->insertGetId($data);
		if(!$comment_id) {
            return false;
        }

        if($at_uid){
            send_notify($user_info['uid'],$parseAtUser['uid'],'TYPE_PEOPLE_AT_ME',$user_info['user_name'].'在文章评论中@了您',$article['id'],['item_type'=>'article_comment','message'=>$user_info['user_name'].'在文章评论中@了您','item_id'=>$article['id'],'title'=>$article['title'],'url'=>url('article/detail',['id'=>$article['id']])]);
        }
        send_notify($user_info['uid'],$article['uid'],'TYPE_ANSWER_COMMENT',$user_info['user_name'].'评论了您的文章',$article['id'],['item_type'=>'article_comment','message'=>$user_info['user_name'].'评论了您的文章','item_id'=>$article['id'],'title'=>$article['title'],'url'=>url('article/detail',['id'=>$article['id']])]);
		//更新文章评论数
		$comment_count = db('article_comment')->where(['article_id'=>$article['id'],'status'=>1])->count();
		self::update(['comment_count'=>$comment_count],['id'=>$article['id']]);

		//更新首页数据
		PostRelation::updatePostRelation($article['id'],'article',['answer_count'=>$comment_count]);
		//TODO 记录

		//TODO 给文章发起者发送新评论的通知
		return ['comment_id'=>$comment_id,'comment_count'=>$comment_count];
	}

    /**
     * 获取文章评论列表
     * @param $article_id
     * @param null $order
     * @param int $page
     * @param int $per_page
     * @return array
     */
	public static function getArticleCommentList($article_id,$order=null,$page=1,$per_page=10)
    {
		$map = ['article_id'=>$article_id,'status'=>1];
        $sort = [];
		if($order)
        {
            switch ($order)
            {
                case 'hot':
                    $sort['agree_count'] = 'DESC';
                    break;

                default :
                    $sort['create_time'] = 'DESC';
            }
        }

		$comments = db('article_comment')
            ->where($map)
            ->order($sort)
            ->page($page,$per_page)
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'query'=>request()->param()
                ]
            );
		$pageRender = $comments->render();
        $comments = $comments->toArray();
		foreach ($comments['data'] as $key => $val)
		{
			$comments['data'][$key]['user_info'] = Users::getUserInfo($val['uid'],'user_name,nick_name,avatar,uid');
		}
        $comments['page_render'] = $pageRender;
		return $comments;
	}

    /**
     * 删除评论
     * @param $id
     */
	public static function deleteComment($id)
    {
		$comment = db('article_comment')->find($id);
		Db::transaction(function ()use($id,$comment)
        {
		    db('article_comment')->where(['id'=>$id])->delete();
			db('article')->where('id', $comment['article_id'])->dec('comment_count')->update();

            $comment_count = db('article_comment')->where(['article_id_id'=>$comment['article_id'],'status'=>1])->count();
            //更新首页数据
            PostRelation::updatePostRelation($comment['article_id'],'article',['answer_count'=>$comment_count]);
		});
	}

    //获取相关文章
	public static function getRelationArticleList($article_id,$limit=5)
    {
        if(!$article_id) {
            return false;
        }
        $article_info = self::getArticleInfo($article_id);
        $keywords = Analysis::getKeywords($article_info['title']);
        $keywords = explode(',', $keywords);
        $articleIds = db('article')
            ->whereRaw("status=1 and (title regexp'".implode('|',$keywords)."')")
            ->order('view_count','DESC')
            ->select()
            ->toArray();
        if($articleIds = array_column($articleIds,'id'))
        {
            unset($articleIds[array_search($article_id, $articleIds, true)]);
            return self::getArticleByIds($articleIds);
        }
        return false;
    }
}