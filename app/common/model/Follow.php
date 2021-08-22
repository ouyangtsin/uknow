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

class Follow extends BaseModel
{
	protected $name = 'users_follow';

    /**
     * 检查用户是否关注
     * @param $friend_uid
     * @param $fans_uid
     * @return false|mixed
     */
	public static function checkUserFollow($friend_uid,$fans_uid)
	{
		if (!$fans_uid OR !$friend_uid)
		{
			return false;
		}

		if ($fans_uid == $friend_uid)
		{
			return false;
		}
		return db('users_follow')->where(['fans_uid'=>intval($fans_uid),'friend_uid'=>intval($friend_uid),'status'=>1])->value('id');
	}

    /**
     * 获取我关注的用户列表
     * @param $uid
     * @param bool $follow
     * @return array|false|false[]
     */
	public static function followUserList($uid,$follow=false)
    {
        $where['status'] = 1;
        if($follow)
        {
            $where['friend_uid'] = intval($uid);
        }else{
            $where['fans_uid'] = intval($uid);
        }
        $follow_list = db('users_follow')->where($where)->column('friend_uid,fans_uid');

        $follow_uid = $follow ? array_column($follow_list,'fans_uid') : array_column($follow_list,'friend_uid');

        return Users::getUserInfoByIds($follow_uid);
    }
}