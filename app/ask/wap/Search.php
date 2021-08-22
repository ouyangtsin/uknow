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

/**
 * 公用搜索模块
 * Class Search
 * @package app\ask\controller
 */
class Search extends Frontend
{
	protected $handle;
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->handle = new \app\common\logic\search\Search();
	}

	public function index()
	{
		$type = $this->request->param('type','question');
		$sort = $this->request->param('sort','all');
		$keywords = $this->request->param('keywords','');
		$list = $this->handle->search($keywords,$type,$this->user_id,$sort);
		$this->assign('type',$type);
		$this->assign('keywords',urlencode($keywords));
		$this->assign('sort',$sort);
		$this->assign('result',[]);
		$this->assign('list',$list);
		return $this->fetch();
	}

	//头部搜索
	public function header_list()
	{
		$keywords = $this->request->param('keywords');
		$limit = $this->request->param('limit',5);
		$list = $this->handle->search($keywords,$this->user_id);
	}
}