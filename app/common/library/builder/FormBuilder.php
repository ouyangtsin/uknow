<?php
namespace app\common\library\builder;
use think\facade\Request;
use think\facade\Route;
use think\facade\View;

class FormBuilder
{
    /**
     * @var string 模板路径(默认使用系统内置路径，无需设置)
     */
    private $_template = '';

    /**
     * @var array 模板变量
     */
    private $_vars = [
        'page_title'     => '',        // 页面标题
        'page_tips'      => '',        // 页面提示
        'tips_type'      => '',        // 提示类型
        'form_url'       => '',        // 表单提交地址 [默认为当前方法 + Post]
        'form_method'    => 'post',    // 表单提交方式
        'empty_tips'     => '暂无数据', // 没有表单项时的提示信息
        'btn_hide'       => [],        // 要隐藏的按钮
        'btn_title'      => [],        // 按钮标题
        'btn_extra'      => [],        // 额外按钮
        'extra_html'     => '',        // 额外HTML代码
        'extra_js'       => '',        // 额外JS代码
        'extra_css'      => '',        // 额外CSS代码
        'submit_confirm' => false,     // 提交确认
        'form_items'     => [],        // 表单项目
        'form_data'      => [],        // 表单数据
    ];

    /**
     * @var bool 是否分组数据 [分组时不再需要传递其他参数]
     */
    private $_is_group = false;

    /**
     * @var
     */
    private static $instance;

    /**
     * 获取句柄
     * @return FormBuilder
     */
    public static function getInstance(): FormBuilder
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 私有化构造函数
     */
    private function __construct()
    {
        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
        // 设置默认模版
        $this->_template = 'admin@global/form/layout';
        // 设置默认表单提交地址 [默认为当前方法 + Post]
        $this->_vars['form_url'] = Route::buildUrl(Request::action());
    }

    /**
     * 私有化clone函数
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 设置页面标题
     * @param string $title 页面标题
     * @return $this
     */
    public function setPageTitle(string $title = ''): FormBuilder
    {
        if ($title != '') {
            $this->_vars['page_title'] = trim($title);
        }
        return $this;
    }

    /**
     * 设置表单页提示信息
     * @param string $tips 提示信息
     * @param string $type 提示类型：danger,info,warning,success
     * @param string $pos  提示位置：top,search,bottom
     * @return $this
     */
    public function setPageTips(string $tips = '', string $type = 'info', string $pos = 'top'): FormBuilder
    {
        if ($tips != '') {
            $this->_vars['page_tips_' . $pos] = $tips;
            $this->_vars['tips_type']         = trim($type);
        }
        return $this;
    }

    /**
     * 设置表单提交地址
     * @param string $form_url 提交地址
     * @return $this
     */
    public function setFormUrl(string $form_url = ''): FormBuilder
    {
        if ($form_url != '') {
            $this->_vars['form_url'] = trim($form_url);
        }
        return $this;
    }

    /**
     * 设置表单提交方式
     * @param string $value 提交方式
     * @return $this
     */
    public function setFormMethod(string $value = '')
    {
        if ($value != '') {
            $this->_vars['form_method'] = $value;
        }
        return $this;
    }

    /**
     * 模板变量赋值
     * @param mixed  $name  要显示的模板变量
     * @param string $value 变量的值
     * @return $this
     */
    public function assign($name, $value = ''): FormBuilder
    {
        if (is_array($name)) {
            $this->_vars = array_merge($this->_vars, $name);
        } else {
            $this->_vars[$name] = $value;
        }
        return $this;
    }

    /**
     * 隐藏按钮
     * @param array|string $btn 要隐藏的按钮，如：['submit']，其中'submit'->确认按钮，'back'->返回按钮
     * @return $this
     */
    public function hideBtn($btn = []): FormBuilder
    {
        if (!empty($btn)) {
            $this->_vars['btn_hide'] = is_array($btn) ? $btn : explode(',', $btn);
        }
        return $this;
    }

    /**
     * 设置按钮标题
     * @param string|array $btn   按钮名 'submit' -> “提交”，'back' -> “返回”
     * @param string       $title 按钮标题
     * @return $this
     */
    public function setBtnTitle($btn = '', string $title = ''): FormBuilder
    {
        if (!empty($btn)) {
            if (is_array($btn)) {
                $this->_vars['btn_title'] = $btn;
            } else {
                $this->_vars['btn_title'][trim($btn)] = trim($title);
            }
        }
        return $this;
    }

    /**
     * 添加额外按钮
     * @param string $btn 按钮内容
     * @return $this
     */
    public function addBtn(string $btn = ''): FormBuilder
    {
        if ($btn != '') {
            $this->_vars['btn_extra'][] = $btn;
        }
        return $this;
    }

    /**
     * 设置额外HTML代码
     * @param string $extra_html 额外HTML代码
     * @param string $pos        位置 [top和bottom]
     * @return $this
     */
    public function setExtraHtml(string $extra_html = '', string $pos = ''): FormBuilder
    {
        if ($extra_html != '') {
            $pos != '' && $pos = '_' . $pos;
            $this->_vars['extra_html' . $pos] = $extra_html;
        }
        return $this;
    }

    /**
     * 设置额外JS代码
     * @param string $extra_js 额外JS代码
     * @return $this
     */
    public function setExtraJs(string $extra_js = ''): FormBuilder
    {
        if ($extra_js != '') {
            $this->_vars['extra_js'] = $extra_js;
        }
        return $this;
    }

    /**
     * 设置额外CSS代码
     * @param string $extra_css 额外CSS代码
     * @return $this
     */
    public function setExtraCss(string $extra_css = ''): FormBuilder
    {
        if ($extra_css != '') {
            $this->_vars['extra_css'] = $extra_css;
        }
        return $this;
    }

    /**
     * 设置提交表单时显示确认框
     * @return $this
     */
    public function submitConfirm(): FormBuilder
    {
        $this->_vars['submit_confirm'] = true;
        return $this;
    }

    // ===================================表单项开始===================================

    /**
     * 添加单行文本框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param array  $group       标签组，可以在文本框前后添加按钮或者文字
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addText($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'text',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多行文本框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @param int    $rows        高度（以行数计）
     * @return $this|array
     */
    public function addTextarea($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false, $rows = 3)
    {
        $item = [
            'type'        => 'textarea',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
            'rows'        => $rows,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单选
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     单选数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addRadio($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'radio',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];
        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加复选框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addCheckbox($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'checkbox',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加树形复选框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addCheckbox2($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'checkbox2',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }


    /**
     * 添加复选框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addArray($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'array',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDate($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'Y-m-d',
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($item['value'] == 'now') {
            $item['value'] = date($item['format']);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加时间
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      时间格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addTime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'time',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'H:m:s',
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($item['value'] == 'now') {
            $item['value'] = date($item['format']);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期时间
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDatetime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'Y-m-d H:m:s',
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($item['value'] == 'now') {
            $item['value'] = date($item['format']);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期范围
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDaterange($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'daterange',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'Y-m-d',
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => !empty($placeholder) ? $placeholder : '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加标签
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addTag($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'tags',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => is_array($default) ? implode(',', $default) : $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加图标
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addIcon($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'icon',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => is_array($default) ? implode(',', $default) : $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加数字输入框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $min         最小值
     * @param string $max         最大值
     * @param string $step        步进值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addNumber($name = '', $title = '', $tips = '', $default = '', $min = '', $max = '', $step = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'number',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default == '' ? 0 : $default,
            'min'         => $min,
            'max'         => $max,
            'step'        => $step,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加密码框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addPassword($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'password',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加普通下拉菜单
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     选项
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param string $placeholder 占位符
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addSelect($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'select',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请选择',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加下拉菜单select2
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     选项
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @param string $ajax_url    ajax 地址(传递时无需再传递选项值)
     * @return $this|array
     */
    public function addSelect2($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false, $ajax_url = '')
    {
        $item = [
            'type'        => 'select2',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请选择',
            'required'    => $required,
            'ajax_url'    => $ajax_url,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单图片上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addImage($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'image',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多图片上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addImages($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'images',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单文件上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addFile($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'file',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多文件上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addFiles($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'files',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加编辑器
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $height      高度
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addEditor($name = '', $title = '', $tips = '', $default = '', $height = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'editor',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'height'      => $height ?: '400',
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加按钮
     * @param string $name     按钮名称(id)
     * @param string $title    字段别名
     * @param array  $attr     按钮属性
     * @param string $elemtype 按钮类型，默认为button，也可以为a标签
     * @return $this|array
     */
    public function addButton($name = '', $title = '', $attr = [], $elemtype = 'button')
    {
        $item = [
            'type'     => 'button',
            'name'     => $name,
            'title'    => $title,
            'id'       => $name,
            'elemtype' => $elemtype,
            'data'     => '',
        ];
        if ($attr) {
            foreach ($attr as $key => $value) {
                if (substr($key, 0, 5) == 'data-') {
                    $item['data'] .= $key . '="' . $value . '" ';
                }
            }
            $item = array_merge($item, $attr);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加隐藏表单项
     * @param string $name        字段名称
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @return $this|array
     */
    public function addHidden($name = '', $default = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'hidden',
            'name'        => $name,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加取色器
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addColor($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'color',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请选择或输入颜色',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加代码编辑器
     * @param string $name 字段名称
     * @param string $title 字段别名
     * @param string $tips 提示信息
     * @param string $default 默认值
     * @param string $height 高度
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @param bool $required 是否必填
     * @param string $mode 编程语言（htmlmixed/javascript/css）
     * @param string $theme 主题
     * @return $this|array
     */
    public function addCode($name = '', $title = '', $tips = '', $default = '', $height = '', $extra_attr = '', $extra_class = '', $required = false, $mode = 'htmlmixed', $theme = 'monokai')
    {
        if ($mode == 'html') {
            $mode = 'htmlmixed';
        } else if ($mode == 'js') {
            $mode = 'javascript';
        }
        $item = [
            'type'        => 'code',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'height'      => $height ?: '500',
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'required'    => $required,
            'mode'        => $mode,
            'theme'       => $theme,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加自定义Html
     * @param string $html html代码
     * @return $this|array
     */
    public function addHtml($html = '')
    {
        $item = [
            'type' => 'html',
            'html' => $html,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    // ===================================表单项结束===================================

    /**
     * 添加表单项 [别名方法]
     * @param string $type 表单项类型
     * @param string $name 表单项名，与各自方法中的参数一致
     * @return $this
     */
    public function addFormItem($type = '', $name = ''): FormBuilder
    {
        if ($type != '') {
            // 获取所有参数值
            $args = func_get_args();
            // 删除数组中的第一个元素（type），并返回被删除元素的值
            array_shift($args);
            // 首字符转换为大写并拼接为方法名
            $method = 'add' . ucfirst($type);
            // 调用回调函数
            call_user_func_array([$this, $method], $args);
        }
        return $this;
    }

    /**
     * 一次性添加多个表单项
     * @param array $items 表单项
     * @return $this
     */
    public function addFormItems($items = []): FormBuilder
    {
        if (!empty($items)) {
            foreach ($items as $item) {
                call_user_func_array([$this, 'addFormItem'], $item);
            }
        }
        return $this;
    }

    /**
     * 设置表单数据
     * @param array $form_data 表单数据
     * @return $this|array
     */
    public function setFormData($form_data = [])
    {
        if (!empty($form_data)) {
            $this->_vars['form_data'] = $form_data;
        }
        return $this;
    }

    /***
     * 设置表单项的值
     */
    private function setFormValue()
    {
        if ($this->_vars['form_data']) {
            foreach ($this->_vars['form_items'] as &$item) {
                // 判断是否为分组
                if ($item['type'] == 'group') {
                    foreach ($item['options'] as &$group) {
                        foreach ($group as $key => $value) {
                            if (isset($value['name'])) {
                                if (isset($this->_vars['form_data'][$value['name']])) {
                                    $group[$key]['value'] = $this->_vars['form_data'][$value['name']];
                                }
                            }
                        }
                    }
                } else {
                    if (isset($item['name'])) {
                        if (isset($this->_vars['form_data'][$item['name']])) {
                            $item['value'] = $this->_vars['form_data'][$item['name']];
                        }
                    }
                }
            }
        }
    }

    /**
     * 添加分组
     * @param array $groups 分组数据
     * @return mixed
     */
    public function addGroup($groups = [])
    {
        if (is_array($groups) && !empty($groups)) {
            $this->_is_group = true;
            foreach ($groups as &$group) {
                foreach ($group as $key => $item) {
                    // 删除数组中的第一个元素（type）
                    $type = array_shift($item);
                    // 转换首字母大写，找到对应方法并调用
                    $group[$key] = call_user_func_array([$this, 'add' . ucfirst($type)], $item);
                }
            }
            $this->_is_group = false;
        }

        $item = [
            'type'    => 'group',
            'options' => $groups
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 渲染模版
     * @param string $template 模板文件名或者内容
     * @return string
     */
    public function fetch(string $template = '')
    {
        // 设置表单值
        $this->setFormValue();

        // 单独设置模板
        if ($template != '') {
            $this->_template = $template;
        }
        View::assign($this->_vars);
        return View::fetch($this->_template);
    }

}
