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
use app\common\library\helper\CheckHelper;
use app\common\library\helper\FileHelper;
use app\common\library\helper\RenderHelper;
use app\common\library\helper\ThemeHelper;
use app\common\model\Inbox as InboxModel;
use app\common\model\Nav;
use app\common\model\Users;
use app\common\traits\Jump;
use app\ask\model\Notify as NotifyModel;
use think\App;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Request;
use think\Validate;

/**
 * Class Frontend
 * @package app\common\controller
 */
class Frontend
{

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

	//需登录的方法
	protected $needLogin = [];

	protected $theme = 'default';

	//控制器名称
	protected $controller;
	//方法名称
	protected $action;
	protected $module;
	protected $isMobile;
	protected $settings;

    /**
     * 游客权限
     * @var mixed
     */
	protected $touristPermission;
	/**
	 * 构造方法
	 * Frontend constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->view = $this->app->view;
        $this->layout && $this->view->engine()->layout($this->layout);
        if($this->request->isPjax() || $this->request->param('_ajax', 0))
        {
            $this->view->engine()->layout(false);
        }

		if ($login_user_info = session('login_user_info'))
		{
			$this->user_info = Users::getUserInfo($login_user_info['uid']);
		}
		$this->user_id = $this->user_info ? $this->user_info['uid'] : 0;
        //是否是手机端
        $this->isMobile = Request::isMobile();
        $this->module = app('http')->getName();
        $this->controller = strtolower($this->request->controller());
        $this->action = $this->request->action();

        $file = new FileHelper();
        $wap_theme_path = public_path().'templates'.DS.$this->module.DS.$this->theme.DS.'wap'.DS;
		if ($this->isMobile && get_setting('mobile_enable') && ENTRANCE!=='wap' && $file->isDir($wap_theme_path)) {
		    $url = $this->request->domain().'/wap.php/'.$this->module.'/' . $this->controller . '/' . $this->action;
            $this->loading($url,1);
		}

        if(!CheckHelper::checkSiteStatus())
        {
            $this->error('网站已关闭',url('member/account/login'));
        }

        $this->touristPermission = CheckHelper::checkTouristPermission();

        if($this->touristPermission)
        {
            if(((!$this->user_info && !$this->touristPermission['visit_website']) || ($this->user_info && !$this->user_info['permission']['visit_website'])) && $this->controller!='account')
            {
                $this->error('当前站点不允许访问',url('member/account/login'));
            }
        }

        //检测用户在线状态
        CheckHelper::checkOnline();

		// 检测是否需要验证登录
        //检测是否登录
        if (AuthHelper::instance()->match($this->needLogin) && !$this->user_id) {
            hook('home_no_login', $this);
            $url = $this->request->url();
            if ($url == '/') {
                $this->redirect(url('member/account/login'));
            }
            $this->error('请先登录后进行操作', url('member/account/login'));
        }
        //获取默认模板名称
        $this->theme = ThemeHelper::instance($this->module)->getDefaultTheme()?:'default';
        $theme_path=get_setting('cdn_url',Request::domain()).'/templates/'.$this->module.'/'.$this->theme.'/'.ENTRANCE .'/static/';
        //渲染系统配置
        $this->settings = get_setting();
        //渲染默认模板配置
        $this->assign([
            'baseUrl'=> Request::domain(),
            'thisModule'       => parse_name($this->module),
            'thisController'       => parse_name($this->controller),
            'thisAction'           => $this->action,
            'thisRequest'          => parse_name("{$this->module}/{$this->controller}/{$this->action}"),
            'version'              => env('app_debug') ? time() : UK_VERSION,
            'cdnUrl' => get_setting('cdn_url',Request::domain()),
            'theme_path'=>$theme_path,
            'theme_config'=>ThemeHelper::instance($this->module)->getThemeConfigs($this->theme),
            '_ajax' =>$this->request->param('_ajax', 0),
            '_ajax_open'=>$this->request->param('_ajax_open', 0),
            '_pjax'=>$this->request->isPjax() ? 1 : 0,
            'setting'=>$this->settings,
            'nav_list'=>Nav::getNavListByType(1),
            'user_info'=>$this->user_info,
            'user_id'=>$this->user_id,
            'isMobile'=>$this->isMobile
        ]);
        $autoloadJs = file_exists(public_path().'templates/'.$this->module.'/'.$this->theme . '/'.ENTRANCE.'/static/js/autoload/'.parse_name("{$this->controller}/{$this->action}.js")) ? 1 : 0;
        $autoloadCss = file_exists(public_path().'templates/'.$this->module.'/'.$this->theme . '/'.ENTRANCE.'/static/css/autoload/'.parse_name("{$this->controller}/{$this->action}.css")) ? 1 : 0;

        // 控制器初始化
        $this->initialize();

        if($autoloadJs)
        {
            $this->script(['/templates/'.$this->module.'/'.$this->theme. '/'.ENTRANCE .'/static/js/autoload/'.$this->controller.'/'.$this->action.'.js']);
        }
        if($autoloadCss)
        {
            $this->style([
                '/templates/'.$this->module.'/'.$this->theme . '/'.ENTRANCE .'/static/css/autoload/'.$this->controller.'/'.$this->action.'.css'
            ]);
        }
	}

	public function initialize()
    {
		$this->script();
        $this->style([
            '/static/common/css/common.css',
        ]);
        $tdk_info = Nav::getNavTDK();
        $title ='';
        $keyword ='';
        $description='';
        if($tdk_info)
        {
            $title = $tdk_info['seo_title'] ?? '';
            $keyword = $tdk_info['seo_keywords'] ?? '';
            $description = $tdk_info['seo_description'] ?? '';
        }
        $this->TDK($title,$keyword,$description);
        $return_url = $_SERVER['HTTP_REFERER'] ?? '/';
        session('return_url',base64_encode($return_url));
        $this->assign('return_url',base64_decode(session('return_url')));
        //私信通知
        if($this->user_id)
        {
            if($notify_list = NotifyModel::getNotifyList($this->user_id,1,5,2))
            {
                $this->assign('notify_list', $notify_list['list']);
            }

            if($dialogList = InboxModel::getDialogListByUid($this->user_id,'(recipient_unread=1 OR sender_unread=1) AND ',1,5))
            {
                $this->assign('inbox_list',$dialogList['list']);
            }
        }
	}

	public function fetch($template = '', $vars = [])
    {
        if (!$this->request->plugin)
        {
            $view_path ='./templates/'. $this->module . DIRECTORY_SEPARATOR . $this->theme. DIRECTORY_SEPARATOR.ENTRANCE . DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR;
            $depr = Config::get('view.view_depr');
            $view_suffix = Config::get('view.view_suffix');

            //判断模板是否存在，不存在复用默认模板
            if ('think' == strtolower(Config::get('view.type')) && $this->controller && 0 !== strpos($template, '/')) {
                $template = str_replace(['/', ':'], $depr, $template);
                if ('' == $template) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $this->controller) . $depr . $this->action;
                } elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DIRECTORY_SEPARATOR, $this->controller) . $depr . $template;
                }
                if ($this->theme != 'default' && !file_exists($view_path . $template . '.' . $view_suffix)) {
                    $this->theme = 'default';
                    $theme_path=get_setting('cdn_url',Request::domain()).'/templates/'.$this->module.'/'.$this->theme .'/static/';
                    $this->assign([
                        'theme_path'=>$theme_path,
                        'theme_config'=>ThemeHelper::instance('ask')->getThemeConfigs($this->theme),
                    ]);
                }
            }
            $view_path ='./templates/'. $this->module . DIRECTORY_SEPARATOR . $this->theme . DIRECTORY_SEPARATOR.ENTRANCE .DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR;
            $this->view->config(['view_path' => $view_path]);
        }
		return $this->view->fetch($template, $vars);
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
            return $e->getMessage();
        }
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
     * 加载js
     * @param array $script
     */
    public function script($script=[])
    {
        self::$scriptFile = array_merge(self::$scriptFile,$script);
        $scriptFile =$script ?  RenderHelper::script(self::$scriptFile) : '';
        $this->assign('_script',$scriptFile);
    }

    /**
     * 加载样式文件
     * @param array $style
     */
    public function style($style=[])
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
    public function TDK($title='',$keywords='',$description='')
    {
        $tdk = array(
            '_page_title' =>$title ? $title .' - '.get_setting('site_name'): get_setting('site_name').' - '.get_setting('site_brand'),
            '_page_keywords' => $keywords ?: get_setting('site_keywords'),
            '_page_description' => $description ?: get_setting('site_description'),
        );
        $this->assign($tdk);
    }
}