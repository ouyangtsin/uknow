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
use app\common\library\helper\StringHelper;
use think\facade\Request;
use think\Model;

class Nav extends Model
{
	//获取导航列表
	public static function getNavListByType($type): array
    {
        $modelName = strtolower(app('http')->getName());
        $controllerName = strtolower(Request::controller());
        $actionName = strtolower(Request::action());
        $nav_list = db('nav')->where(['status' => 1, 'type' => $type])->order('sort', 'DESC')->select()->toArray();

        foreach ($nav_list as $key=>$val)
        {
            $nav_list[$key]['active'] = 0;
            $nav_list[$key]['url_link'] = $val['url'];
            if(!$val['url'])
            {
                if(strtolower($val['module']) == $modelName && strtolower($val['controller']) == $controllerName && strtolower($val['action']) == $actionName)
                {
                    $nav_list[$key]['active'] = 1;
                }
                $nav_list[$key]['url_link'] = url($val['module'].'/'.$val['controller'].'/'.$val['action']);
            }
        }
        return $nav_list;
	}

    /**
     * 获取当前菜单的TDK
     */
	public static function getNavTDK()
    {
        $modelName = strtolower(app('http')->getName());
        $controllerName = strtolower(Request::controller());
        $actionName = strtolower(Request::action());

        $nav_info = db('nav')
            ->where([
                'module' => $modelName,
                'controller' => $controllerName,
                'action' => $actionName,
            ])
            ->field('seo_title,seo_keywords,seo_description')->find();
        if(!$nav_info)
        {
            return  false;
        }
        return $nav_info;
    }
}