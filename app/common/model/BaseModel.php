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

namespace app\common\model;
use think\facade\Request;
use think\Model;

class BaseModel extends Model
{
	//错误信息
	public static $error;
	public static $dbName;
	public static $isMobile;

	public function __construct(array $data = [])
	{
		parent::__construct($data);
		self::$isMobile = Request::isMobile();
	}

	/**
	 * 设置错误信息
	 * @param $error
	 * @return mixed
	 */
	public static function setError($error) {
		return self::$error = $error;
	}

	/**
	 * 获取错误信息
	 * @return mixed
	 */
	public static function getError() {
		return self::$error;
	}
}