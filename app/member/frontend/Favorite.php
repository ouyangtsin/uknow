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
use app\common\logic\common\FocusLogic;
use app\common\model\Favorite as FavoriteModel;
use think\App;

class Favorite extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
		$this->model = new FavoriteModel();
	}

	//收藏标签
	public function index()
	{
		$type = $this->request->param('type','my');
		$page = $this->request->param('page',1);
		$data = FavoriteModel::getFavoriteTags($this->user_id,$page);

		$this->assign($data);
		$this->assign('type',$type);
		return $this->fetch();
	}

	//收藏详情
	public function detail()
	{
		$id = $this->request->param('id',0);
		$page = $this->request->param('page',1);

		$info = db('favorite_tag')->where(['id'=>$id])->find();
		$info['focus'] = FocusLogic::checkUserIsFocus($this->user_id,'favorite',$info['id']);
		$data = FavoriteModel::getFavoriteListByTagId($this->user_id,$info['id'],$page);

		$this->assign($data);
		$this->assign($info);
		return $this->fetch();
	}

	//删除标签
	public function delete()
	{
		$id = $this->request->param('id',0);
		if($this->model->where(['id'=>$id])->delete())
		{
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	public function save_favorite()
    {
        if($this->request->isPost())
        {
            $is_public = $this->request->post('is_public',0);
            $title = $this->request->post('title','');
            if(!FavoriteModel::saveFavoriteTag($this->user_id, $title,$is_public))
            {
                $this->error('创建失败');
            }
            $this->success('创建成功');
        }
    }
}