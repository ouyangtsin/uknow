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
use app\common\library\helper\ModuleHelper;
use think\Model;

/**
 * 配置管理模型
 * Class Config
 * @package app\admin\model
 */
class Config extends Model
{
	protected $name = 'config';

    /**
     * 获取配置
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
	public static function getConfigs(string $name='', $default=null)
    {
		static $config=[];
		if(empty($config))
        {
            foreach (db('config')->select() as $k => $v)
            {
                if (in_array($v['type'], ['select', 'checkbox', 'images','files'])) {
                    $v['value'] = explode(',',$v['value']);
                }
                if ($v['type'] === 'array') {
                    $v['value'] = json_decode($v['option'],true);
                }
                $config[$v['name']] = $v['value'];
            }

            //TODO 加入模块中的配置
            $module_configs = ModuleHelper::instance()->getAllModuleConfigs();
            $config = array_merge($config,$module_configs);
        }
		if($name!=='' && isset($config[$name]) && !$config[$name] && $default)
        {
            $config[$name] = $default;
        }
		return $name ? ($config[$name] ?? '') : $config;
	}
}
