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
namespace plugins\third\lib;
use app\common\library\helper\RandomHelper;
use plugins\third\model\Third;
use app\common\model\Users;
use think\facade\Db;

/**
 * 第三方登录服务类
 */
class Service
{
    /**
     * 第三方登录
     * @param string $platform 平台
     * @param array $params 参数
     * @param array $extend 会员扩展信息
     * @return boolean
     */
    public static function connect(string $platform, $params = [], $extend = []): bool
    {
        $time = time();
        $values = [
            'platform'      => $platform,
            'openid'        => $params['openid'],
            'open_username'      => isset($params['user_info']['nickname']) ? $params['user_info']['nickname'] : '',
            'access_token'  => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'expires_in'    => $params['expires_in'],
            'login_time'     => $time,
            'expire_time'    => $time + $params['expires_in'],
        ];

        $third = db('third')->where(['platform' => $platform, 'openid' => $params['openid']])->find();
        if ($third) {
            $user = Users::getUserInfo($third['uid']);
            if (!$user) {
                return false;
            }
            $third->save($values);
        } else {
            // 先随机一个用户名,随后再变更为u+数字id
            $username = RandomHelper::alnum(intval(get_setting('username_min_length')));
            $password = RandomHelper::alnum(get_setting('password_min_length'));
            $domain = request()->host();

            Db::startTrans();
            try {
                // 默认注册一个会员
                $uid = Users::registerUser($username, $password,$extend);
                if (!$uid) {
                    return false;
                }
                $user = Users::getUserInfo( $uid);
                $fields = ['user_name' => 'u' . $user->id, 'email' => 'u' . $user->id . '@' . $domain];
                if (isset($params['user_info']['nick_name'])) {
                    $fields['nick_name'] = $params['user_info']['nickname'];
                }
                if (isset($params['user_info']['avatar'])) {
                    $fields['avatar'] = htmlspecialchars(strip_tags($params['user_info']['avatar']));
                }
                // 更新会员资料
                Users::updateUserFiled($uid,$fields);
                // 保存第三方信息
                $values['uid'] = $uid;
                Third::create($values);
                Db::commit();
            } catch (\PDOException $e) {
                Db::rollback();
                return false;
            }
        }
    }
}
