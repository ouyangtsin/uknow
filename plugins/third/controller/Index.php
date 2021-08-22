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

namespace plugins\third\controller;

use app\common\controller\Frontend;
use plugins\third\lib\Application;
use plugins\third\lib\Service;
use plugins\third\model\Third;
use think\App;

/**
 * 第三方登录插件
 * Class Index
 * @package plugins\third\controller
 */
class Index extends Frontend
{
    protected $application = null;
    protected $options = [];
    protected $config;

    public function __construct(App $app)
    {
        parent::__construct($app);
	    $this->config = get_plugins_config('third');
	    $this->application = new Application($this->config);
    }

	/**
     * 插件首页
     */
    public function index()
    {
        $platformList = [];
        if ($this->user_id) {
            $platformList = db('third')->where('uid', $this->user_id)->column('platform');
        }
        $this->assign('platformList', $platformList);
        return $this->fetch();
    }

    /**
     * 发起授权
     */
    public function connect()
    {
        $platform = $this->request->param('platform');
        $url = $this->request->request('url', $this->request->server('HTTP_REFERER', '/'), 'trim');
        if (!$this->application->{$platform}) {
            $this->error('参数错误');
        }
        if ($url) {
            session("redirect_url", $url);
        }
        // 跳转到登录授权页面
        $url = $this->application->{$platform}->getAuthorizeUrl();
        return redirect($url);
    }

    /**
     * 通知回调
     */
    public function callback()
    {
        $platform = $this->request->param('platform');
        // 成功后返回之前页面
        $url = session("redirectUrl") ? session("redirectUrl") : url('/');
        // 授权成功后的回调
        $user_info = $this->application->{$platform}->getUserInfo();
        if (!$user_info) {
            $this->error('操作失败', $url);
        }

        session("{$platform}-user-info", $user_info);
        //判断是否启用账号绑定
        $third = db('third')->where(['platform' => $platform, 'openid' => $user_info['openid']])->find();
        if (!$third) {
            //要求绑定账号或会员当前是登录状态
            if ($this->config['bind_account'] || $this->user_id) {
                return redirect((string)plugins_url('third://Index/prepare',['platform' => $platform, 'url' => $url]));
            }
        }

        $loginRet = Service::connect($platform, $user_info);
        if ($loginRet) {
            return redirect($url);
        }
    }

    /**
     * 准备绑定
     */
    public function prepare()
    {
        $platform = $this->request->request('platform');
        $url = $this->request->get('url', '/');
        if ($this->auth->id) {
            $this->redirect((string)plugins_url("third://Index/bind",['platform' => $platform, 'url' => $url]));
        }

        // 授权成功后的回调
        $userinfo = session("{$platform}-userinfo");
        if (!$userinfo) {
            $this->error("操作失败，请返回重度");
        }

        $this->view->assign('userinfo', $userinfo['userinfo']);
        $this->view->assign('platform', $platform);
        $this->view->assign('url', $url);
        $this->view->assign('bind_url',(string) plugins_url("third://Index/bind",['platform' => $platform, 'url' => $url]));
        return $this->view->fetch();
    }

    /**
     * 绑定账号
     */
    public function bind()
    {
        $platform = $this->request->request('platform');
        $url = $this->request->get('url', $this->request->server('HTTP_REFERER'));
        if (!$platform) {
            $this->error("参数不正确");
        }

        // 授权成功后的回调
        $userinfo = session("{$platform}-userinfo");
        if (!$userinfo) {
            return redirect((string)plugins_url('third://Index/connect', ['platform' => $platform,'url'=>urlencode($url)]));
        }

        $third = db('third')->where('uid', $this->auth->id)->where('platform', $platform)->find();
        if ($third) {
            $this->error("已绑定账号，请勿重复绑定");
        }
        $time = time();
        $values = [
            'platform'      => $platform,
            'user_id'       => $this->auth->id,
            'openid'        => $userinfo['openid'],
            'open_name'      => isset($userinfo['userinfo']['nickname']) ? $userinfo['userinfo']['nickname'] : '',
            'access_token'  => $userinfo['access_token'],
            'refresh_token' => $userinfo['refresh_token'],
            'expires_in'    => $userinfo['expires_in'],
            'login_time'     => $time,
            'expire_time'    => $time + $userinfo['expires_in'],
        ];
        $third = Third::create($values);
        if ($third) {
            $this->success("账号绑定成功", $url);
        } else {
            $this->error("账号绑定失败，请重试", $url);
        }
    }

    /**
     * 解绑账号
     */
    public function unbind()
    {
        $platform = $this->request->request('platform');
        $third = db('third')->where('user_id', $this->auth->id)->where('platform', $platform)->find();
        if (!$third) {
            $this->error("未找到指定的账号绑定信息");
        }
        $third->delete();
        $this->success("账号解绑成功");
    }
}
