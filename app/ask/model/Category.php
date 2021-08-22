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


namespace app\ask\model;

use think\Model;
use tools\Tree;

/**
 * 分类模型
 * Class Category
 * @package app\ask\model
 */
class Category extends Model
{
    /**
     * 根据分类类型获取分类列表
     * @param string $type
     * @return mixed
     */
	public static function getCategoryListByType($type='all')
	{
		return db('category')->where(['status'=>1,'type'=>$type])->select()->toArray();
	}

	/**
	 * 获取分类的所有子集
	 * @param $category_id
	 * @param null $type
	 * @param bool $self
	 * @return array|bool
	 */
	public static function getCategoryWithChildIds($category_id,$type=null,$self=false)
	{
		if(!$category_id) return false;
		$where[] =['status','=',1];
        $where[] = ['id','=',$category_id];
		if($type)
		{
			$where[] =['type','=',$type];
		}
		$child_data = db('category')->where($where)->column('id,pid,title');
        return Tree::getChildrenIds($child_data,$category_id,$self);
	}
}