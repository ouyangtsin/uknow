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
use app\common\library\helper\PayHelper;
use app\common\library\helper\PowerHelper;
use app\common\library\helper\RandomHelper;
use app\common\library\helper\RuleHelper;
use app\common\logic\common\FocusLogic;
use app\common\model\Approval;
use app\common\model\PostRelation;
use app\common\model\Users;
use app\common\model\Common;
use app\ask\model\Topic;
use app\ask\model\Vote;
use think\App;
use app\ask\model\Report;
use WordAnalysis\Analysis;
use app\ask\model\Question as QuestionModel;
use app\ask\model\Answer;

/**
 * 问答模块
 * Class Question
 * @package app\ask\controller
 */
class Question extends Frontend
{
	protected $needLogin = ['publish','delete_answer'];
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new QuestionModel();
	}

	/**
	 * 问题首页
	 */
	public function index()
	{
        $sort = $this->request->param('sort','new');
        $category = $this->request->param('category_id',0);
        $set_top_list = PostRelation::getPostTopList($this->user_id,'question', $category);
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
	 * 发起问题/编辑问题
	 */
	public function publish()
	{
		if(!(int)$this->user_info['permission']['publish_question_enable'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
		{
			$this->error('您没有发布问题的权限');
		}

		if ($this->request->isPost())
		{
            $postData = $this->request->post();
            unset($postData['__token__']);
			$postData['uid'] = $this->user_id;
            $postData['question_type'] = $postData['question_type'] ?? 'normal';
			/*问题发起钩子*/
            hook('publish_question_post',$postData);

            //验证用户积分是否满足积分操作条件
            if($postData['question_type']=='normal' && !LogHelper::checkUserScore('publish_question',$this->user_id) && isset($postData['id']))
            {
                $this->error('您的积分不足,无法发起问题');
            }

			$postData['is_anonymous'] = $postData['is_anonymous'] ?? 0;

			if(!$postData['title'])
            {
                $this->error('请填写问题标题');
            }

            if($this->settings['enable_category'] && !$postData['category_id'])
            {
                $this->error('请选择问题分类');
            }

            if($postData['question_type']=='reward')
            {
                if(!$postData['reward_money'])
                {
                    $this->error('请填写悬赏金额');
                }

                if((strtotime($postData['reward_day'])+60*60*24)<=(strtotime(date("Y-m-d",time()))+2*60*60*24))
                {
                    $this->error('悬赏时间至少为一天');
                }
            }

            /*if (!$this->request->checkToken())
            {
                $this->error('请勿重复提交');
            }*/

			//发起需要审核
			if($postData['question_type']=='normal' && $this->user_info['permission']['publish_question_approval'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
			{
				Approval::saveApproval('question',$postData,$this->user_id);
				$this->error('发表成功,请等待管理员审核', (string)url('member/index/index?uid=' . $this->user_id.'&type=publish_question'));
			}

            //修改需要审核
            if($postData['question_type']=='normal' && $this->user_info['permission']['modify_question_approval'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2 && isset($postData['id']))
            {
                Approval::saveApproval('modify_question',$postData,$this->user_id);
                $this->error('修改成功,请等待管理员审核', (string)url('member/index/index?uid=' . $this->user_id.'&type=publish_question'));
            }

            $postData['status'] = $postData['question_type']=='reward' ? 0 : 1;
			if ($id = QuestionModel::saveQuestion($this->user_id, $postData))
			{
			    if($postData['question_type']=='reward')
                {
                    $out_trade_no = RandomHelper::alnum(16);
                    $options = [
                        'title'=>'悬赏问题',
                        'product_id'=>$id,
                        'relation_type'=>'question',
                        'order_type'=>'reward',
                        'amount'=>$postData['reward_money'],
                        'out_trade_no'=>$out_trade_no,
                        'relation_id'=>$id
                    ];
                    $this->result($options,99,'发表成功');
                }
				$this->success('发表成功', (string)url('ask/question/detail?id=' . $id));
			}
		}

		$question_id = $this->request->param('id',0);
        $draft_info = \app\common\model\Draft::getDraftByItemID($this->user_id,'question',$question_id);
		if($question_id)
		{
			$question_info = QuestionModel::getQuestionInfo($question_id);
			if($question_info['question_type']=='reward')
            {
                $this->error('悬赏问题不可修改');
            }
            $question_info['topics'] = Topic::getTopicByItemType('question',$question_info['id']);
            if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $question_info = $draft_info['data'];
                $question_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
            }
		}else{
			$question_info = array();
			if($topic_id = $this->request->param('topic_id'))
            {
                $question_info['topics'] = db('topic')->where('id', $topic_id)->column('id,title');
            }
			if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $question_info = $draft_info['data'];
                $question_info['topics'] = $draft_info['data']['topics'] ? Topic::getTopicByIds($draft_info['data']['topics']) : [];
            }
            unset($question_info['id']);
		}

        /*问题发起钩子*/
        hook('publish_question_page',$question_info);

		$this->assign('question_info',$question_info);
		$this->assign('category_list', \app\ask\model\Category::getCategoryListByType());
		return $this->fetch();
	}

    /**
     * 问题详情页
     * @param $id
     * @return mixed
     */
	public function detail($id)
	{
		$question_id = (int)$id;
		$answer_id = (int)input('answer');
		//更新问题浏览
		QuestionModel::updateQuestionViews($question_id,$this->user_id);
		$question_info = QuestionModel::getQuestionInfo($question_id);
		if (!$question_info || $question_info['status']===0) {
			$this->error('问题不存在或已被删除');
		}

        //更新问题热度值
        RuleHelper::calcQuestionPopularValue($question_id);

		//问题用户信息
		$question_info['user_info'] = Users::getUserInfo($question_info['uid']);
		$question_info['user_focus'] = (bool)Users::checkFocus($this->user_id, $question_info['uid']);

		// 获取话题
		$question_info['topics'] = Topic::getTopicByItemType('question',$question_info['id']);
        $question_info['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'question', $question_info['id']) ? 1 : 0;
        //获取相关问题
        $relation_question = QuestionModel::getRelationQuestion($question_id);
		//是否举报
		$checkReport=Report::getReportInfo($question_id,'question',$this->user_id)?1:0;
        //是否点赞
        $question_info['vote_value'] = Vote::getVoteByType($question_id,'question',$this->user_id);
		//是否收藏
		$checkFavorite=Common::checkFavorite(['uid'=>$this->user_id,'item_id'=>$question_id,'item_type'=>'question'])?1:0;

        $recommend_post=[];
		if($question_info['topics'])
        {
            $recommend_post = Topic::getRecommendPost($question_info['id'],'question',array_column($question_info['topics'], 'id'),$this->user_id);
        }

        //获取推荐内容
		$this->assign([
			'question_info' => $question_info,
			'answer_id' => $answer_id,
			'checkReport' => $checkReport,
			'checkFavorite' => $checkFavorite,
            'relation_question'=>$relation_question,
            'recommend_post'=>$recommend_post,
            'best_answer_count'=>db('answer')->where(['question_id'=>$question_id,'is_best'=>1])->count() ? 1 : 0
		]);
        $this->assign('publish_question_count',LogHelper::getActionLogCount('publish_question',$question_info['uid'],$this->user_id));
        $this->assign('publish_answer_count',LogHelper::getActionLogCount('publish_answer',$question_info['uid'],$this->user_id));
        $this->assign('publish_article_count',LogHelper::getActionLogCount('publish_article',$question_info['uid'],$this->user_id));

        //回答
        $page = $this->request->param('page', 1);
        $sort = $this->request->param('sort','new');
        if($sort=='new')
        {
            $order = ['is_best'=>'DESC','create_time'=>'DESC'];
        }else{
            $order = ['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'];
        }

        //TODO 不感兴趣的回答排除操作
        $answer = Answer::getAnswerByQuestionId($question_id,$answer_id,$page,15,$order);

        foreach ($answer['data'] as $key=>$val)
        {
            $answer['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$this->user_id);
            $answer['data'][$key]['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$val['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
            $answer['data'][$key]['has_uninterested'] = db('uninterested')->where(['item_id'=>$val['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
        }

        $this->assign($answer);
        $this->assign('sort',$sort);

        $seo_title = $question_info['seo_title'] ? : $question_info['title'];
        $seo_keywords = $question_info['seo_keywords'] ? : Analysis::getKeywords($question_info['detail'], 4);
        $seo_description = $question_info['seo_description'] ? : str_cut(strip_tags($question_info['detail']),0,200);
		$this->TDK($seo_title, $seo_keywords, $seo_description);

		if($question_info['question_type']=='reward')
        {
            return $this->fetch('reward');
        }else{
            return $this->fetch();
        }
	}

    /**
     * 保存问题评论
     */
	public function save_comment()
    {
		if($this->request->isPost())
		{
			$data = $this->request->post();
            $data['uid'] = $this->user_id;
            $data['user_name'] = $this->user_info['user_name'];
            $question_info=QuestionModel::getQuestionInfo($data['question_id'],'id,uid,title');
            $data['question_info']=$question_info;
            if (!$question_info) {
                $this->error('问题不存在或已被删除');
            }
            $ret = QuestionModel::saveComments($data);
            if ($ret) {
                $this->success('评论成功');
            }
            $this->error('评论失败');
        }
    }

    /**
     * 保存回答评论
     */
    public function save_answer_comment()
    {
        if($this->request->isPost())
        {
            $data = $this->request->post();
            $data['uid'] = $this->user_id;
            $data['user_name'] = $this->user_info['user_name'];
            $answer=Answer::getAnswerInfoById($data['answer_id']);
			if (!$answer) {
			     $this->error('回答不存在');
            }
            $data['question_info'] = QuestionModel::getQuestionInfo($answer['question_id'],'id,uid,title');

            $ret = Answer::saveComments($data);
            if ($ret) {
                $this->success('评论成功');
            }
            $this->error('评论失败');
		}
	}

    /**
     * 删除问题评论
     */
	public function delete_comment()
    {
		$comment_id = $this->request->param('id');
		$ret = QuestionModel::deleteComment($comment_id,$this->user_id);
		if ($ret) {
			$this->success('删除成功');
		}
		$this->error(QuestionModel::getError());
	}

    /**
     * 删除回答评论
     */
	public function delete_answer_comment()
    {
		$comment_id = $this->request->param('id');
		$ret = Answer::deleteComment($comment_id,$this->user_id);
		if ($ret) {
			$this->success('删除成功');
		}
		$this->error(Answer::getError());
	}

    /**
     * 获取邀请
     * @param $question_id
     * @return mixed
     */
	public function invite($question_id)
	{
		if($this->request->isPost())
		{
			$name = $this->request->post('name', '');
			$page = $this->request->param('page',1);
			$where[] = ['uid', '<>', $this->user_id];
			if (isset($name)) {
				$where[] = ['user_name', 'like', "%" . $name . "%"];
			}
			$data = QuestionModel::getQuestionInvite($this->user_id,$where,$question_id, (int)$page);
			$this->assign('question_id',$question_id);
			$this->assign($data);
		}
		$this->assign('question_id',$question_id);
		return $this->fetch();
	}

    /**
     * 保存问题邀请
     * @param $question_id
     */
	public function save_question_invite($question_id)
    {
		$invite_uid = $this->request->post('uid');
		$has_invite = $this->request->post('has_invite');

		if($invite_uid==$this->user_id)
        {
            $this->error('不可以邀请自己回答问题');
        }

		if(db('answer')->where(['question_id'=> $question_id, 'uid'=> $invite_uid])->value('id'))
        {
            $this->error('该用户已回答过该问题,不能继续邀请');
        }

        //验证用户积分是否满足积分操作条件
        if(!LogHelper::checkUserScore('invite_user_answer_question',$this->user_id))
        {
            $this->error('您的积分不足,无法邀请用户回答问题');
        }
        if(!$question_info=QuestionModel::getQuestionInfo($question_id,'id,uid,title'))
        {
            $this->error('问题不存在或已被删除');
        }
		if(!QuestionModel::saveQuestionInvite($question_info,$this->user_id,$invite_uid))
		{
			$this->error('该用户已邀请过啦','',[]);
		}
		$this->success('操作成功','',['invite'=> (int)!$has_invite]);
	}

    /**
     * 回答编辑
     * @return mixed
     */
	public function editor()
	{
		$question_id = $this->request->param('question_id',0);
		$answer_id = $this->request->param('answer_id',0);
		if($answer_id)
		{
			$answer_info = Answer::getAnswerInfoById($answer_id);
			$question_id = $answer_info['question_id'];
			$this->assign('answer_info',$answer_info);
		}

		$this->assign('question_id',$question_id);
		$this->assign('answer_id',$answer_id);
		return $this->fetch();
	}

    /**
     * 保存回答
     */
	public function save_answer()
    {
        $this->view->engine()->layout(false);
		$data = $this->request->post();

        if(!(int)$this->user_info['permission']['publish_answer_enable'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->error('您没有发布回答的权限');
        }

        if(!$this->request->checkToken())
        {
            $this->error('请不要重复提交');
        }

        unset($data['__token__']);

        //验证用户积分是否满足积分操作条件
        if(!LogHelper::checkUserScore('publish_question_answer',$this->user_id) && !$data['id'])
        {
            $this->error('您的积分不足,无法回答问题');
        }

        $question_info = QuestionModel::getQuestionInfo($data['question_id']);
        if(!$question_info)
        {
            $this->error('问题不存在', '/');
        }

        //发起回答审核
        if($this->user_info['permission']['publish_answer_approval'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            Approval::saveApproval('answer',$data,$this->user_id);
            $this->error('发起成功,请等待管理员审核', 'ask/question/detail?id=' . $data['question_id']);
        }

        //修改回答审核
        if($this->user_info['permission']['modify_answer_approval'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2 && isset($data['id']))
        {
            Approval::saveApproval('modify_answer',$data,$this->user_id);
            $this->error('修改成功,请等待管理员审核', 'ask/question/detail?id=' . $data['question_id']);
        }

		$data['uid'] = $this->user_id;
        $data['is_anonymous'] = $data['is_anonymous'] ?? 0;

		if ($ret = Answer::saveAnswer($data))
		{
            $ret['update'] = 0;
            $ret['question_info'] = $question_info;
            $ret['best_answer_count']=db('answer')->where(['question_id'=>$data['question_id'],'is_best'=>1])->count() ? 1 : 0;
            $ret['info']['vote_value'] = Vote::getVoteByType($ret['info']['id'],'answer',$this->user_id);
            $ret['info']['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$ret['info']['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
            $ret['info']['has_uninterested'] = db('uninterested')->where(['item_id'=>$ret['info']['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
		    if($data['id'])
            {
                $ret['update'] =1;
                $this->result(['answer_count'=>$ret['answer_count'],'id'=>$ret['info']['id'],'html'=>$this->fetch('single_answer',$ret)],1,'更新成功');
            }
            $this->result(['answer_count'=>$ret['answer_count'],'id'=>$ret['info']['id'],'html'=>$this->fetch('single_answer',$ret)],2,'回复成功');
		}
        $this->result([],0,Answer::getError());
	}

	//问题回答评论
	public function answer_comments()
	{
		$this->view->engine()->layout(false);
		$data = $this->request->param();
		$list = Answer::getAnswerComments($data);
		$this->assign('list', $list);
		return $this->fetch();
	}

	//删除回复
	public function delete_answer()
    {
		$answer_id = $this->request->param('answer_id',0);
		$answer_info = Answer::getAnswerInfoById($answer_id);
		
		if(!$answer_info){
			$this->error('回答不存在');
		}

		if(!$answer_info['status']){
			$this->error('回答已被删除');
		}

		if ($answer_info['uid']!=$this->user_id && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
		{
			$this->error('您没有删除回答的权限');
		}

		if(!Answer::deleteAnswer($answer_id))
		{
			$this->error(Answer::getError());
		}

		$this->success('删除成功','ask/question/detail?id='.$answer_info['question_id']);
	}

	//获取问题回答列表
	public function answers() 
	{
		$data['page'] = $this->request->param('page', 1);
		$data['question_id'] = $this->request->param('question_id');
		$data['answer_id'] = $this->request->param('answer_id', 0);
		$data['limit'] = 10;
		$sort = $this->request->param('sort','new');
		if($sort=='new')
        {
            $order = ['is_best'=>'DESC','create_time'=>'DESC'];
        }else{
            $order = ['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'];
        }

		$answer = Answer::getAnswerByQid($data,$order);

		foreach ($answer['data'] as $key=>$val)
        {
            $answer['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$this->user_id);
        }
		$this->result([
		    'total'=>$answer['total'],
            'last_page'=>$answer['last_page'],
            'html'=>$this->fetch('',['list'=>$answer['data']]),
            'page_render'=>$answer['page_render']
        ],1);
	}

	public function comment_vote(){
		$item_id=$this->request->param('item_id', 0);
		if(!$item_id){
			$this->result([], 0, '参数错误');
		}
		$comment=QuestionModel::comment($item_id);
		if(!$comment){
			$this->result([], 0, '数据错误');
		}
		$ret=QuestionModel::comment_vote($item_id,$this->user_id,$comment);
		if($ret){
			$this->result([], 1, '操作成功');
		}else{
			$this->result([], 0, '操作失败');
		}
	}

	//问题管理操作
	public function manager()
    {
        $question_id = $this->request->param('id');
        $type=$this->request->param('type');

        if(!$question_id && !$type)
        {
            $this->error('请求参数不正确');
        }

        if(QuestionModel::manger($question_id,$type))
        {
            $this->success('操作成功');
        }

        $this->error(QuestionModel::getError());
    }

    //删除问题
    public function remove_question()
    {
        $id = $this->request->param('id');
        $question_info = QuestionModel::getQuestionInfo($id);

        if ($this->user_id !== $question_info['uid'] && $this->user_info['group_id'] !== 1 && $this->user_info['group_id'] !== 2) {
            $this->error('您没有删除问题的权限');
        }

        if (!QuestionModel::removeQuestion($id)) {
            $this->error('删除问题失败');
        }
        $this->success('删除问题成功');
    }

    /**
     * 设置最佳回答
     */
    public function set_answer_best()
    {
        if( $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->error('您没有操作权限');
        }

        $answer_id = $this->request->param('answer_id',0);
        $answer_info = Answer::getAnswerInfoById($answer_id);

        if(!$answer_info)
        {
            $this->error('回答不存在');
        }

        if($answer_info['uid']==$this->user_id &&  $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->error('不可设置自己的回答为最佳答案');
        }

        if(db('answer')->where(['question_id'=>$answer_info['question_id'],'is_best'=>1])->count())
        {
            $this->error('最多只可设置一个最佳答案');
        }

        if(db('question')->where(['id'=>$answer_info['question_id']])->update(['best_answer'=>$answer_info['id']]))
        {
            db('answer')->where(['id'=>$answer_info['id']])->update(['is_best'=>1,'best_uid'=>$this->user_id,'best_time'=>time()]);
            //添加积分记录
            score_log('set_best_answer',$answer_id,'answer',$answer_info['uid']);
            $this->success('设置最佳答案成功',(string)url('detail',['id'=>$answer_info['question_id']]));
        }
        $this->error('设置最佳答案失败');
    }

    /**
     * 喜欢回答
     */
    public function thanks()
    {
        if(!$this->user_id){
            $this->error('请先登录');
        }
        $id = $this->request->param('id',0);
        if(db('answer_thanks')->where(['answer_id'=>$id,'uid'=>$this->user_id])->value('id'))
        {
            $this->error('您已感谢该回答');
        }

        if(db('answer_thanks')->insert(['uid'=>$this->user_id,'answer_id'=>$id,'create_time'=>time()]))
        {
            $this->success('感谢成功');
        }
        $this->error('感谢失败');
    }
}