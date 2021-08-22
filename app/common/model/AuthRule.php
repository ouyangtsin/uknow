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

use app\common\library\helper\TreeHelper;
use think\Model;

/**
 * 权限节点模型
 * Class AuthRule
 * @package app\admin\model
 */
class AuthRule extends BaseModel
{

    // 获取父ID选项信息
    public static function getPidOptions($order = ['sort', 'id' => 'desc']): array
    {
        $list = self::order($order)
            ->select()
            ->toArray();
        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }
        return $result;
    }

	/**
	 * 创建菜单
	 * @param array $menu
	 * @param mixed $parent 父类的name或pid
	 */
	public static function createMenu($menu, $parent = 0)
	{
		if (!is_numeric($parent)) {
			$parentRule = self::getByName($parent);
			$pid = $parentRule ? $parentRule['id'] : 0;
		} else {
			$pid = $parent;
		}
		$allow = array_flip(['type', 'name', 'title', 'icon', 'condition','menu']);

		foreach ($menu as $k => $v) {
			$hasChild = isset($v['child']) && $v['child'];
			$data = array_intersect_key($v, $allow);
			$data['menu'] = $data['menu'] ?? ($hasChild ? 1 : 0);
			$data['icon'] = $data['icon'] ?? 'icon-list';
			$data['pid'] = $pid;
			$data['status'] = 1;
			$menu = self::create($data);
			if ($hasChild) {
				self::createMenu($v['child'], $menu->id);
			}
		}
	}

	/**
	 * 删除菜单
	 * @param string $name 规则name
	 * @return boolean
	 */
	public static function deleteMenu($name)
	{
		$ids = self::getAuthRuleIdsByName($name);
		if (!$ids) {
			return false;
		}
		AuthRule::destroy($ids);
		return true;
	}

	/**
	 * 启用菜单
	 * @param string $name
	 * @return boolean
	 */
	public static function enableMenu($name)
	{
		$ids = self::getAuthRuleIdsByName($name);
		if (!$ids) {
			return false;
		}
		AuthRule::whereIn('id', $ids)->update(['status' => 1]);
		return true;
	}

	/**
	 * 禁用菜单
	 * @param string $name
	 * @return boolean
	 */
	public static function disableMenu($name)
	{
		$ids = self::getAuthRuleIdsByName($name);
		if (!$ids) {
			return false;
		}
		AuthRule::whereIn('id', $ids)->update(['status' => 0]);
		return true;
	}

	//导出指定名称的菜单规则
	public static function exportMenu($name)
	{
		$ids = self::getAuthRuleIdsByName($name);
		if (!$ids) {
			return [];
		}
		$menuList = [];
		$menu = AuthRule::getByName($name);
		if ($menu) {
			$ruleList = AuthRule::whereIn('id', $ids)->select()->toArray();
			$menuList = TreeHelper::instance()->init($ruleList)->getTreeArray($menu['id']);
		}
		return $menuList;
	}

	public static function getAuthRuleIdsByName($name)
	{
		$ids = [];
		$menu = self::getByName($name);
		if ($menu) {
			// 必须将结果集转换为数组
			$ruleList = self::order('weigh', 'desc')->field('id,pid,name')->select()->toArray();
			// 构造菜单数据
			$ids = TreeHelper::instance()->init($ruleList)->getChildrenIds($menu['id'], true);
		}
		return $ids;
	}
}