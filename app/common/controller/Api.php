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
namespace app\common\controller;
use app\common\library\helper\AuthHelper;
use app\common\model\Users;
use think\App;
use think\facade\Event;

//解决跨域问题
header('Access-Control-Allow-Origin:*');//允许所有来源访问
header("Access-Control-Allow-Credentials:true");
header('Access-Control-Allow-Method:GET,POST,OPTIONS');//允许访问的方式
header("Access-Control-Allow-Headers:Content-Type,Access-Token,version");//OPTIONS请求，直接返回
if (request()->method() == "OPTIONS") {
    exit();
}

class Api extends Controller
{
	protected $auth;

	//需登录的方法
	protected $needLogin=[];
	//控制器名称
	protected $controller;
	//方法名称
	protected $action;
	/**
	 * 构造方法
	 * Frontend constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->auth = AuthHelper::instance();
        $this->user_id = session('login_uid') ? : 0;
		if($this->user_id)
		{
			$this->user_info = Users::getUserInfo($this->user_id);
		}

		$this->controller = strtolower($this->request->controller());
		$this->action = $this->request->action();

		$this->checkSiteStatus();
		// 检测是否需要验证登录
		if ($this->auth->match($this->needLogin)) {
			//检测是否登录
			if (!$this->user_id) {
				Event::trigger('home_no_login', $this);
                $this->result(['url'=>url('member/account/login')],99,'请先登录后进行操作');
			}
		}
		// 控制器初始化
		$this->initialize();
	}

	public function initialize()
	{
		$this->view->assign('user_info',$this->user_info);
		$this->view->assign('user_id',$this->user_id);
	}

	private function checkSiteStatus(): void
    {
		if(strtolower($this->controller) !== 'account' && strtolower($this->action) !== 'login' && (int)get_setting('close_site') && $this->user_info['group_id']!==1 && $this->user_info['group_id']!==2)
		{
			session('login_uid',null);
			session('login_user_info',null);
			$this->error('网站已关闭',url('member/account/login'));
		}
	}
}