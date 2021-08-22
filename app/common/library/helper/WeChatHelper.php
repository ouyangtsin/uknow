<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;
use EasyWeChat\Factory;

class WeChatHelper
{
    protected $config = [
        'log' => [
            'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
            'channels' => [
                // 测试环境
                'dev' => [
                    'driver' => 'single',
                    'path' => '../runtime/wechat.log',
                    'level' => 'debug',
                ],
                // 生产环境
                'prod' => [
                    'driver' => 'daily',
                    'path' => '../runtime/wechat.log',
                    'level' => 'info',
                ],
            ],
        ],
        'http' => [
            'max_retries' => 1,
            'retry_delay' => 500,
            'timeout' => 5.0,
        ],
        /**
         * OAuth 配置
         * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
         * callback：OAuth授权完成后的回调页地址
         */
        'oauth' => [
            'scopes' => ['snsapi_userinfo'],
            'callback' => '/examples/oauth_callback.php', //回掉地址
        ],
    ];
    public $wechatAccount;
    public $wechatFactory;
    protected static $instance;
    public function __construct()
    {
        $this->wechatAccount = db('wechat_account')->where(['status'=> 1])->find();
        if ($this->wechatAccount)
        {
            $this->config = array_merge($this->config,[
                'app_id' => $this->wechatAccount['app_id'],
                'secret' => $this->wechatAccount['app_secret'],
                'token' => $this->wechatAccount['token'],
                'response_type' => 'array',
            ]);
            $this->wechatFactory = cache('wechat_app_'.$this->wechatAccount['app_id']);

            if(!$this->wechatFactory){
                $this->wechatFactory = Factory::officialAccount($this->config);
                cache('wechat_app_'.$this->wechatAccount['app_id'],$this->wechatFactory);
            }
        } else {
            return false;
        }
    }

    /**
     * 初始化
     * @return WeChatHelper
     */
    public static function instance(): WeChatHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}