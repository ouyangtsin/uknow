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
use app\common\model\Users;
use think\App;
use WordAnalysis\Analysis;
use app\ask\model\Question as QuestionModel;
use app\ask\model\Answer;

/**
 * 问答模块
 * Class Question
 * @package app\ask\wap
 */
class Question extends Frontend
{
	protected $needLogin = ['publish','answer'];

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
		$hot_topic = \app\ask\model\Topic::where([])->order('discuss desc')->page(1,10)->select();
		$hot_user = Users::getUserList([],'',null,1,3);

		$this->assign('hot_topic',$hot_topic);
		$this->assign('hot_user',$hot_user['list']);
		$this->assign('sort', $sort);
		return $this->fetch();
	}

	/**
	 * 发起问题/编辑问题
	 */
	public function publish()
	{
		if(!intval($this->user_info['permission']['publish_question_enable']) && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
		{
			$this->error('您没有发布问题的权限');
		}

		if ($this->request->isPost())
		{
			$postData = $this->request->post();
			$postData['uid'] = $this->user_id;
			if (!$this->request->checkToken()) {
				$this->error('请勿重复提交');
			}

			$postData['is_anonymous'] = isset($postData['is_anonymous']) ?$postData['is_anonymous'] : 0;
			//验证
			//$this->validate($postData,\app\ask\validate\Question::class);

			//需要审核
			if($this->user_info['permission']['publish_question_approval'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2){
				unset($postData['__token__']);
				Approval::saveApproval('question',$postData,$this->user_id);
				$this->error('发表成功,请等待管理员审核', 'member/index/index?uid=' . $this->user_id.'&type=question');
			}

			if ($id = QuestionModel::saveQuestion($this->user_id, $postData))
			{
				$this->success('发表成功', 'ask/question/detail?id=' . $id);
			}
		}

		$question_id = $this->request->param('id');
		if($question_id)
		{
			$question_info = QuestionModel::getQuestionInfo($question_id);
			$question_info['topics'] = \app\ask\model\Topic::getTopicByItemType('question',$question_id);
			$question_info['topic_select'] = $question_info['topics'] ?  implode(',',array_column($question_info['topics'],'id')) : '';
		}else{
			$question_info = null;
		}
		$this->assign('question_info',$question_info);
		return $this->fetch();
	}

	//问题详情页
	public function detail()
	{
		$question_id = $this->request->param('id/i', 0);
		$answer_id = $this->request->param('aid/i', 0);
		QuestionModel::updateQuestionViews($question_id,$this->user_id);

		$question_info = QuestionModel::getQuestionInfo($question_id);
		if (!$question_info || !$question_info['status']) {
			$this->error('问题不存在或已被删除');
		}
		$question_info['user_info'] = Users::getUserInfo($question_info['uid']);
		// 获取话题
		$question_info['topics'] = \app\ask\model\Topic::getTopicByItemType('question',$question_info['id']);
		$question_info['topic_select'] = $question_info['topics'] ?  implode(',',array_column($question_info['topics'],'id')) : '';
		$this->assign([
			'question_info' => $question_info,
			'answer_id' => $answer_id,
		]);

		//自动提取内容关键词
		$keywords = Analysis::getKeywords($question_info['detail'], 5);
		$this->TDK($question_info['title'], $keywords, str_cut(strip_tags($question_info['detail']),0,200));
		return $this->fetch();
	}

	//问题评论
	public function comments()
	{
		$this->view->engine()->layout(false);
		$data = $this->request->param();
		$list = QuestionModel::getQuestionComments($data['item_id'],$data['page']);
		$this->assign('list', $list);
		return $this->fetch();
	}

	//保存问题评论
	public function save_comment()
	{
		if($this->request->isPost())
		{
			$data = $this->request->post();
			$data['uid'] = $this->user_id;
			$ret = QuestionModel::saveComments($data);
			if ($ret) {
				$this->success('评论成功');
			}
			$this->error('评论失败');
		}
	}

	//删除问题评论
	public function delete_comment()
	{
		$item = $this->request->post();
		$ret = $this->model->delete_comment($item);
		if ($ret) {
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	//邀请回答问题
	public function get_invites() {
		$page = $this->request->param('page', 1);
		$name = $this->request->param('name', '');
		$where[] = ['uid', '>', 0];
		if (isset($name)) {
			$where[] = ['user_name', 'like', "%" . $name . "%"];
		}

		$data = $this->model->getInvites($where, $page);
		$this->view->engine()->layout(false);
		$this->assign('list', $data);

		return $this->fetch('question/invites');
	}

	/*保存回答*/
	public function save_answer() {
		$data = $this->request->post();
		$arr['question_id'] = $data['question_id'];
		$arr['content'] = $data['content'];
		$arr['uid'] = $this->user_id;
		$ret = Answer::saveAnswer($arr);
		if ($ret['code']) {
			$this->success($ret['msg']);
		} else {
			$this->error($ret['msg']);
		}
	}

	public function answer_comments()
	{
		$this->view->engine()->layout(false);
		$data = $this->request->param();
		$list = Answer::getAnswerComments($data);
		$this->assign('list', $list);
		return $this->fetch('comment/index');
	}

	public function answer()
	{
		$question_id = $this->request->param('question_id',0);
		$question_info = QuestionModel::getQuestionInfo($question_id);
		if(!$question_id || !$question_info )
		{
			$this->error('问题不存在');
		}
		$this->assign('question_info',$question_info);
		return $this->fetch();
	}

	public function answers() {
		$this->view->engine()->layout(false);
		$data['page'] = $this->request->param('page', 1);
		$data['question_id'] = $this->request->param('question_id');
		$data['answer_id'] = $this->request->param('answer_id', 0);
		$data['limit'] = 10;
		$answer = Answer::getAnswerByQid($data);
		$this->assign($answer);
		return $this->fetch();
	}
}