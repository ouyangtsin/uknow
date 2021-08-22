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



namespace app\member\widget;
use app\common\controller\Widget;
use app\common\model\Users;
use app\ask\model\Topic;

/**
 * 侧边栏小部件
 * Class Sidebar
 * @package app\ask\widget
 */
class Sidebar extends Widget
{
    /**
     * 用户信息
     * @param $user_info
     * @param $uid
     * @return mixed
     */
	public function profile($user_info,$uid)
	{
		$this->assign('user_info',$user_info);
		$this->assign('uid',$uid);
		return $this->fetch('sidebar/profile');
	}

    /**
     * 当前登录用户信息
     * @return mixed
     */
	public function login_user()
	{
		return $this->fetch('sidebar/login_user');
	}

    /**
     * 我感兴趣的话题
     * @param $uid
     * @return mixed
     */
    public function focus_topic($uid)
    {
        $topic_list = Topic::getFocusTopicByRand($uid);
        $this->assign('topic_list',$topic_list);
        return $this->fetch('sidebar/focus_topic');
    }

    /**
     * 热门话题
     * @param $uid
     * @param array $where
     * @param array $order
     * @param int $limit
     * @return mixed
     */
	public function hot_topic($uid,$where=[],$order=[],$limit=5)
	{
		$topic_list = Topic::getHotTopics($uid,$where,$order,$limit);
		$this->assign('topic_list',$topic_list['data']);
		return $this->fetch('sidebar/hot_topic');
	}

    /**
     * 热门用户
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $limit
     * @return mixed
     */
	public function hot_users(int $uid=0, $where=[], $order=[], $limit=5)
    {
        $people_list = Users::getHotUsers($uid,$where,$order,$limit);
        $this->assign('people_list',$people_list['data']);
        return $this->fetch('sidebar/hot_users');
    }

    /**
     * 快捷菜单
     * @return mixed
     */
    public function write_nav()
    {
        return $this->fetch('sidebar/write_nav');
    }

	//侧边分类列表
    public function category($sort='',$category='')
    {
        $where =  ['status'=>1,'type'=>'all','pid'=>0];
        $list = db('category')->where($where)->order('sort','DESC')->column('id,title,icon');
        foreach ($list as $key=>$val)
        {
            $list[$key]['post_count'] = db('post_relation')->where(['category_id'=>$val['id'],'status'=>1])->count();
        }

        $this->assign([
            'list'=>$list,
            'sort'=>$sort,
            'category'=>$category,
            'total'=>db('post_relation')->where(['status'=>1])->count()
        ]);
        return $this->fetch('sidebar/category');
    }
}
