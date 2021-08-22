<?php
// 应用公共文件
use app\common\library\helper\LogHelper;
use app\common\library\helper\PluginsHelper;
use app\common\library\helper\ThemeHelper;
use app\common\model\Config;
use app\ask\model\Notify;
use app\common\model\Users;
use Overtrue\Pinyin\Pinyin;
use think\facade\Db;
use think\facade\Route;
use think\route\Url;

/**
 * 数据库实例
 * @param string $name 类名或标识 默认获取当前应用实例
 * @param bool $newInstance 是否每次创建新的实例
 * @return mixed
 */
function db(string $name, bool $newInstance = false)
{
    return \think\Container::getInstance()->make('db',[],$newInstance)->name($name);
}


/**
 * 获取系统配置,获取模块配置时请填写模块名_配置名，如ask_site_name代表ask模块的site_name 配置
 * @param string $name
 * @param null $default
 * @return mixed
 */
function get_setting(string $name='', $default=null)
{
    return Config::getConfigs($name,$default);
}

/**
 * 获取模板配置
 * @param string $themeName 模板名称
 * @param null $configName 配置值
 * @return mixed
 */
function get_theme_setting($themeName='',$configName=null)
{
    $config = ThemeHelper::instance()->getConfig($themeName);
    return $configName ? $config[$configName] : $config;
}

/**
 * 获取插件配置
 * @param string $plugin_name 插件名称
 * @param string $config_name 配置名称
 * @return array|false
 */
function get_plugins_config($plugin_name='',$config_name='')
{
    return PluginsHelper::instance()->getPluginsConfigs($plugin_name,$config_name);
}

//实例化小部件函数
if (! function_exists('widget')) {
	function widget($name, $param = [])
	{
		$name  = str_replace('/', '\\', $name);
		$action = $class = '';
		$array = explode('\\', $name);
		if(count($array)===3)
		{
			$model = $array[0];
			$action = $array[2];
			$class = app()->make(app($model)->getNamespace() .'\\widget\\' . ucfirst($array[1]));
		}elseif(count($array)===2)
		{
			$action = $array[1];
			$class = app()->make(app()->getNamespace() .'\\widget\\' . ucfirst($array[0]));
		}
		$call = [$class, $action];
		return app()->invoke($call, $param);
	}
}

/**
 * 字符串截取
 * @param $string
 * @param $start
 * @param $length
 * @param string $charset
 * @param string $dot
 * @return string
 */
if (! function_exists('str_cut')) {
	function str_cut($string, $start, $length, $charset = 'UTF-8', $dot = '...'): string
    {
		if (mb_strlen($string, $charset) <= $length) {
			return $string;
		}

		if (function_exists('mb_substr')) {
			return mb_substr($string, $start, $length, $charset) . $dot;
		}

        return iconv_substr($string, $start, $length, $charset) . $dot;
    }
}

if (!function_exists('parse_sql')) {
	/**
	 * 解析sql语句
	 * @param  string $sql sql内容
	 * @param  int $limit  如果为1，则只返回一条sql语句，默认返回所有
	 * @param  array $prefix 替换表前缀
	 * @return array|string 除去注释之后的sql语句数组或一条语句
	 */
	function parse_sql($sql = '', $limit = 0, $prefix = []) {
		// 被替换的前缀
		$from = '';
		// 要替换的前缀
		$to = '';

		// 替换表前缀
		if (!empty($prefix)) {
			$to   = current($prefix);
			$from = current(array_flip($prefix));
		}

		if ($sql != '') {
			// 纯sql内容
			$pure_sql = [];

			// 多行注释标记
			$comment = false;

			// 按行分割，兼容多个平台
			$sql = str_replace(["\r\n", "\r"], "\n", $sql);
			$sql = explode("\n", trim($sql));

			// 循环处理每一行
			foreach ($sql as $key => $line) {
				// 跳过空行
				if ($line == '') {
					continue;
				}

				// 跳过以#或者--开头的单行注释
				if (preg_match("/^(#|--)/", $line)) {
					continue;
				}

				// 跳过以/**/包裹起来的单行注释
				if (preg_match("/^\/\*(.*?)\*\//", $line)) {
					continue;
				}

				// 多行注释开始
				if (substr($line, 0, 2) === '/*') {
					$comment = true;
					continue;
				}

				// 多行注释结束
				if (substr($line, -2) === '*/') {
					$comment = false;
					continue;
				}

				// 多行注释没有结束，继续跳过
				if ($comment) {
					continue;
				}

				// 替换表前缀
				if ($from != '') {
					$line = str_replace('`'.$from, '`'.$to, $line);
				}
				if ($line === 'BEGIN;' || $line === 'COMMIT;') {
					continue;
				}
				// sql语句
				array_push($pure_sql, $line);
			}

			// 只返回一条语句
			if ($limit == 1) {
				return implode($pure_sql, "");
			}

			// 以数组形式返回sql语句
			$pure_sql = implode($pure_sql, "\n");
			$pure_sql = explode(";\n", $pure_sql);
			return $pure_sql;
		} else {
			return $limit == 1 ? '' : [];
		}
	}
}

/**
 * 数字转换为字符串单位
 * @param $num
 * @return string
 */
if (! function_exists('num2string')) {
	function num2string($num): string
    {
		if ($num >= 10000) {
			$num = round($num / 10000 * 100) / 100 . 'W+';
		} elseif ($num >= 1000) {
			$num = round($num / 1000 * 100) / 100 . 'K';
		}
		return $num;
	}
}

if(!function_exists('date_friendly'))
{
	function date_friendly($timestamp, $time_limit = 604800, $out_format = 'Y-m-d H:i', $formats = null, $time_now = null)
	{
		if (!$timestamp)
		{
			return false;
		}
		if ($formats == null)
		{
			$formats = array(
				'YEAR' => lang('%s 年前'),
				'MONTH' => lang('%s 月前'),
				'DAY' => lang('%s 天前'),
				'HOUR' => lang('%s 小时前'),
				'MINUTE' => lang('%s 分钟前'),
				'SECOND' => lang('%s 秒前'),
				'YEARS' => lang('%ss 年前'),
				'MONTHS' => lang('%s 月前'),
				'DAYS' => lang('%ss 天前'),
				'HOURS' => lang('%ss 小时前'),
				'MINUTES' => lang('%s 分钟前'),
				'SECONDS' => lang('%ss 秒前')
			);
		}

		$time_now = $time_now == null ? time() : $time_now;
		$seconds = $time_now - $timestamp;

		if ($seconds == 0)
		{
			$seconds = 1;
		}

		if (!$time_limit OR $seconds > $time_limit)
		{
			return date($out_format, $timestamp);
		}

		$minutes = floor($seconds / 60);
		$hours = floor($minutes / 60);
		$days = floor($hours / 24);
		$months = floor($days / 30);
		$years = floor($months / 12);
		$flag = '';
		if ($years > 0)
		{
			$diffFormat = 'YEAR';
			if($years > 1){
				$flag = 's';
			}
		}
		else
		{
			if ($months > 0)
			{
				$diffFormat = 'MONTH';
				if($months > 1){
					$flag = 's';
				}
			}
			else
			{
				if ($days > 0)
				{
					$diffFormat = 'DAY';
					if($days > 1){
						$flag = 's';
					}
				}
				else
				{
					if ($hours > 0)
					{
						$diffFormat = 'HOUR';
						if($hours > 1){
							$flag = 's';
						}
					}
					else
					{
						if($minutes > 0){
							$diffFormat = 'MINUTE';
							if($minutes > 1){
								$flag = 's';
							}
						}else{
							$diffFormat = 'SECOND';
							if($seconds > 1){
								$flag = 's';
							}
						}
					}
				}
			}
		}

		$dateDiff = null;
		switch ($diffFormat)
		{
			case 'YEAR' :
				$dateDiff = sprintf($formats[$diffFormat], $years, $flag);
				break;
			case 'MONTH' :
				$dateDiff = sprintf($formats[$diffFormat], $months, $flag);
				break;
			case 'DAY' :
				$dateDiff = sprintf($formats[$diffFormat], $days, $flag);
				break;
			case 'HOUR' :
				$dateDiff = sprintf($formats[$diffFormat], $hours, $flag);
				break;
			case 'MINUTE' :
				$dateDiff = sprintf($formats[$diffFormat], $minutes, $flag);
				break;
			case 'SECOND' :
				$dateDiff = sprintf($formats[$diffFormat], $seconds, $flag);
				break;
		}
		return $dateDiff;
	}
}

/*
 * =========================================================用户相关===================================================================
 */
function get_username($uid = 0): string
{
	static $list;
	if (!($uid && is_numeric($uid))) {
		//获取当前登录用户名
		return session('login_user_info.user_name');
	}

	/* 获取缓存数据 */
	if (empty($list)) {
		$list = cache('sys_user_username_list');
	}

	/* 查找用户信息 */
	$key = "u{$uid}";
	if (isset($list[$key])) {
		//已缓存，直接使用
		$name = $list[$key];
	} else {
		//调用接口获取用户信息
		$info = db('users')->field('user_name')->find($uid);

		if ($info !== false && $info['user_name']) {
			$nickname = $info['user_name'];
			$name     = $list[$key]     = $nickname;
			/* 缓存用户 */
			$count = count($list);
			$max   = get_setting('user_max_cache');
			while ($count-- > $max) {
				array_shift($list);
			}
			cache('sys_user_username_list', $list);
		} else {
			$name = '';
		}
	}
	return $name;
}
function get_user_id()
{
	return session('login_user_info.uid');
}
function get_link_username($uid=0)
{
    static $list;
    if (!($uid && is_numeric($uid))) {
        //获取当前登录用户名
        return session('login_user_info.user_name');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = cache('sys_user_username_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) {
        //已缓存，直接使用
        $name = $list[$key];
    } else {
        //调用接口获取用户信息
        $info = db('users')->field('user_name')->find($uid);
        if ($info !== false && $info['user_name']) {
            $nickname = $info['user_name'];
            $name     = $list[$key]     = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max   = get_setting('user_max_cache');
            while ($count-- > $max) {
                array_shift($list);
            }
            cache('sys_user_username_list', $list);
        } else {
            $name = '';
        }
    }
    return '<a href="'.(string)url('member/index//index',['uid'=>$uid]).'" class="uk-username">'.$name.'</a>';
}
/**
 * 获取用户信息
 * @param int $uid
 * @param string $field
 * @return array|false|mixed
 */
function get_user_info($uid=0,$field='')
{
    if (!$uid && !$uid=session('login_uid')) {
        return false;
    }

    $user_info=cache('user_info_'.$uid);
    if(!$user_info){
        $user_info = \app\common\model\Users::getUserInfo($uid);
        cache('user_info_'.$uid,$user_info);
    }
    return $field ? $user_info[$field] : $user_info;
}


/**
 * 记录行为日志，并执行该行为的规则
 * @param null $action 行为标识
 * @param null $model 触发行为的模型名
 * @param null $record_id 触发行为的记录id
 * @param null $uid 执行行为的用户id
 * @return boolean
 */
function action_log($action = null, $model = null, $record_id = null, $uid = null): bool
{
	return LogHelper::addActionLog($action, $model, $record_id, $uid);
}

/**
 * 记录积分日志，并执行该行为的规则
 * @param null $action 行为标识
 * @param null $record_id 触发行为的记录id
 * @param string $record_db
 * @param int $uid 执行行为的用户id
 * @return boolean
 */
function score_log($action,$record_id,$record_db='', $uid=0): bool
{
    return LogHelper::addScoreLog($action,$record_id,$record_db, $uid);
}

/**
 * 获取随机字符串编码
 * @param integer $size 编码长度
 * @param integer $type 编码类型(1纯数字,2纯字母,3数字字母)
 * @param string $prefix 编码前缀
 * @return string
 */
function random(int $size = 10, int $type = 1, string $prefix = ''): string
{
    $numbs = '0123456789';
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    if (intval($type) === 1) $chars = $numbs;
    if (intval($type) === 3) $chars = "{$numbs}{$chars}";
    $code = $prefix . $chars[rand(1, strlen($chars) - 1)];
    while (strlen($code) < $size) $code .= $chars[rand(0, strlen($chars) - 1)];
    return $code;
}
function Directory( $dir ){  
 
   return  is_dir ( $dir ) or Directory(dirname( $dir )) and  mkdir ( $dir , 0777);
 
}
/**
 * 唯一日期编码
 * @param integer $size 编码长度
 * @param string $prefix 编码前缀
 * @return string
 */
function uniqueDate(int $size = 16, string $prefix = ''): string
{
    if ($size < 14) $size = 14;
    $code = $prefix . date('Ymd') . (date('H') + date('i')) . date('s');
    while (strlen($code) < $size) $code .= rand(0, 9);
    return $code;
}

/**
 * 唯一数字编码
 * @param integer $size 编码长度
 * @param string $prefix 编码前缀
 * @return string
 */
function uniqueNumber(int $size = 12, string $prefix = ''): string
{
    $time = time() . '';
    if ($size < 10) $size = 10;
    $code = $prefix . (intval($time[0]) + intval($time[1])) . substr($time, 2) . rand(0, 9);
    while (strlen($code) < $size) $code .= rand(0, 9);
    return $code;
}

/**
 * PHP格式化字节大小
 * @param number $size 字节数
 * @param string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function formatBytes($size, $delimiter = ''): string
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 时间戳格式化时间
 * @param $time
 * @param string $format
 * @return false|string
 */
function formatTime($time,$format='Y-m-d H:i:s')
{
    return date($format,intval($time));
}

/**
 * 发送通知
 * @param int $sender_uid 发送用户id
 * @param int $recipient_uid 接受用户id
 * @param int $action_type 通知类型
 * @param string $subject 通知标题
 * @param int $item_id 通知内容id
 * @param array $content 通知详细数据
 * @return bool
 */
function send_notify($sender_uid=0, $recipient_uid=0, $action_type='', $subject='', $item_id = 0, $content = array()){
    $guid=$sender_uid ?: intval(session('login_uid'));
    $G_SETTING= db('users_setting')->where('uid',$guid)->find();
    if((!$sender_uid and !$recipient_uid) || $guid==0 || ! $G_SETTING){
        return false;
    }

	if(in_array($action_type,explode(',', $G_SETTING['notify_setting']))){
		Notify::send($sender_uid, $recipient_uid, $action_type, $subject, $item_id, $content);
	}	
}

//获取用户链接地址
function get_user_url($uid,$param=[])
{
    static $userInfo;
    if(!isset($userInfo[$uid]))
    {
        $userInfo[$uid] = db('users')->where('uid',$uid)->field('user_name,url_token')->find();
    }
    if($userInfo[$uid]['url_token'])
    {
        $user_name = $userInfo[$uid]['url_token'];
    }else{
        $pinyin = new Pinyin();
        $user_name = $pinyin->permalink($userInfo[$uid]['user_name']);
    }
    $param['name'] = $user_name;
    return (string)url('member/index/index',$param);
}