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
use app\common\library\helper\HtmlHelper;
use app\ask\model\Vote;
use app\common\model\Users;
use think\App;
use app\ask\model\Column as ColumnModel;
use app\ask\model\Article;

class Column extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ColumnModel();
	}

	//专栏首页
	public function index()
	{
		$list = $this->model->where(['verify'=>1])->order(['sort'=>'DESC'])->page(1,10)->select();
		$sort = $this->request->param('sort','new');
		$this->assign('sort', $sort);
		$this->assign('list',$list);
		return $this->fetch();
	}

	//专栏列表
	public function lists()
	{
		if(!$this->isMobile)
		{
			$this->redirect(url('ask/column/index'));
		}

		return $this->fetch();
	}

	/**
	 * 申请专栏
	 */
	public function apply()
	{
		if($this->request->isPost())
		{
			$name = $this->request->post('name');
			$description = $this->request->post('description');
			$cover = $this->request->post('cover');
			if(!$this->request->checkToken())
			{
				$this->error('请勿重复提交');
			}

			if(!$name){
				$this->error('专栏标题不能为空');
			}

			if(!$description){
				$this->error('专栏描述不能为空');
			}

			if($id= ColumnModel::applyColumn($this->user_id,$name,$description,$cover))
			{
				// TODO 是否需要审核
				$this->success('申请成功','ask/column/detail?id='.$id);
			}
		}
		return $this->fetch();
	}

	/**
	 * 专栏详情
	 */
	public function detail()
	{
		$column_id = $this->request->param('id');
		$column_info = ColumnModel::where(['id'=>$column_id])->find();
		$column_info['user_info'] = Users::getUserInfo($column_info['uid']);
		if(!$column_info['verify'] && $this->user_id!=$column_info['uid'])
		{
			$this->error('该专栏不存在或审核中，暂时无法访问');
		}

		$where[] = ['column_id','=',$column_id];

		$_list = Article::where($where)->order(['sort'=> 'desc'])->paginate(10);

		if($_list)
		{
			foreach ($_list as $key => $val) {
				$_list[$key]['user_info'] = Users::getUserInfo($val['uid'], true);
				$_list[$key]['message'] = str_cut(strip_tags($val['message']), 0, 120);
				$_list[$key]['img_list'] =HtmlHelper::parseImg($val['message']);
				$_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'article',$this->user_id);
			}
		}

		$this->assign('column_info', $column_info);
		$this->assign('list', $_list);
		$this->assign('page',$_list->render());

		return $this->fetch();
	}

	/**
	 * 管理专栏
	 * @return mixed
	 */
	public function manager()
	{
		return $this->fetch();
	}

	//我的专栏
	public function my()
	{
		return $this->fetch();
	}

	//ajax专栏列表
	public function ajax_list()
	{
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','new');
		$data = ColumnModel::getColumnListByPage($this->user_id,$sort,$page);
		$this->assign($data);
		return $this->fetch();
	}
}