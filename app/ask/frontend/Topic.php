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
use app\common\logic\common\FocusLogic;
use think\App;
use app\ask\model\Topic as TopicModel;
use think\db\exception\DbException;
use think\facade\Db;
use tools\Tree;
use WordAnalysis\Analysis;

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
     * @param string $type
     * @param int $pid
     * @return mixed
     * @throws DbException
     */
	public function index($type='focus',$pid=0)
	{
		$parent_list = $this->model->where([['pid','=',0],['status','=',1]])->order('discuss desc')->select()->toArray();
        $this->assign('parent_list', $parent_list);
        if($type=='time')
        {
            $order['discuss_update'] ='desc';
        }else{
            $order['focus'] ='desc';
        }
        $where[] = ['status','=',1];
        if($pid)
        {
            $where[] = ['pid','=',$pid];
        }

		$list = $this->model->where($where)->order('discuss desc')->paginate(12);
		$page = $list->render();

		foreach ($list->all() as $key => $value)
		{
            $list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'topic', $value['id']) ? 1 : 0;
			$list[$key]['description'] = $value['description'] ? str_cut(strip_tags(htmlspecialchars_decode($value['description'])), 0, 45) : '';
		}

		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('type',$type);
        $this->assign('pid',$pid);
		return $this->fetch();
	}

	/**
	 * 话题详情
	 */
	public function detail()
	{
		$topic_id = $this->request->param('id', 0);
		$type = $this->request->param('type','all');
		$sort = $this->request->param('sort','all');
        $topic_info = $this->model->where(['id' => $topic_id])->find();
        if (!$topic_info || $topic_info['status']!=1)
        {
            $this->error('话题不存在');
        }

		$focus_user = TopicModel::getTopicFocusUser($topic_id);
		$topic_info['description'] = $topic_info['description'] ? htmlspecialchars_decode($topic_info['description']) : '';
		$topic_info['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'topic', $topic_info['id']) ? 1 : 0;
		$this->assign('result',TopicModel::getTopicPostCountResult($topic_id));
		$this->assign('focus_user',$focus_user);
		$this->assign('type',$type);
		$this->assign('sort',$sort);
		$this->assign('topic_info', $topic_info);

        $seo_title = $topic_info['seo_title'] ? : $topic_info['title'];
        $seo_keywords = $topic_info['seo_keywords'] ? : Analysis::getKeywords($topic_info['description'], 5);
        $seo_description = $topic_info['seo_description'] ? : str_cut(strip_tags($topic_info['description']),0,200);
        $this->TDK($seo_title, $seo_keywords, $seo_description);
		return $this->fetch();
	}

	//编辑话题
	public function manager()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			$postData['uid']=$this->user_id;
			if(!TopicModel::updateTopic($postData,$postData['topic_id']))
			{
				$this->error('更新失败');
			}
			$this->success('更新成功');
		}

		$topic_id = $this->request->param('id', 0);
		if (!$info = $this->model->where(['id' => $topic_id])->find()) {
			$this->error('话题不存在');
		}
        $info['description'] = htmlspecialchars_decode($info['description']);
		$this->assign('info',$info);
		return $this->fetch();
	}

	/**
	 * 获取话题列表
	 */
	public function get_topic()
    {
        $item_type = $this->request->param('item_type');
        $item_id = $this->request->param('item_id');
		$keywords = $this->request->param('keywords');
		$where[] = ['pid', '>', 0];
		if (isset($keywords)) {
			$where[] = ['title', 'like', "%" . $keywords . "%"];
		}
        $topic_list = TopicModel::getTopic($where);
        foreach ($topic_list as $key=>$val)
        {
            $topic_list[$key]['is_checked'] = 0;
            if(TopicModel::checkTopicRelation($val['id'], $item_id, $item_type))
            {
                $topic_list[$key]['is_checked'] = 1;
            }
        }
        $this->assign('search_list',$topic_list);
        return $this->fetch('ajax/topic');
	}

    /**
     * 删除话题
     */
	public function remove_topic(): void
    {
        $id = $this->request->param('id');
        if($this->user_id && ($this->user_info['group_id']===1 || $this->user_info['group_id']===2))
        {
            TopicModel::removeTopic((int)$id);
        	$this->error('删除成功');
        }
        $this->error('您没有删除话题的权限');
    }

    /**
     * 获取话题日子
     * @return mixed
     */
    public function logs(){
        $id = $this->request->param('id');
        $list=TopicModel::getLogs((int)$id);
        $this->assign([
        	'list'=>$list,
        ]);
    	return $this->fetch();
    }

    /**
     * 获取热门话题
     */
    public function get_hot_topic()
    {
        $page = $this->request->param('page',1);
        $topic_list =TopicModel::getHotTopics($this->user_id,[],['focus'=>'desc','discuss'=>'desc'],4,$page);
        $this->assign($topic_list);
        $this->result(['html'=>$this->fetch(),'total'=>$topic_list['last_page']]);
    }

    /**
     * 创建话题
     * @return mixed
     */
    public function create()
    {
        if(!$this->user_id)
        {
            $this->error('请先登录','member/account/login');
        }
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $data['uid']=$this->user_id;
            if($this->user_info['group_id']!==1 && $this->user_info['group_id']!==2) {
                $data['status'] = 0;
            }
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功请等待审核','index');
            }
        }
        return $this->fetch();
    }
}