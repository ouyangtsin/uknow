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
use app\common\library\helper\LogHelper;
use app\common\logic\common\FocusLogic;
use app\common\model\Users;
use think\App;

/**
 * 用户模块
 * Class People
 * @package app\ask\controller
 */
class Index extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new Users();
	}

	public function index()
	{
	    $url = $this->request->param('name');
        if(!$url)
        {
            $this->error('访问页面不存在');
        }
        $uid = db('users')->whereRaw('url_token = "'.$url.'" OR user_name="'.$url.'"')->value('uid');
        if(!$uid)
        {
            $this->error('用户不存在');
        }

        $user =Users::getUserInfo((int)$uid);

        if(!$user || $user['status']===2)
        {
            $this->error('当前用户不存在');
        }

        Users::updateQuestionViews($uid,$this->user_id);
        $user['draft_count'] = db('draft')->where(['uid'=> (int)$uid])->count();
        $user['favorite_count'] = db('favorite')->where(['uid'=> (int)$uid])->count();
        $user['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user['uid']);

        $type = $this->request->param('type','dynamic');
        $this->assign('type',$type);

        $this->assign('question_count',LogHelper::getActionLogCount('publish_question',$user['uid'],$this->user_id));
        $this->assign('answer_count',LogHelper::getActionLogCount('publish_answer',$user['uid'],$this->user_id));
        $this->assign('article_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('favorite_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('user',$user);

       /* $action = [];
        switch ($type)
        {
            case 'dynamic':
                $action=[
                    'publish_question',
                    'publish_article',
                    'publish_answer',
                    'agree_question',
                    'agree_article',
                    'agree_answer',
                    'focus_question',
                    'modify_answer',
                    'modify_question',
                    'modify_article'
                ];
                break;

            case 'question':
                $action=[
                    'publish_question',
                ];
                break;

            case 'article':
                $action=[
                    'publish_article',
                ];
                break;

            case 'answer':
                $action=[
                    'publish_answer',
                ];
                break;
        }

        $data = LogHelper::getActionLogList($action,$user['uid'],$this->user_id,request()->param('page'),10,'uk-index-main');
        $this->assign($data);*/

        $this->TDK($user['user_name'].'的主页');
        return $this->fetch();
	}

	/**
	 * 用户列表
	 */
	public function lists()
	{
		$param = $this->request->param();
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','default');
        if($page>5)
        {
            $page=5;
        }
		$data = Users::getUserList($sort,$param,$page,12,$this->user_id);
		$this->assign($data);

		$this->assign('sort',$sort);
		$this->TDK('用户列表');
		return $this->fetch();
	}

	//邀请码管理
	public function invite()
    {
        if(!$this->user_id)
        {
            $this->redirect('/');
        }

        $invite_user_count = db('invite')->where(['uid'=>$this->user_id,'active_status'=>1])->count();
        $type = $this->request->param('type','code');
        $this->assign(
            [
                'invite_user_count'=>$invite_user_count,
                'type'=>$type
            ]
        );
        return $this->fetch();
    }

    public function get_hot_user()
    {
        $page = $this->request->param('page',1);
        $list = Users::getHotUsers($this->user_id,[],['power'=>'desc','fans_count'=>'desc'],4,$page);
        $this->assign($list);
        $this->result(['html'=>$this->fetch(),'total'=>$list['last_page']]);
    }
}