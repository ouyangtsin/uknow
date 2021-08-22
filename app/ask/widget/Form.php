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


namespace app\ask\widget;
use app\common\controller\Widget;

/**
 * 表单构造器
 * Class Form
 * @package app\common\widget
 */
class Form extends Widget
{
	public function show($field, $info)
	{
		$type = $field['type'] ?? 'text';
		//类型合并
		if ($type === 'string') {
			$type = 'text';
		}
		if ($type === 'picture') {
			$type = 'image';
		}
		$data = array(
			'type'   => $type,
			'field'  => $field['name'] ?? '',
			'value'  => $info[$field['name']] ?? ($field['value'] ?? ''),
			'size'   => $field['size'] ?? 12,
			'option' => $field['option'] ?? '',
		);
		$no_tem = array('readonly', 'text', 'password','checkbox', 'textarea', 'select', 'bind', 'checkbox', 'radio', 'num', 'bool', 'decimal','array');
		$type   = !in_array($type, $no_tem) ? $type : 'show';
		$this->assign($data);
		return $this->fetch('form/' . $type);
	}
}