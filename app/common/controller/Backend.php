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
use app\common\library\builder\FormBuilder;
use app\common\library\builder\MakeBuilder;
use app\common\library\builder\TableBuilder;
use app\common\library\helper\AdminNotifyHelper;
use app\common\library\helper\AuthHelper;
use app\common\library\helper\DataHelper;
use app\common\library\helper\RenderHelper;
use app\common\model\Users;
use app\common\traits\Curd;
use app\admin\model\AdminLog;
use app\common\traits\Jump;
use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 后台控制器基类
 * Class Backend
 * @package app\common\controller
 */
class Backend
{
    use Curd;
    use Jump;
    /**
     * Request实例
     */
    protected $request;

    /**
     * 应用实例
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 当前模型
     * @Model
     * @var object
     */
    protected $model;

    /**
     * javascript渲染文件列表
     * @var array
     */
    public static $scriptFile=[];

    /**
     * css渲染文件列表
     * @var array
     */
    public static $styleFile=[];
    /**
     * 模板布局, false取消
     * @var string|bool
     */
    protected $layout = 'default';
    protected $user_info;
    protected $user_id;
    protected $view;
	protected $auth;
    /**
     * 无需鉴权的方法
     * @var array
     */
	protected $noNeedRight=[];

    /**
     * 无需登录的方法
     * @var array
     */
	protected $noNeedLogin=[];

	protected $table;

	protected $validate;

    /**
     * 表格构造器
     * @var TableBuilder
     */
	protected $tableBuilder;

    /**
     * 数据处理构造器
     * @var MakeBuilder
     */
	protected $makeBuilder;

    /**
     * 表单构造器
     * @var FormBuilder
     */
	protected $formBuilder;

    /**
     * 忽略显示字段
     * @var array
     */
	protected $exceptFields=[];

    /**
     * 列表搜索字段
     * @var array
     */
	protected $searchList=[];

    /**
     * 表格顶部按钮
     * @var string[]
     */
	protected $right_buttons = ['edit', 'delete'];

    /**
     * 表格操作按钮
     * @var string[]
     */
	protected $top_buttons = ['add','delete','export'];

	/**
	 * 构造方法
	 * Backend constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->view = $this->app->view;
        $this->layout && $this->view->engine()->layout($this->layout);
        $this->auth = AuthHelper::instance();
        $this->tableBuilder = TableBuilder::getInstance();
        $this->formBuilder = FormBuilder::getInstance();
        $this->makeBuilder = MakeBuilder::getInstance();

        if ($login_user_info = session('admin_user_info')) {
            $this->user_info = Users::getUserInfo($login_user_info['uid']);
        }
        $this->user_id = $this->user_info ? $this->user_info['uid'] : 0;

        $module = $this->app->http->getName();
        $actionName = $this->request->action();

        //检验权限地址
        $checkPath = $module.'/'.$this->request->controller() . '/' . strtolower($actionName);

        if ($this->request->plugin) {
            $checkPath = $this->request->plugin . '/' . $this->request->controller() . '/' . strtolower($actionName);
        }

        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //检测是否登录
            if (!$this->user_id && !session('login_uid')) {
                hook('admin_no_login', $this);
                $this->loading(request()->domain().'/member/account/login');
            }

            if (!$this->user_id && session('login_uid')) {
                $this->loading('admin/index/login');
            }

            // 判断是否需要验证权限
            // 判断控制器和方法判断是否有对应权限
            if (!$this->auth->match($this->noNeedRight) && !$this->auth->check($checkPath, $this->user_id)) {
                hook('admin_no_permission', $this);
                $this->error('您没有访问权限', '/');
            }
        }

        // 进行操作日志的记录
        AdminLog::record();

        //渲染菜单
        if ($this->user_id)
        {
            $_menuData = $this->auth->getTreeMenu();
            $this->assign($_menuData);
            $breadCrumb = DataHelper::formatBreadCrumb($this->auth->getBreadCrumb());
            // 菜单
            $this->assign(['breadCrumb' => $breadCrumb]);
        }

		//是否是ajax请求
        $this->assign('_ajax',$this->request->param('_ajax',0));
		//全局用户信息
		$this->assign('user_info',$this->user_info);
		$this->assign('user_id',$this->user_id);
		// 控制器初始化
		$this->initialize();
	}

	/**
	 * 后台初始化控制器
	 */
	public function initialize()
	{
        $this->assign('notify_count',AdminNotifyHelper::getNotifyCount());
        $this->assign('notify_list',AdminNotifyHelper::getNotifyTextList());
		$this->TDK();
		$this->script();
		$this->style();
        $return_url = $_SERVER['HTTP_REFERER'] ?? '/';
        session('return_url',base64_encode($return_url));
	}


    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], $batch = false)
    {
        try {
            if (is_array($validate)) {
                $v = new Validate();
                $v->rule($validate);
            } else {
                if (strpos($validate, '.')) {
                    // 支持场景
                    [$validate, $scene] = explode('.', $validate);
                }
                $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
                $v     = new $class();
                if (!empty($scene)) {
                    $v->scene($scene);
                }
            }

            $v->message($message);

            // 是否批量验证
            if ($batch || $this->batchValidate) {
                $v->batch(true);
            }
            return $v->failException(true)->check($data);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return true;
    }

    /**
     * 模板变量赋值
     * @param string|array $name 模板变量
     * @param mixed $value 变量值
     * @return mixed
     */
    public function assign($name, $value = null)
    {
        return $this->view->assign($name, $value);
    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string $template
     * @param array $vars
     * @return mixed
     */
    public function fetch($template = '', $vars = [])
    {
        return $this->view->fetch($template, $vars);
    }

    /**
     * 加载js
     * @param array $script
     */
    public function script($script=[]): void
    {
        self::$scriptFile = array_merge(self::$scriptFile,$script);
        $scriptFile =$script ?  RenderHelper::script(self::$scriptFile) : '';
        $this->assign('_script',$scriptFile);
    }

    /**
     * 加载样式文件
     * @param array $style
     */
    public function style($style=[]): void
    {
        self::$styleFile = array_merge(self::$styleFile,$style);
        $styleFile = $style ? RenderHelper::style(self::$styleFile) : '';
        $this->assign('_style',$styleFile);
    }

    /**
     * TDK渲染
     * @param string $title
     * @param string $keywords
     * @param string $description
     */
    public function TDK($title='',$keywords='',$description=''): void
    {
        $tdk = array(
            '_page_title' =>$title ? $title .'-'.get_setting('site_name'): get_setting('site_name').' 一款基于TP6开发的社交化知识付费问答系统，打造私有社交',
            '_page_keywords' => $keywords ?: 'UKnowing',
            '_page_description' => $description ?: get_setting('site_description'),
        );
        $this->assign($tdk);
    }
}