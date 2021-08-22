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


namespace app\ask\wap;

use app\common\controller\Frontend;
use app\common\model\Approval;
use app\common\model\Favorite;
use app\common\model\Users;
use app\ask\model\Topic;
use app\ask\model\Report;
use app\ask\model\Vote;
use think\App;
use think\facade\Cache;
use think\facade\Db;
use WordAnalysis\Analysis;
use app\ask\model\Article as ArticleModel;

class Article extends Frontend
{
	protected $needLogin = ['publish','save_comment','remove_article'];
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ArticleModel();
	}

	/**
	 * 文章列表
	 * @return mixed
	 */
	public function index()
	{
		$sort = $this->request->param('sort','new');
		$this->assign('sort', $sort);
		$this->TDK('文章');
		return $this->fetch();
	}

	/**
	 * 发表文章
	 */
	public function publish()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			if(!$this->request->checkToken())
			{
				$this->error('请勿重复提交');
			}

			if(!$postData['title']){
				$this->error('文章标题不能为空');
			}

			if(!$postData['message']){
				$this->error('文章内容不能为空');
			}

			//需要审核
			if($this->user_info['permission']['publish_question_approval'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2){
				unset($postData['__token__']);
				Approval::saveApproval('article',$postData,$this->user_id);
				$this->error('发表成功,请等待管理员审核', 'member/index/index?uid=' . $this->user_id.'&type=article');
			}

			//正常发表
			if(isset($postData['id']) && $postData['id'])
			{
				if($id=ArticleModel::updateArticle($this->user_id,$postData))
				{
					$this->success('修改成功','ask/article/detail?id='.$id);
				}
			}else{
				if($id= ArticleModel::saveArticle($this->user_id,$postData))
				{
					$this->success('发表成功','ask/article/detail?id='.$id);
				}
			}
		}

		$article_id = $this->request->param('id');
		if($article_id)
		{
			$article_info = ArticleModel::getArticleInfo($article_id);
			$article_info['topics'] = Topic::getTopicByItemType('article',$article_id);
			$article_info['topic_select'] = $article_info['topics'] ?  implode(',',array_column($article_info['topics'],'id')) : '';
		}else{
			$article_info = null;
		}
		$this->assign('article_info', $article_info);

		//从专栏进入发起文章
		$column_id = $this->request->param('column_id',0);
		$this->assign('column_id',$column_id);

		$column_list =\app\ask\model\Column::getColumnByUid($this->user_id);
		$this->assign('column_list',$column_list);
		return $this->fetch();
	}

	/**
	 * 文章详情
	 */
	public function detail()
	{
        $id = $this->request->param('id/i', 0);
        ArticleModel::updateArticleViews($id,$this->user_id);

        $article_info = ArticleModel::getArticleInfo($id)->toArray();
        if (!$article_info || !$article_info['status']) {
            $this->error('文章不存在或已被删除');
        }

        //举报状态
        $article_info['is_report'] = Report::getReportInfo($article_info['id'],'article',$this->user_id);
        //投票状态
        $article_info['vote_value'] = Vote::getVoteByType($article_info['id'],'article',$this->user_id);
        //收藏状态
        $article_info['is_favorite'] =  Favorite::checkFavorite($this->user_id,'article',$article_info['id']);
        //用户信息
        $article_info['user_info'] = Users::getUserInfo($article_info['uid']);
        $article_info['topics'] = Topic::getTopicByItemType('article',$article_info['id']);
        $article_info['topic_select'] = $article_info['topics'] ?  implode(',',array_column($article_info['topics'],'id')) : '';

        $this->assign('article_info',$article_info);
        //自动提取内容关键词
        $keywords = Analysis::getKeywords($article_info['message'], 5);
        $this->TDK($article_info['title'], $keywords, str_cut(strip_tags($article_info['message']),0,200));
        return $this->fetch();
	}

	/*删除文章*/
	public function remove_article()
	{
		$id = $this->request->param('id');
		$article_info = ArticleModel::getArticleInfo($id);

		if($this->user_id!=$article_info['uid'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
		{
			$this->error('您没有删除文章的权限');
		}

		if(!ArticleModel::removeArticle($id))
		{
			$this->error('删除文章失败');
		}

		$this->success('删除文章成功');
	}

	/**
	 * 保存文章评论
	 */
	public function save_comment()
	{
		if($this->request->isPost())
		{
			$article_id = $this->request->post('article_id');
			$message = $this->request->post('message');
			if(!$article_id){
				$this->error('文章不存在');
			}
			if(!$message){
				$this->error('评论内容不能为空');
			}

			if($id= ArticleModel::saveArticleComment($article_id,$message,$this->user_id) )
			{
				$this->success('发表成功','ask/article/detail?id='.$article_id);
			}
		}
	}

	//ajax加载文章评论
	public function get_ajax_comment()
	{
		$this->layout = false;
		$article_id = $this->request->param('article_id',0);
		$page = $this->request->param('page',1);
		$comment_list = ArticleModel::getArticleCommentList($article_id,[],['create_time'=>'DESC'],intval($page));
		foreach ($comment_list as $key=>$val)
		{
			$comment_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'article_comment',$this->user_id);
		}
		$this->assign('comment_list',$comment_list);

		$total = Db::name('article_comment')->where(['article_id'=>$article_id,'status'=>1])->count();
		$this->assign('total',ceil($total/5));
		return $this->fetch();
	}

	//预览文章
	public function preview()
	{
		if($this->request->isPost())
		{
			$data = $this->request->post('data');
			unset($data['__token__'],$data['topics_text']);
			$article_info = array();
			if(isset($data['id']) && $data['id'])
			{
				$article_info = ArticleModel::getArticleInfo($data['id']);
			}
			$article_info['message'] = htmlspecialchars_decode($data['message']);
			$article_info['title'] = $data['title'];
			$article_info['agree_count'] = $article_info['agree_count'] ? $article_info['agree_count'] : 0;
			$article_info['view_count'] = $article_info['view_count'] ? $article_info['view_count'] : 0;
			$article_info['create_time'] = $article_info['create_time'] ? $article_info['create_time'] : time();
			$article_info['topics'] = Topic::getTopicByIds($data['topics']);

			Cache::set('article_preview_'.$this->user_id,$article_info);
		}
		$article_info = Cache::get('article_preview_'.$this->user_id);
		$this->assign('article_info',$article_info);
		$keywords = Analysis::getKeywords($article_info['message'], 5);
		$this->TDK($article_info['title'], $keywords, str_cut(strip_tags($article_info['message']),0,200));
		return $this->fetch();
	}

}