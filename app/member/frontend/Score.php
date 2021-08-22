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


namespace app\member\frontend;
use app\common\controller\Frontend;
use app\common\model\Score as ScoreModel;

class Score extends Frontend
{
    protected $needLogin=['index'];

	public function index()
	{
		$type = $this->request->param('type','log');
		$page = $this->request->param('page',1);
		$where=['uid'=>$this->user_id];
		$data = ScoreModel::getScoreList($where,$page,10,'uk-index-main');
		$this->assign($data);
		$this->assign('type',$type);
		return $this->fetch();
	}
}