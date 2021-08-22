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

namespace app\common\library\helper;

/**
 * HTML渲染服务
 * Class RenderHelper
 * @package app\common\library\helper
 */
class RenderHelper
{
	protected static $CSRFToken = array('name' => '__token__');

	/**
	 * 已创建的标签名称
	 * @var array
	 */
	protected static $labels = [];

	/**
	 * 跳过的填充value值的类型
	 * @var array
	 */
	protected static $skipValueTypes = array('file', 'password', 'checkbox', 'radio');

	/**
	 * 转义HTML
	 * @var boolean
	 */
	protected static $escapeHtml = true;

	/**
	 * 设置是否转义
	 * @param boolean $escape
	 */
	public function setEscapeHtml(bool $escape)
	{
		self::$escapeHtml = $escape;
	}

	/**
	 * 获取转义编码后的值
	 * @param string $value
	 * @return string
	 */
	public static function escape($value)
	{
		if (!self::$escapeHtml) {
			return $value;
		}
		if (is_array($value)) {
			$value = json_encode($value, JSON_UNESCAPED_UNICODE);
		}
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
	}

	/**
	 * 生成Label标签
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function label($name, $value = null, $options = [])
	{
		self::$labels[] = $name;
		$options = self::attributes($options);
		$value = self::escape(self::formatLabel($name, $value));
		return '<label for="' . $name . '"' . $options . '>' . $value . '</label>';
	}

	/**
	 * Format the label value.
	 * @param  string      $name
	 * @param  string|null $value
	 * @return string
	 */
	protected static function formatLabel($name, $value)
	{
		return $value ?: ucwords(str_replace('_', ' ', $name));
	}

	/**
	 * 生成文本框(按类型)
	 * @param  string $type 文本框类型
	 * @param  string $name 文本框字段名称
	 * @param  string $value 文本框默认值
	 * @param  array  $options 文本框附加属性
	 * @return string
	 */
	public static function input($type, $name, $value = null, $options = [])
	{
		if (!isset($options['name'])) {
			$options['name'] = $name;
		}
		$id = self::getIdAttribute($name, $options);
		if (!in_array($type, self::$skipValueTypes)) {
			$value = self::getValueAttribute($name, $value);
			$options['class'] = isset($options['class']) ? $options['class'] : 'am-form-field';
		}
		$merge = compact('type', 'value', 'id');
		$options = array_merge($options, $merge);
		return '<input' . self::attributes($options) . '>';
	}

	/**
	 * 生成普通文本框
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function text($name, $value = null, $options = [])
	{
		return self::input('text', $name, $value, $options);
	}

	/**
	 * 生成密码文本框
	 * @param  string $name
	 * @param  array  $options
	 * @return string
	 */
	public static function password($name, $options = [])
	{
		return self::input('password', $name, '', $options);
	}

	/**
	 * 生成隐藏文本框
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function hidden($name, $value = null, $options = [])
	{
		return self::input('hidden', $name, $value, $options);
	}

	/**
	 * 生成Email文本框
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function email($name, $value = null, $options = [])
	{
		return self::input('email', $name, $value, $options);
	}

	/**
	 * 生成URL文本框
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function url($name, $value = null, $options = [])
	{
		return self::input('url', $name, $value, $options);
	}

	/**
	 * 生成上传文件组件
	 * @param  string $name
	 * @param  array  $options
	 * @return string
	 */
	public static function file($name, $options = [])
	{
		return self::input('file', $name, null, $options);
	}

	/**
	 * 生成多行文本框
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function textarea($name, $value = null, $options = [])
	{
		if (!isset($options['name'])) {
			$options['name'] = $name;
		}
		$options = self::setTextAreaSize($options);
		$options['id'] = self::getIdAttribute($name, $options);
		$value = (string)self::getValueAttribute($name, $value);
		unset($options['size']);
		$options['class'] = isset($options['class']) ? $options['class'] : '';
		$options = self::attributes($options);
		return '<textarea' . $options . '>' . self::escape($value) . '</textarea>';
	}


	/**
	 * 设置默认的文本框行列数
	 * @param  array $options
	 * @return array
	 */
	protected static function setTextAreaSize($options)
	{
		if (isset($options['size'])) {
			return self::setQuickTextAreaSize($options);
		}
		$cols = self::get($options, 'cols', 50);
		$rows =self::get($options, 'rows', 5);
		return array_merge($options, compact('cols', 'rows'));
	}

	/**
	 * 根据size设置行数和列数
	 * @param  array $options
	 * @return array
	 */
	protected static function setQuickTextAreaSize($options)
	{
		$segments = explode('x', $options['size']);
		return array_merge($options, array('cols' => $segments[0], 'rows' => $segments[1]));
	}


	/**
	 * 生成下拉列表框
	 * @param  string $name
	 * @param  array  $list
	 * @param  mixed  $selected
	 * @param  array  $options
	 * @return string
	 */
	public static function select($name, $list = [], $selected = null, $options = [])
	{
		$selected = self::getValueAttribute($name, $selected);
		$options['id'] = self::getIdAttribute($name, $options);
		if (!isset($options['name'])) {
			$options['name'] = $name;
		}
		$html = [];
		foreach ($list as $value => $display) {
			$html[] = self::getSelectOption($display, $value, $selected);
		}
		$options['class'] = isset($options['class']) ? $options['class']  : '';
		$options = self::attributes($options);
		$list = implode('', $html);
		return "<select {$options}>{$list}</select>";
	}

	/**
	 * 下拉列表(多选)
	 * @param string $name
	 * @param array  $list
	 * @param mixed  $selected
	 * @param array  $options
	 * @return string
	 */
	public static function selects($name, $list = [], $selected = null, $options = [])
	{
		$options[] = 'multiple';
		return self::select($name, $list, $selected, $options);
	}

	/**
	 * 根据传递的值生成option
	 * @param  string $display
	 * @param  string $value
	 * @param  string $selected
	 * @return string
	 */
	public static function getSelectOption($display, $value, $selected)
	{
		if (is_array($display)) {
			return self::optionGroup($display, $value, $selected);
		}
		return self::option($display, $value, $selected);
	}

	/**
	 * 生成optionGroup
	 * @param  array  $list
	 * @param  string $label
	 * @param  string $selected
	 * @return string
	 */
	protected static function optionGroup($list, $label, $selected)
	{
		$html = [];
		foreach ($list as $value => $display) {
			$html[] = self::option($display, $value, $selected);
		}
		return '<optgroup label="' . self::escape($label) . '">' . implode('', $html) . '</optgroup>';
	}

	/**
	 * 生成option选项
	 * @param  string $display
	 * @param  string $value
	 * @param  string $selected
	 * @return string
	 */
	protected static function option($display, $value, $selected)
	{
		$selected = self::getSelectedValue($value, $selected);
		$options = array('value' => self::escape($value), 'selected' => $selected);
		return '<option' . self::attributes($options) . '>' . self::escape($display) . '</option>';
	}

	/**
	 * 检测value是否选中
	 * @param  string $value
	 * @param  string $selected
	 * @return string
	 */
	protected static function getSelectedValue($value, $selected)
	{
		if (is_array($selected)) {
			return in_array($value, $selected) ? 'selected' : null;
		}
		return ((string)$value == (string)$selected) ? 'selected' : null;
	}

	/**
	 * 生成复选按钮
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @param  array  $options
	 * @return string
	 */
	public static function checkbox($name, $value = 1, $checked = null, $options = [])
	{
		if ($checked) {
			$options['checked'] = 'checked';
		}
		return self::input('checkbox', $name, $value, $options);
	}

	/**
	 * 生成一组筛选框
	 * @param string $name
	 * @param array  $list
	 * @param mixed  $checked
	 * @param array  $options
	 * @return string
	 */
	public static function checkboxs($name, $list, $checked, $options = [])
	{
		$html = [];
		$checked = is_null($checked) ? [] : $checked;
		$checked = is_array($checked) ? $checked : explode(',', $checked);
		foreach ($list as $k => $v) {
			$options['id'] = "{$name}-{$k}";
			$html[] = sprintf(self::label("{$name}-{$k}", "%s {$v}"), self::checkbox($name, $k, in_array($k, $checked), $options));
		}
		return '<div class="checkbox">' . implode(' ', $html) . '</div>';
	}

	/**
	 * 生成单选按钮
	 * @param  string $name
	 * @param  mixed  $value
	 * @param  bool   $checked
	 * @param  array  $options
	 * @return string
	 */
	public static function radio($name, $value = null, $checked = null, $options = [])
	{
		if (is_null($value)) {
			$value = $name;
		}
		if ($checked) {
			$options['checked'] = 'checked';
		}
		return self::input('radio', $name, $value, $options);
	}

	/**
	 * 生成一组单选框
	 * @param string $name
	 * @param array  $list
	 * @param mixed  $checked
	 * @param array  $options
	 * @return string
	 */
	public static function radios($name, $list, $checked = null, $options = [])
	{
		$html = [];
		$checked = is_null($checked) ? key($list) : $checked;
		$checked = is_array($checked) ? $checked : explode(',', $checked);
		foreach ($list as $k => $v) {
			$options['id'] = "{$name}-{$k}";
			$html[] = sprintf(self::label("{$name}-{$k}", "%s {$v}"), self::radio($name, $k, in_array($k, $checked), $options));
		}
		return '<div class="radio">' . implode(' ', $html) . '</div>';
	}

	/**
	 * 生成一个按钮
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public static function button($value = null, $options = [])
	{
		if (!array_key_exists('type', $options)) {
			$options['type'] = 'button';
		}
		return '<button' . self::attributes($options) . '>' . $value . '</button>';
	}

	/**
	 * 获取ID属性值
	 * @param  string $name
	 * @param  array  $attributes
	 * @return string
	 */
	public static function getIdAttribute($name, $attributes)
	{
		if (array_key_exists('id', $attributes)) {
			return $attributes['id'];
		}
		if (in_array($name, self::$labels)) {
			return $name;
		}
	}

	/**
	 * 获取Value属性值
	 * @param  string $name
	 * @param  string $value
	 * @return string
	 */
	public static function getValueAttribute($name, $value = null)
	{
		if (is_null($name)) {
			return $value;
		}
		if (!is_null($value)) {
			return $value;
		}
	}

	/**
	 * 拼接成一个属性。
	 * @param  string $key
	 * @param  string $value
	 * @return string
	 */
	protected static function attributeElement($key, $value)
	{
		if (is_numeric($key)) {
			$key = $value;
		}
		if (!is_null($value)) {
			if (is_array($value) || stripos($value, '"') !== false) {
				$value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
				return $key . "='" . $value . "'";
			} else {
				return $key . '="' . $value . '"';
			}
		}
	}

	/**
	 * 生成JavaScript文件标签.
	 * @param $url
	 * @param array $attributes
	 * @return string
	 */
	public static function script($url, $attributes = array())
	{
		$return = '';
		if(is_array($url))
		{
			foreach ($url as $v) {
				$attributes['src'] = $v;
				$return .= '<script'.self::attributes($attributes).'></script>'.PHP_EOL;
			}
		}else{
			$attributes['src'] = $url;
			$return = '<script'.self::attributes($attributes).'></script>'.PHP_EOL;
		}
		return $return;
	}

	/**
	 * 生成html的link标签
	 * @param $url
	 * @param array $attributes
	 * @return string
	 */
	public static function style($url, $attributes = array())
	{
		$defaults = array('media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet');
		$attributes = $attributes + $defaults;
		$return = '';
		if(is_array($url))
		{
			foreach ($url as $v) {
				$attributes['href'] = $url = $v;
				$return .= '<link'.self::attributes($attributes).'>'.PHP_EOL;
			}
		}else{
			$attributes['href'] = $url;
			$return = '<link'.self::attributes($attributes).'>'.PHP_EOL;
		}
		return $return;
	}

	/**
	 * 创建附加属性
	 * @param  array  $attributes
	 * @return string
	 */
	public static function attributes($attributes)
	{
		$html = array();
		foreach ((array) $attributes as $key => $value)
		{
			$element = self::attributeElement($key, $value);
			if ( ! is_null($element)) $html[] = $element;
		}
		return count($html) > 0 ? ' '.implode(' ', $html) : '';
	}
	
}