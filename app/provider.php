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

use app\common\paginator\UKnowing;
use app\ExceptionHandle;
use app\Request;

// 容器Provider定义文件
return [
	'think\Request' => Request::class,
	'think\exception\Handle' => ExceptionHandle::class,
	'think\Paginator' => UKnowing::class,
];
