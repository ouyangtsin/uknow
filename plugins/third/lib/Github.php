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

use app\common\library\helper\HttpHelper;

class Github
{
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_USERINFO_URL = "https://graph.qq.com/user/get_user_info";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

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
            "client_id"     => $this->config['qq_app_id'],
            "redirect_uri"  => isset($this->config['qq_callback']) ? $this->config['qq_callback'] : (string)plugins_url('third://Index/callback', ['platform' => 'qq']),
            "scope"         => $this->config['qq_scope'] ? $this->config['qq_scope'] :'get_user_info',
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
                $openid = $this->getOpenId($access_token);
                //获取用户信息
                $queryArr = [
                    "access_token"       => $access_token,
                    "oauth_consumer_key" => $this->config['qq_app_id'],
                    "openid"             => $openid,
                ];
                $ret = HttpHelper::get(self::GET_USERINFO_URL, $queryArr);
                $userInfo = (array)json_decode($ret, true);
                if (!$userInfo || !isset($userInfo['ret']) || $userInfo['ret'] !== 0) {
                    return [];
                }
                $userInfo['avatar'] = isset($userInfo['figureurl_qq_2']) ? $userInfo['figureurl_qq_2'] : '';
                $data = [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in'    => $expires_in,
                    'openid'        => $openid,
                    'userinfo'      => $userInfo
                ];
                return $data;
            }
        }
        return [];
    }

    /**
     * 获取access_token
     * @param string $code
     * @return array
     */
    public function getAccessToken($code = ''): array
    {
        if (!$code) {
            return [];
        }
        $queryArr = array(
            "grant_type"    => "authorization_code",
            "client_id"     => $this->config['qq_app_id'],
            "client_secret" => $this->config['qq_app_secret'],
            "redirect_uri"  => isset($this->config['qq_callback']) ? $this->config['qq_callback'] : (string)plugins_url('third://Index/callback', ['platform' => 'qq']),
            "code"          => $code,
        );
        $ret = HttpHelper::get(self::GET_ACCESS_TOKEN_URL,$queryArr);
        $params = [];
        parse_str($ret, $params);
        return $params ? $params : [];
    }

    /**
     * 获取open_id
     * @param string $access_token
     * @return string
     */
    private function getOpenId($access_token = ''): string
    {
        $response = HttpHelper::get(self::GET_OPENID_URL, ['access_token' => $access_token]);
        if (strpos($response, "callback") !== false) {
            $left_pos = strpos($response, "(");
            $right_pos = strrpos($response, ")");
            $response = substr($response, $left_pos + 1, $right_pos - $left_pos - 1);
        }
        $user = (array)json_decode($response, true);
        return isset($user['openid']) ? $user['openid'] : '';
    }
}
