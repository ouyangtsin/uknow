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


namespace app\home\validate;

use think\Validate;

/**
 * 问题验证器
 * Class Question
 * @package app\ask\validate
 */
class Question extends Validate
{
	protected $rule =   [
		'title'  => 'require|max:100',

	];

	protected $message  =   [
		'title.require' => '问题标题必须填写',
		'title.max'     => '问题标题最多不能超过100个字符',
	];
}