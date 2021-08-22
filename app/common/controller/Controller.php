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

use app\common\library\helper\RenderHelper;
use app\common\model\Users;
use app\common\traits\Jump;
use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class Controller
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
	protected $user_info;
	protected $user_id;
	protected $view;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->view = $this->app->view;
        $this->user_id = (int)session('login_uid');
        $this->user_info = $this->user_id ? Users::getUserInfo($this->user_id) : [];
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

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
    public function fetch(string $template = '', array $vars = [])
    {
        return $this->view->fetch($template, $vars);
    }

	/**
	 * 加载js
	 * @param array $script
	 */
	public function script(array $script=[]): void
    {
		self::$scriptFile = array_merge(self::$scriptFile,$script);
		$scriptFile =$script ?  RenderHelper::script(self::$scriptFile) : '';
		$this->view->assign('_script',$scriptFile);
	}

	/**
	 * 加载样式文件
	 * @param array $style
	 */
	public function style(array $style=[])
    {
		self::$styleFile = array_merge(self::$styleFile,$style);
		$styleFile = $style ? RenderHelper::style(self::$styleFile) : '';
        $this->view->assign('_style',$styleFile);
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
			'_page_title' =>$title ? $title .'-'.get_setting('site_name'): get_setting('site_name').' 一款基于TP6开发的社交化知识付费问答系统，打造私有社交',
			'_page_keywords' => $keywords ?: 'UKnowing',
			'_page_description' => $description ?: get_setting('site_description'),
		);
        $this->view->assign($tdk);
	}
}
