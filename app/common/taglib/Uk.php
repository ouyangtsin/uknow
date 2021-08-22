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
namespace app\common\taglib;
use think\template\TagLib;

class Uk extends Taglib
{

	// 标签定义
	protected $tags = [
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'nav'        => ['attr' => 'name,type', 'close' => 1],
        'list'      => ['attr' => 'id,name,category_id,topic_ids,where,type,page,limit,current,sort,relation_uid','close' => 1], // 通用列表
	];

    // 通用导航信息
    public function tagNav($tag, $content)
    {
        $tag['type']  = $tag['type'] ?? 1;
        $name         = $tag['name']  ?? 'nav';
        $cateStr  = '$__LIST__ = \app\common\model\Nav::getNavListByType('.$tag['type'].');';
        $parse  = '<?php ';
        $parse .= $cateStr;
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 通用列表
    public function tagList($tag, $content)
    {
        $name   = $tag['name'] ?? "list";
        $sort   = $tag['sort'] ?? input('sort',"new");      // 排序
        $type   = $tag['type'] ?? input('type','null');
        $category_id   = $tag['category_id'] ?? input('category_id','null');
        $topic_ids   = $tag['topic_ids'] ??  input('topic','null');
        $page =  $tag['page'] ?? 'page';
        $per_page = $tag['limit'] ?? 10;
        $uid = session('login_uid') ?? 0;
        $relation_uid = $tag['relation_uid'] ?? 0;
        $current = $tag['current'] ?? input('page',1);
        $parse  = '<?php ';
        $parse .= '
            $__DATA__ = \app\common\model\PostRelation::getPostRelationList('.$uid.','.$type.','.$sort.','.$topic_ids.','.$category_id.','.$current.','.$per_page.','.$relation_uid.');
            $__LIST__ = $__DATA__["list"];$'.$page.' = $__DATA__["page"];';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }
}

