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

namespace app\ask\frontend;

use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\common\library\helper\RuleHelper;
use app\common\model\Approval;
use app\common\model\Favorite;
use app\common\model\PostRelation;
use app\common\model\Users;
use app\ask\model\Article as ArticleModel;
use app\ask\model\Report;
use app\ask\model\Topic;
use app\ask\model\Vote;
use think\App;
use think\facade\Cache;
use tools\Tree;
use WordAnalysis\Analysis;

class Article extends Frontend
{
	protected $needLogin = ['publish', 'save_comment', 'remove_article'];
	public function __construct(App $app) {
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
        $category = $this->request->param('category_id',0);
        $set_top_list = PostRelation::getPostTopList($this->user_id,'article', $category);
        $this->assign(
            [
                'sort'=> $sort,
                'category'=>$category,
                'top_list'=>$set_top_list
            ]
        );
        $this->assign('category_list', \app\ask\model\Category::getCategoryListByType());
		return $this->fetch();
	}

	/**
	 * 发表文章
	 */
	public function publish()
    {
		if ($this->request->isPost()) {
			$postData = $this->request->post();

            if(!$postData['title'])
            {
                $this->error('请填写文章标题');
            }

            if(!$postData['message'])
            {
                $this->error('请填写文章正文');
            }

            if($this->settings['enable_category'] && !$postData['category_id'])
            {
                $this->error('请选择文章分类');
            }

            //验证用户积分是否满足积分操作条件
            if(!LogHelper::checkUserScore('publish_article',$this->user_id))
            {
                $this->error('您的积分不足,无法发表文章');
            }

            //表单验证
            /*if (!$this->request->checkToken()) {
                $this->error('请勿重复提交');
            }*/

			//需要审核
			if ($this->user_info['permission']['publish_question_approval'] && $this->user_info['group_id'] !== 1 && $this->user_info['group_id'] !== 2)
			{
				unset($postData['__token__']);
				Approval::saveApproval('article', $postData, $this->user_id);
				$this->error('发表成功,请等待管理员审核', 'member/index/index?uid=' . $this->user_id . '&type=article');
			}

			//正常发表
			if (isset($postData['id']) && $postData['id'])
			{
				if ($id = ArticleModel::updateArticle($this->user_id, $postData)) {
					$this->success('修改成功', 'ask/article/detail?id=' . $id);
				}
				$this->error('修改文章失败');
			} else if ($id = ArticleModel::saveArticle($this->user_id, $postData))
			{
				$this->success('发表成功', 'ask/article/detail?id=' . $id);
			}
		}

		$article_id = $this->request->param('id',0);
        $draft_info = \app\common\model\Draft::getDraftByItemID($this->user_id,'article',$article_id);

		if ($article_id) {
			$article_info = ArticleModel::getArticleInfo($article_id);
            $article_info['topics'] = Topic::getTopicByItemType('article', $article_info['id']);
            if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $article_info = $draft_info['data'];
                $article_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
            }
		} else {
			$article_info = array();
            if($topic_id = $this->request->param('topic_id'))
            {
                $article_info['topics'] = db('topic')->where('id', $topic_id)->column('id,title');
            }
            if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $article_info = $draft_info['data'];
                $article_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
            }
            unset($article_info['id']);
		}

		//从专栏进入发起文章
		$column_id = $this->request->param('column_id', 0);
		$article_info['column_id'] = $column_id;
		$this->assign('article_info', $article_info);
		$column_list = \app\ask\model\Column::getColumnByUid($this->user_id);
		$this->assign('column_list', $column_list);
		$this->assign('article_category_list', \app\ask\model\Category::getCategoryListByType());
		return $this->fetch();
	}

	/**
	 * 文章详情
	 */
	public function detail()
    {
		$id = $this->request->param('id/i', 0);
		$article_info = ArticleModel::getArticleInfo($id)->toArray();
		if (!$article_info || !$article_info['status']) {
			$this->error('文章不存在或已被删除');
		}

        ArticleModel::updateArticleViews($id, $this->user_id);

        //更新文章热度值
        RuleHelper::calcArticlePopularValue($id);

		//举报状态
		$article_info['is_report'] = Report::getReportInfo($article_info['id'], 'article', $this->user_id);
		//投票状态
		$article_info['vote_value'] = Vote::getVoteByType($article_info['id'], 'article', $this->user_id);
		//收藏状态
		$article_info['is_favorite'] = Favorite::checkFavorite($this->user_id, 'article', $article_info['id']);
		//用户信息
		$article_info['user_info'] = Users::getUserInfo($article_info['uid']);

		$article_info['topics'] = Topic::getTopicByItemType('article', $article_info['id']);
		$article_info['topic_select'] = $article_info['topics'] ? implode(',', array_column($article_info['topics'], 'id')) : '';
		$this->assign('article_info', $article_info);

		//获取相关文章
        $relation_article = ArticleModel::getRelationArticleList($article_info['id']);
        $this->assign('relation_article', $relation_article);

        //获取推荐内容
        $recommend_post=[];
        if($article_info['topics'])
        {
            $recommend_post = Topic::getRecommendPost($article_info['id'],'article',array_column($article_info['topics'], 'id'),$this->user_id);
        }
        $this->assign('recommend_post', $recommend_post);

        $page = $this->request->param('page', 1);

        $sort = $this->request->param('sort', 'new');

        $comment_list = ArticleModel::getArticleCommentList($article_info['id'], $sort, intval($page));

        foreach ($comment_list['data'] as $key => $val)
        {
            $comment_list['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'], 'article_comment', $this->user_id);
        }

        $this->assign([
            'comment_list'=> Tree::toTree($comment_list['data']),
            'page_render'=> $comment_list['page_render'],
            'sort' =>$sort
        ]);

        $seo_title = $article_info['seo_title'] ? : $article_info['title'];
        $seo_keywords = $article_info['seo_keywords'] ? : Analysis::getKeywords($article_info['title'], 4);
        $seo_description = $article_info['seo_description'] ? : str_cut(strip_tags($article_info['message']),0,200);
        $this->TDK($seo_title, $seo_keywords, $seo_description);
		return $this->fetch();
	}

	/*删除文章*/
	public function remove_article()
    {
		$id = $this->request->param('id');
		$article_info = ArticleModel::getArticleInfo($id);

		if ($this->user_id !== $article_info['uid'] && $this->user_info['group_id'] !== 1 && $this->user_info['group_id'] !== 2) {
			$this->error('您没有删除文章的权限');
		}

		if (!ArticleModel::removeArticle($id)) {
			$this->error('删除文章失败');
		}

		$this->success('删除文章成功');
	}

	/**
	 * 保存文章评论
	 */
	public function save_comment()
    {
        $this->view->engine()->layout(false);
		if ($this->request->isPost())
		{
			$article_id = $this->request->post('article_id');
            $article=ArticleModel::getArticleInfoField($article_id,'id,title,uid');
			$message = $this->request->post('message');
			$at_info = htmlspecialchars_decode($this->request->post('at_info',''));
			$pid = $this->request->post('pid');

			if (!$article) {
				$this->error('文章不存在');
			}
			if (!$message) {
				$this->error('评论内容不能为空');
			}

			if (!$result = ArticleModel::saveArticleComment($article, $message, $this->user_info,$at_info,$pid))
			{
                $this->result([],0,'评论失败');
			}

			$comment_info = db('article_comment')->find($result['comment_id']);
            $comment_info['user_info'] = Users::getUserInfo($comment_info['uid'],'user_name,nick_name,avatar,uid');
            $comment_info['vote_value'] = Vote::getVoteByType($comment_info['id'], 'article_comment', $this->user_id);
            //$comment_info['at_uid']=json_decode($comment_info['at_uid'],true);
			$this->assign('comment_info',$comment_info);
            $this->result(['html'=>$this->fetch('single_comment'),'comment_count'=>$result['comment_count']],1,'评论成功');
		}
	}

    /**
     * 删除文章评论
     */
    public function remove_comment()
    {
        $id=input('id');
        $comment_info = db('article_comment')->find($id);
        if($this->user_id!=$comment_info['uid'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->result([], 0, '您没有删除评论的权限');
        }

        if(ArticleModel::deleteComment($id))
        {
            $this->result([], 1, '删除成功');
        }
        $this->result([], 0, '删除失败');
    }

	//ajax加载文章评论
	public function get_ajax_comment()
    {
		$article_id = $this->request->param('article_id', 0);
		$page = $this->request->param('page', 1);

		$comment_list = ArticleModel::getArticleCommentList($article_id, ['create_time' => 'DESC'], intval($page));
		foreach ($comment_list['data'] as $key => $val)
		{
            $comment_list['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'], 'article_comment', $this->user_id);
		}
		$this->assign('comment_list', $comment_list['data']);

        $this->layout = false;

		$this->assign('total', $comment_list['last_page']);
		return $this->fetch();
	}

	//预览文章
	public function preview()
    {
		if ($this->request->isPost())
		{
			$data = $this->request->post('data');
			unset($data['__token__'], $data['topics_text']);
			$article_info = array();
            $article_info['id'] = 0;
            $article_info['comment_count'] = 0;
            $article_info['uid'] = $this->user_id;
			if (isset($data['id']) && $data['id']) {
				$article_info = ArticleModel::getArticleInfo($data['id']);
			}
			$article_info['message'] = htmlspecialchars_decode($data['message']);
			$article_info['title'] = $data['title'];
			$article_info['agree_count'] = $article_info['agree_count'] ?? 0;
			$article_info['view_count'] = $article_info['view_count'] ?? 0;
			$article_info['create_time'] = $article_info['create_time'] ?? time();
			if (isset($data['topics'])) {
				$article_info['topics'] = Topic::getTopicByIds($data['topics']);
			}
			Cache::set('article_preview_' . $this->user_id, $article_info);
		} else {
			$article_info = Cache::get('article_preview_' . $this->user_id);
		}
		$this->assign('article_info', $article_info);
		$keywords =$article_info['message'] ? Analysis::getKeywords($article_info['message'], 5) : '';
		$this->TDK($article_info['title'], $keywords, str_cut(strip_tags($article_info['message']), 0, 200));
		return $this->fetch();
	}

	/*文章操作*/
	public function action()
    {
		$action=input('type');
		$article_id=input('article_id');
		switch ($action) {
			case 'recommend':
				$is_recommend=input('is_recommend');
				$msg=$is_recommend ? '取消成功' : '推荐成功';
				$ret=$this->model->where(['id'=>$article_id])->update(['is_recommend'=>$is_recommend ? 0 : 1]);
				if($ret != false){
                    PostRelation::updatePostRelation($article_id,'article',['is_recommend'=>$is_recommend?0:1]);
                    $this->success($msg);
				}
				break;
			case 'set_top':
				$set_top=input('set_top');
				$msg=$set_top ?  '取消成功' : '置顶成功';
				$ret=$this->model->where(['id'=>$article_id])->update(['set_top'=>$set_top ? 0 : 1,'set_top_time'=>time()]);
				if($ret != false){
                    PostRelation::updatePostRelation($article_id,'article',['set_top'=>$set_top ? 0 : 1,'set_top_time'=>$set_top ? 0 : time()]);
					$this->success($msg);
				}
				break;
		}
	}
}