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

    /**
     * 专栏首页
     * @return mixed
     */
	public function index()
	{
		if($this->isMobile && get_setting('mobile_enable') )
		{
			$this->redirect(url('wap/column/lists'));
		}
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','new');
		$data = ColumnModel::getColumnListByPage($this->user_id,$sort,$page);
		$this->assign($data);
		$this->assign('sort',$sort);
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
			$id = $this->request->post('id');

			/*if(!$this->request->checkToken())
			{
				$this->error('请勿重复提交');
			}*/

			if(!$name){
				$this->error('专栏标题不能为空');
			}

			if(!$description){
				$this->error('专栏描述不能为空');
			}

            $verify = $this->user_info['group_id']=1 || $this->user_info['group_id']==2 ? 1 :0;
            $id= ColumnModel::applyColumn($this->user_id,$name,$description,$cover,$id,$verify);
            $this->success($verify ? '申请成功' : '申请成功,请等待管理员审核','ask/column/detail?id='.$id);
		}

		if($id = $this->request->param('id'))
        {
            $column_info = db('column')->where(['uid'=>$this->user_id,'id'=>$id])->find();
            if(!$column_info)
            {
                $this->error('专栏信息不存在');
            }
            $this->assign('info',$column_info);
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
		$focus = ColumnModel::checkFocus($column_id,$this->user_id);
		$sort = $this->request->param('sort','new');
		if(!$column_info['verify'] && $this->user_id!=$column_info['uid'])
		{
			$this->error('该专栏不存在或审核中，暂时无法访问');
		}

		$where[] = ['column_id','=',$column_id];

		$_list = db('article')->where($where)->order(['sort'=> 'desc'])->paginate(10);
        $page = $_list->render();
        $_list = $_list->all();
		foreach ($_list as $key => $val)
		{
			$_list[$key]['user_info'] = Users::getUserInfo($val['uid'], true);
			$_list[$key]['message'] = str_cut(strip_tags($val['message']), 0, 120);
			$_list[$key]['img_list'] =HtmlHelper::parseImg($val['message']);
			$_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'article',$this->user_id);
		}

		$this->assign('column_info', $column_info);
		$this->assign('_list', $_list);
		$this->assign('focus', $focus);
		$this->assign('page',$page);
		$this->assign('sort',$sort);
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


	/**
	 * 我的专栏
	 * @return mixed
	 */
	public function my()
	{
		if($this->isMobile)
		{
			$this->redirect(url('wap/column/my'));
		}

		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','new');
		$verify =  $this->request->param('verify',1);
		$data = ColumnModel::getMyColumnList($this->user_id,$sort,$verify,$page);
		$this->assign($data);
		$this->assign([
		    'sort'=>$sort,
            'verify'=>$verify
        ]);
		return $this->fetch();
	}
}