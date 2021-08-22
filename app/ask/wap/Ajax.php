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
use app\common\model\PostRelation;
use app\common\model\Users;
use app\ask\model\Category;
use app\ask\model\Vote;

class Ajax extends Frontend
{
	/**
	 * 首页内容列表渲染
	 * @return mixed
	 */
	public function explore_list()
	{
		$item_type = $this->request->param('type');
		$sort = $this->request->param('sort');
		$topic_ids = $this->request->param('topic_id');
		$category_id = $this->request->param('category_id');
		$page = $this->request->param('page',1);
		$uid = $this->request->param('uid',0);

		$data = PostRelation::getPostRelationList($this->user_id,$item_type,$sort,$topic_ids,$category_id,$page,10,$uid);
		$this->assign($data);
		return $this->fetch();
	}

	//投票操作
	public function set_vote()
	{
		$item_id = $this->request->post('item_id');
		$item_type = $this->request->post('item_type');
		$vote_value = $this->request->post('vote_value');
		$result = Vote::saveVote($this->user_id,$item_id,$item_type,$vote_value);
		$this->result($result,1000);
	}

	//获取点赞用户列表
	public function agree_users()
	{
		$this->layout = false;
		$item_id = $this->request->param('item_id',0);
		$item_type = $this->request->param('item_type',0);
		$user_list = Vote::getVoteUserByType($item_id ,$item_type,$this->user_id);
		$this->assign('user_list',$user_list);
		return $this->fetch();
	}

	/*获取用户ajax列表*/
	public function user_list()
	{
		$param = $this->request->param();
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','default');
		$data = Users::getUserList(['status'=>1],$sort,$param,$page,10);
		$this->assign($data);
		$this->assign('sort',$sort);
		return $this->fetch();
	}

	//获取分类
	public function category_list()
	{
		$type = $this->request->param('type');
		$html = '<option value="0" >选择分类</option>';
		if($type)
		{
			$category_list = Category::getCategoryListByType($type);
			if($category_list)
			{
				foreach ($category_list as $key =>$val)
				{
					$html .= '<option value="'.$val['id'].'" >'.$val['title'].'</option>';
				}
			}
		}
		return $html;
	}

}