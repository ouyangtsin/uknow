<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UKCMS
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowCMS一款基于TP6开发的内容管理系统
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

use think\facade\Request;

class CheckHelper
{
    /**
     * 检测用户在线状态
     */
    public static function checkOnline()
    {
        if (get_setting('online_check'))
        {
            $userOnlineDb = db('users_online');
            $user_info = session('login_user_info');
            if ($user_info && (time() - session('access_time') > 60)) {
                session('access_time', time());
            }
            // 在线更新
            $ip = IpHelper::getRealIp();
            $map[] = array('last_login_time','>', time() - (int)get_setting('online_check_time') * 60);
            $userOnlineDb->where($map)->delete();

            $updateData = array(
                'last_login_time'=>time(),
                'last_url' =>Request::url(),
                'user_agent'=>Request::server('HTTP_USER_AGENT'),
                'last_login_ip'=>IpHelper::getRealIp()
            );

            if ($user_info)
            {
                $map = array();
                $map[] = ['uid','=',$user_info['uid']];
                $map[] = ['last_login_ip','=',$ip];
                $id = $userOnlineDb->where($map)->value('id');
                $updateData['uid'] = $user_info['uid'];
                if($id)
                {
                    $userOnlineDb->where(['id'=> $id])->save($updateData);
                }else{
                    $userOnlineDb->insert($updateData);
                }
            } else {
                unset($map);
                $map[] = array('last_login_ip','=', $ip);
                $id = $userOnlineDb->where($map)->value('id');
                $updateData['uid'] = 0;
                if (!$id) {
                    $userOnlineDb->insert($updateData);
                } else {
                    $userOnlineDb->where(['id'=> $id])->save($updateData);
                }
            }
        }
    }

    /**
     * 检测网站是否关闭
     * @return bool
     */
    public static function checkSiteStatus(): bool
    {
        if(strtolower(request()->controller()) !== 'account' && strtolower(request()->action()) !== 'login' && (int)get_setting('close_site') && get_user_info(0,'group_id')!==1 && get_user_info(0,'group_id')!==2)
        {
            session('login_uid',null);
            session('login_user_info',null);
            return false;
        }
        return true;
    }

    /**
     * 检查游客权限
     */
    public static function checkTouristPermission()
    {
        $permission = db('auth_group')->where('id',5)->value('permission');
        return json_decode($permission,true);
    }
}