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

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * Class Theme
 * @package app\common\model
 */
class Theme extends BaseModel
{
	protected $name = 'theme';
	protected static $themePath = '';

	public function __construct(array $data = [])
	{
		parent::__construct($data);
		self::$themePath = root_path().'templates/';
	}

	//获取模板主题列表
	public static function getThemeList($installed = true): array
    {
		static $_plugins = [];
		if ($_plugins) {
			return $_plugins;
		}

		$where = $installed ? ['status'=>2] : [];
        try {
            $plugins = self::where($where)->select()->toArray();
            $list = [];
            foreach ($plugins as $key => $value)
            {
                if (is_file(self::$themePath.$value['name'])) {
                    unset($plugins[$key]);
                    continue;
                }
                $pluginsDir = self::$themePath.$value['name'].DIRECTORY_SEPARATOR;
                if (! is_dir($pluginsDir)) {
                    unset($plugins[$key]);
                    continue;
                }
                if (!is_file($pluginsDir.ucfirst($value['name']).'.php')) {
                    unset($plugins[$key]);
                    continue;
                }

                $info = require(self::$themePath.$value['name'] .DIRECTORY_SEPARATOR. "info.php");
                $version = str_replace('.', '', $info['version']);
                $db_version=str_replace('.', '', $value['version']);

                $list[$value['name']] = $value;
                $configArr = json_decode($value['config'],true);
                $list[$value['name']]['config'] = $configArr;
                if ($version>$db_version)
                {
                    $list[$value['name']]['upgrade']=true;
                    $list[$value['name']]['up_version']=$info['version'];
                } else {
                    $list[$value['name']]['up_version']=false;
                }
            }
            $_plugins = $list;
            return  $_plugins;
        } catch (DataNotFoundException | ModelNotFoundException | DbException $e) {

        }
    }

	//获取模板主题列表带分页
	public static function getThemeListByPage($where,$page,$per_page): array
    {
		$templates = self::where($where)->order('update_time','desc')->paginate(
			[
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param()
			]
		);
		$pageVar = $templates->render();
		$list = [];
		foreach ($templates->all() as $key => $value)
		{
			$pluginsDir = self::$themePath.$value['name'].DIRECTORY_SEPARATOR;
			if (! is_dir($pluginsDir)) {
				unset($templates[$key]);
				continue;
			}

			$info = require(self::$themePath.$value['name'] .DIRECTORY_SEPARATOR. "info.php");
			$version=str_replace('.', '', $info['version']);
			$db_version=str_replace('.', '', $value['version']);

			$list[$value['name']] = $value;
			$configArr = json_decode($value['config'],true);
			$list[$value['name']]['config'] = $configArr;
			if ($version>$db_version)
			{
				$list[$value['name']]['up_version']=$info['version'];
			} else {
				$list[$value['name']]['up_version']=false;
			}
		}
		return ['list'=>$list,'page'=>$pageVar];
	}

	//安装模板主题
	public static function install($id)
	{
		$plug = self::where('id', $id)->find();
		if (!$plug) {
			self::setError('模板主题不存在');
			return false;
		}

		if ($plug['status'] > 0) {
			self::setError('请勿重复安装此模板主题');
			return false;
		}

		$plugPath = self::$themePath.$plug['name'].'/';
		if (!file_exists($plugPath.'info.php')) {
			self::setError('模板主题文件[info.php]丢失');
			return false;
		}

		$info       = include_once $plugPath.'info.php';
		// 导入配置信息
		if (file_exists($plugPath.'config.php')) {
			$config = include_once $plugPath.'config.php';
			self::update(['config'=>json_encode($config, 1)],['id'=> $id]);
		}

		// 更新模板主题基础信息
		$sqlMap = [];
		$sqlMap['title'] = $info['title'];
		$sqlMap['intro'] = $info['intro'];
		$sqlMap['author'] = $info['author'];
		$sqlMap['url'] = $info['url'];
		$sqlMap['version'] = $info['version'];
		$sqlMap['status'] = 2;
		return self::update($sqlMap,['id'=> $id]);
	}

    /**
     * 获取模板主题配置
     * @param string $name
     * @param bool $update
     * @return mixed
     */
    public static function getConfigs(string $name, $update = false)
    {
        $config = array();
        if (!isset($config[$name]) || $update == true) {
            $map  = $name ? ['name'=>$name] : [];
            $keyList =self::where($map)->column('config,name','name');
            $tmp = array();
            foreach ($keyList as $k => $v) {
                $tmp[$name] = json_decode($v['config'],true);
            }
            foreach ($tmp[$name] as $k=>$v)
            {
                if (in_array($v['type'], ['select','checkbox'])) {
                    $v['value'] = explode(',', $v['value']);
                }
                if ($v['type'] === 'array') {
                    $v['value'] = json_decode($v['option'],true);
                }

                $v['tips'] = htmlspecialchars($v['tips']);
                $config[$name]['config'][] = $v;
            }
        }
        return $config[$name]['config'];
    }
}