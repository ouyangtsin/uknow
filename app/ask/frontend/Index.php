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
use app\common\model\PostRelation;

class Index extends Frontend
{
	public function index()
	{
	    //首页自定义钩子
	    hook('home_index');
		$sort = $this->request->param('sort','new');
        $category = $this->request->param('category_id',0);
        $set_top_list = PostRelation::getPostTopList($this->user_id,null, $category);
		$this->assign(
		    [
		        'sort'=> $sort,
                'category'=>$category,
                'top_list'=>$set_top_list,
                'current'=>$this->request->param('page',1)
            ]
        );
        $this->assign('category_list', \app\ask\model\Category::getCategoryListByType());
        $this->assign('links',db('links')->where('status',1)->select()->toArray());
		return $this->fetch();
	}
}