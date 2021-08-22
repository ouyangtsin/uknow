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

use app\common\library\helper\HttpHelper as Http;
use think\facade\Session;

/**
 * 微博
 */
class Weibo
{
    const GET_AUTH_CODE_URL = "https://api.weibo.com/oauth2/authorize";
    const GET_ACCESS_TOKEN_URL = "https://api.weibo.com/oauth2/access_token";
    const GET_USERINFO_URL = "https://api.weibo.com/2/users/show.json";

    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    public function __construct($options = [])
    {
	    $pluginsConfig = get_plugins_config('third');
        $this->config = array_merge($this->config, $pluginsConfig);
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 登陆
     */
    public function login()
    {
        header("Location:" . $this->getAuthorizeUrl());
    }

    /**
     * 获取authorize_url
     */
    public function getAuthorizeUrl(): string
    {
        $state = md5(uniqid(rand(), true));
        session('state', $state);
        $queryArr = array(
            "response_type" => "code",
            "client_id"     => $this->config['weibo_app_id'],
            "redirect_uri"  =>isset($this->config['weibo_callback']) ? $this->config['weibo_callback'] :  (string)plugins_url('third://Index/callback', ['platform' => 'weibo']),
            "state"         => $state,
        );
        request()->isMobile() && $queryArr['display'] = 'mobile';
        return self::GET_AUTH_CODE_URL . '?' . http_build_query($queryArr);
    }

    /**
     * 获取用户信息
     * @param array $params
     * @return array
     */
    public function getUserInfo($params = []): array
    {
        $params = $params ? $params : $_GET;
        if (isset($params['access_token']) || (isset($params['state']) && $params['state'] == session('state') && isset($params['code']))) {
            //获取access_token
            $data = isset($params['code']) ? $this->getAccessToken($params['code']) : $params;
            $access_token = isset($data['access_token']) ? $data['access_token'] : '';
            $refresh_token = isset($data['refresh_token']) ? $data['refresh_token'] : '';
            $expires_in = isset($data['expires_in']) ? $data['expires_in'] : 0;
            if ($access_token) {
                $uid = isset($data['uid']) ? $data['uid'] : '';
                //获取用户信息
                $queryArr = [
                    "access_token" => $access_token,
                    "uid"          => $uid,
                ];
                $ret = Http::get(self::GET_USERINFO_URL, $queryArr);
                $userInfo = (array)json_decode($ret, true);
                if (!$userInfo || isset($userInfo['error_code'])) {
                    return [];
                }
                $userInfo = $userInfo ? $userInfo : [];
                $userInfo['nickname'] = isset($userInfo['screen_name']) ? $userInfo['screen_name'] : '';
                $userInfo['avatar'] = isset($userInfo['profile_image_url']) ? $userInfo['profile_image_url'] : '';
                $data = [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in'    => $expires_in,
                    'openid'        => $uid,
                    'userinfo'      => $userInfo
                ];
                return $data;
            }
        }
        return [];
    }

    /**
     * 获取access_token
     * @param string code
     * @return array
     */
    public function getAccessToken($code = '')
    {
        if (!$code) {
            return '';
        }
        $queryArr = array(
            "grant_type"    => "authorization_code",
            "client_id"     => $this->config['weibo_app_id'],
            "client_secret" => $this->config['weibo_app_secret'],
            "redirect_uri"  =>  isset($this->config['weibo_callback']) ? $this->config['weibo_callback'] : (string)plugins_url('third://Index/callback', ['platform' => 'weibo']),
            "code"          => $code,
        );
        $response = Http::post(self::GET_ACCESS_TOKEN_URL, $queryArr);
        $ret = (array)json_decode($response, true);
        return $ret ? $ret : [];
    }
}
