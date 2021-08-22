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

namespace app\common\logic\search;
use app\common\logic\search\driver\RegexpSearch;

/**
 * 搜索引擎逻辑层
 * Class Search
 * @package app\common\logic
 */
class Search
{
	//搜索引擎
	protected $handler;

    /**
     * 架构函数
     * @access public
     * @param null $search
     */
	public function __construct($search=null)
	{
		$this->handler = $search ? $search : new RegexpSearch() ;
	}

	public function search($keywords,$type,$time,$uid,$sort,$page=1,$per_page=10)
	{
		if (!$keywords) return false;
		$keywords = is_array($keywords) ? $keywords : explode('.',$keywords);
		$searchResult = array();
        $where = [];
        $order = [];
		switch ($type)
		{
            case 'all':
                $searchResult = $this->handler->searchMixed($keywords,$where,$uid,$order,$page,$per_page);
                break;
			case 'question':
				$searchResult = $this->handler->searchQuestion($keywords,$where,$uid,$order,$page,$per_page);
				break;
			case 'article':
				$searchResult = $this->handler->searchArticle($keywords,$where,$uid,$order,$page,$per_page);
				break;
			case 'user':
				$searchResult = $this->handler->searchUser($keywords,$where,$uid,$order,$page,$per_page);
				break;
            case 'topic':
                $searchResult = $this->handler->searchTopic($keywords,$where,$uid,$order,$page,$per_page);
                break;
		}
		return $searchResult;
	}
}