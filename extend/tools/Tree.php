<?php
namespace tools;

/**
* 通用树型类
*/
class Tree {
    
    /**
     * 配置参数
     * @var array
     */
    protected static $config = [
        'id'                => 'id',        // id名称
        'pid'               => 'pid',       // pid名称
        'child'             => 'childs',    // 子元素键名
        'name'              => 'name',      // 下拉列表的选项名
        'icon'              => '├',         // 下拉列表的图标
        'placeholder'       => '&nbsp;',    // 下拉列表的占位符
        'placeholder_number'=> 3,           // 下拉列表的占位符数量
    ];

    /**
     * 架构函数
     * @param array $config
     */
    public function __construct($config = [])
    {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * 配置参数
     * @param  array $config
     * @return array|object
     */
    public static function config($config = [])
    {
        if (!empty($config)) {
            return self::$config = array_merge(self::$config, $config);
        }
    }

	public static function treeList($data, $pid=0, $deep = 1): array
    {
		static $tree = array();
		foreach ($data as $row) {
			if ($row ['pid'] === $pid) {
				$row ['lever'] = $deep;
				$tree [] = $row;
				self::treeList($data, $row ['id'], $deep + 1);
			}
		}
		return $tree;
	}

    /**
     * 将数据集格式化成树形结构
     * @param array $data 原始数据
     * @param int $pid 父级id
     * @param int $limitLevel  限制返回几层，0为不限制
     * @param int $currentLevel 当前层数
     * @return array
     */
    public static function toTree($data = [], $pid = 0, $limitLevel = 0, $currentLevel = 0): array
    {
        $trees = [];
        $data = array_values($data);
        foreach ($data as $k => $v) {
            if ($v[self::$config['pid']] == $pid) {
                if ($limitLevel > 0 && $limitLevel == $currentLevel) {
                    return $trees;
                }
                unset($data[$k]);
                $children = self::toTree($data, $v[self::$config['id']], $limitLevel, ($currentLevel+1));
                if (!empty($children)) {
                    $v[self::$config['child']] = $children;
                }
                $trees[] = $v;
            }
        }
        return $trees;
    }

    /**
     * 将树形结构的数据转成下拉选择
     * @param array|object $data 原始数据
     * @param int $sid 选中ID
     * @param array $did 禁止选择
     * @param int $level 当前层数
     * @return mixed array
     */
    public static function toOptions($data = [], $sid = 0, $did = [], $level = 0)
    {
        if (empty($data)) {
            return '';
        }

        $id     = self::$config['id'];
        $name   = self::$config['name'];
        $str    = '';
        $icon   = '';
        $child = self::$config['child'];

        if ($level > 0) {

            for ($i=0; $i < $level; $i++) {

                for($j = 0; $j < self::$config['placeholder_number']; $j++) {

                    $icon .= self::$config['placeholder'];
                }

            }

            $icon .= self::$config['icon'].'&nbsp;';
        }

        foreach ($data as $k => $v) {

            if ($sid === $v['id']) {

                $str .= '<option value="'.$v[$id].'" selected>'.$icon.$v[$name].'</option>';

            } else if ($did && in_array($v[$id], $did, true)) {

                $str .= '<option value="'.$v[$id].'" disabled>'.$icon.$v[$name].'</option>';

            } else {

                $str .= '<option value="'.$v[$id].'">'.$icon.$v[$name].'</option>';

            }

            if (isset($v[$child])) {
                $str.= self::toOptions($v[$child], $sid, $did, $level+1);
            }
        }

        return $str;
    }

	/**
	 * @param $data
	 * @param $id
	 * @param string $pid_name
	 * @return array
	 */
	public static function getChild($data,$id,$pid_name ='pid'): array
    {
		$newData = [];
		foreach ($data as $value) {
			if (!isset($value['id'])) {
				continue;
			}
			if ($value[$pid_name] === $id) {
				$newData[$value['id']] = $value;
			}
		}
		return $newData;
	}

	/**
	 * 读取指定节点的所有孩子节点
	 * @param $data
	 * @param $id
	 * @param bool $withSelf
	 * @param string $pid_name
	 * @return array
	 */
	public static function getChildren($data,$id, $withSelf = false,$pid_name ='pid'): array
    {
		$newData = [];
		foreach ($data as $value) {
			if (!isset($value['id'])) {
				continue;
			}
			if ($value[$pid_name] === $id) {
				$newData[] = $value;
				$newData = array_merge($newData, self::getChildren($data,$value['id']));
			} elseif ($withSelf && $value['id'] === $id) {
				$newData[] = $value;
			}
		}
		return $newData;
	}

	/**
	 * 读取指定节点的所有孩子节点ID
	 * @param $data
	 * @param $id
	 * @param bool $withSelf
	 * @param string $pid_name
	 * @return array
	 */
	public static function getChildrenIds($data,$id, $withSelf = false,$pid_name ='pid'): array
    {
		$childrenList = self::getChildren($data,$id, $withSelf,$pid_name);
		$childrenIds = [];
		foreach ($childrenList as $k => $v) {
			$childrenIds[] = $v['id'];
		}
		return $childrenIds;
	}

	/**
	 * 得到当前位置父辈数组
	 * @param $data
	 * @param $id
	 * @param string $pid_name
	 * @return array
	 */
	public static function getParent($data,$id,$pid_name ='pid'): array
    {
		$pid = 0;
		$newData = [];
		foreach ($data as $value) {
			if (!isset($value['id'])) {
				continue;
			}
			if ($value['id'] == $id) {
				$pid = $value[$pid_name];
				break;
			}
		}
		if ($pid) {
			foreach ($data as $value) {
				if ($value['id'] == $pid) {
					$newData[] = $value;
					break;
				}
			}
		}
		return $newData;
	}

	/**
	 * 得到当前位置所有父辈数组
	 * @param $data
	 * @param $id
	 * @param bool $withSelf
	 * @param string $pid_name
	 * @return array
	 */
	public static function getParents($data,$id, $withSelf = false,$pid_name ='pid'): array
    {
		$pid = 0;
		$newData = [];
		foreach ($data as $value) {
			if (!isset($value['id'])) {
				continue;
			}
			if ($value['id'] == $id) {
				if ($withSelf) {
					$newData[] = $value;
				}
				$pid = $value[$pid_name];
				break;
			}
		}
		if ($pid) {
			$arr = self::getParents($data,$pid, true);
			$newData = array_merge($arr, $newData);
		}
		return $newData;
	}

	/**
	 * 读取指定节点所有父类节点ID
	 * @param $data
	 * @param $id
	 * @param bool $withSelf
	 * @param string $pid_name
	 * @return array
	 */
	public static function getParentsIds($data,$id, $withSelf = false,$pid_name ='pid'): array
    {
		$parentList = self::getParents($data,$id, $withSelf,$pid_name);
		$parentsIds = [];
		foreach ($parentList as $k => $v) {
			$parentsIds[] = $v['id'];
		}
		return $parentsIds;
	}
}
