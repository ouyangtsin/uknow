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
use think\App;
use app\ask\model\Topic as TopicModel;

/**
 * 话题模块
 * Class Topic
 * @package app\ask\controller
 */
class Topic extends Frontend
{
	public function __construct(App $app) {
		parent::__construct($app);
		$this->model = new TopicModel;
	}

	/**
	 * 话题列表
	 */
	public function index()
	{
		$pid = $this->request->param('pid');
		$page = $this->request->param('page');
		$where = ['pid'=> 0];

		$parent =db('topic')
            ->where($where)
            ->order('discuss desc')
            ->select()
            ->toArray();

		$list = db('topic')
            ->where(['pid'=>$pid])
            ->order('discuss desc')
            ->paginate(
            [
                'list_rows'=> 10,
                'page' => $page,
                'query'=>request()->param()
            ]
        );

		$this->assign('pid',$pid);
		$this->assign('parent', $parent);
        $this->assign('page', $list->render());
		$this->assign('list', $list->all());
		return $this->fetch();
	}

	/**
	 * 话题详情
	 */
	public function detail()
	{
		$topic_id = $this->request->param('id', 0);
		$type = $this->request->param('type','question');
		$sort = $this->request->param('sort','all');
		if (!$topic_info = $this->model->where(['id' => $topic_id])->find()) {
			$this->error('话题不存在');
		}

		$focus_user = $this->model->getTopicFocusUser($topic_id);
		$topic_info['description'] = str_cut(htmlspecialchars_decode(strip_tags($topic_info['description'])),0,100);
		$topic_info['is_focus'] = db('topic_focus')->where([['uid','=',$this->user_id], ['topic_id','=',intval($topic_id)]])->find();

		$this->assign('result',$this->model->getTopicPostCountResult($topic_id));
		$this->assign('focus_user',$focus_user);
		$this->assign('type',$type);
		$this->assign('sort',$sort);
		$this->assign('topic_info', $topic_info);
		return $this->fetch();
	}

	//编辑话题
	public function manager()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			if(!$this->model->updateTopic($postData,$postData['topic_id']))
			{
				$this->error('更新失败');
			}
			$this->success('更新成功');
		}

		$topic_id = $this->request->param('id', 0);
		if (!$info = $this->model->where(['id' => $topic_id])->find()) {
			$this->error('话题不存在');
		}

		$this->assign('info',$info);
		return $this->fetch();
	}

	/**
	 * 获取话题列表
	 */
	public function get_topic() {
		$page = $this->request->param('pageNumber', 1);
		$size = $this->request->param('pageSize', 5);
		$q_word = $this->request->param('q_word');
		$searchKey = $this->request->param('searchKey');
		$searchValue = $this->request->param('searchValue');
		$where[] = ['id', '>', 0];
		if (isset($q_word) and (array_filter($q_word))) {
			$where[] = ['title', 'like', "%" . $q_word[0] . "%"];
		}
		if (isset($searchKey)) {
			$where[] = [$searchKey, 'in', $searchValue];
		}
		$data = $this->model->getTopic($where, $page, $size);
		return json($data);
	}

	//获取话题ajax列表
	public function get_ajax_topic_list()
	{
		$pid = $this->request->param('pid');
		$where = array();
		if($pid) $where=['pid'=>$pid];
		$page = $this->request->param('page');
		$data = TopicModel::getAjaxTopicList($where,$this->user_id,$page);
		$this->assign($data);
		return $this->fetch();
	}
}