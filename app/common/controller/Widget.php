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
use app\common\library\helper\ThemeHelper;
use app\common\model\Users;
use app\common\traits\Jump;
use think\App;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Request;
use think\Validate;

/**
 * 控制器基础类
 */
class Widget
{
	use Jump;
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
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
	protected $settings;
	protected $user_info;
	protected $user_id;
	protected $view;
	protected $theme;
	protected $module;

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
        $this->theme =ThemeHelper::instance(ENTRANCE=='wap'?'wap':'ask')->getDefaultTheme();
        $this->user_id = (int)session('login_uid');
        $this->user_info = $this->user_id ? Users::getUserInfo($this->user_id) : [];
	    $this->module = app('http')->getName();
		$config = ['view_path' =>  './templates'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR.$this->theme. DIRECTORY_SEPARATOR.ENTRANCE.DIRECTORY_SEPARATOR.'html'.DIRECTORY_SEPARATOR.'widget'.DIRECTORY_SEPARATOR];
        $this->view->config($config);
	    $this->view->engine()->layout(false);
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
				    list($validate, $scene) = explode('.', $validate);
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
}
