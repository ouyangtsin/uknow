<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;
use Endroid\QrCode\QrCode;
use We;
use WeChat\Exceptions\InvalidResponseException;
use WeChat\Exceptions\LocalCacheException;
use WeChat\Pay;
use function Symfony\Component\String\s;

/**
 * 支付处理
 * Class PayHelper
 * @package plugins\pay\library
 */
class PayHelper
{
    /**
     * 微信支付配置
     * @var array|array[]
     */
    protected static $wechatConfig;

    /**
     * 支付宝支付配置
     * @var array|array[]
     */
    protected static $alipayConfig;

    /**
     * 微信支付SDK对象
     * @var
     */
    public static $wechatObj;

    /**
     * 错误提示句柄
     * @var
     */
    public static  $error='';

    /**
     * 构造函数
     * PayHelper constructor.
     */
    public function __construct()
    {
        //检查是否启用支付功能
        /*if(!get_setting('pay_enable'))
        {
            $this->setError('未启用支付功能');
            return false;
        }

        //检查支付功能配置完整性
        if(in_array('wechat',get_setting('pay_type')))
        {
            if(!get_setting('wechat_app_id') || !get_setting('wechat_mch_id') || !get_setting('wechat_mch_key'))
            {
                $this->setError('微信支付配置不完整');
                return false;
            }
        }

        if(in_array('alipay',get_setting('pay_type')))
        {
            if(!get_setting('alipay_app_id') || !get_setting('public_key') || !get_setting('private_key'))
            {
                $this->setError('支付宝支付配置不完整');
                return false;
            }
        }*/

        //微信配置
        self::$wechatConfig = [
            'token'          => 'test',
            'appid'          => get_setting('wechat_app_id'),
            'appsecret'      => '71308e96a204296c57d7cd4b21b883e8',
            'encodingaeskey' => 'BJIUzE0gqlWy0GxfPp4J1oPTBmOrNDIGPNav1YFH5Z5',
            // 配置商户支付参数（可选，在使用支付功能时需要）
            'mch_id'         => get_setting('wechat_mch_id'),
            'mch_key'        => get_setting('wechat_mch_key'),
            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
            'ssl_key'        => get_setting('ssl_cer'),
            'ssl_cer'        => get_setting('ssl_key'),
            // 缓存目录配置（可选，需拥有读写权限）
            'cache_path'     => '',
        ];

        self::$alipayConfig = [
            // 沙箱模式
            'debug'       => true,
            // 签名类型（RSA|RSA2）
            'sign_type'   => "RSA2",
            // 应用ID
            'appid'       => get_setting('alipay_app_id'),
            // 支付宝公钥文字内容 (1行填写，特别注意：这里是支付宝公钥，不是应用公钥，最好从开发者中心的网页上去复制)
            'public_key'  => get_setting('public_key'),
            // 支付宝私钥文字内容 (1行填写)
            'private_key' => get_setting('private_key'),
            // 应用公钥证书完整内容（新版资金类接口转 app_cert_sn）
            'app_cert'    => '',
            // 支付宝根证书完整内容（新版资金类接口转 alipay_root_cert_sn）
            'root_cert'   => '',
            // 支付成功通知地址
            'notify_url'  => '',
            // 网页支付回跳地址
            'return_url'  => '',
        ];
    }

    /**
     * 获取微信支付实例
     * @param array $config
     */
    public static function WechatPay($config=[]): Pay
    {
        $config = $config ? array_merge(self::$wechatConfig,$config) : self::$wechatConfig;
        if (is_null(self::$wechatObj)) {
            self::$wechatObj = new Pay($config);
        }
        return self::$wechatObj;
    }

    /**
     * 微信扫码支付
     * 返回为base64图片
     */
    public static function getWechatScanImage($data=[])
    {
        $options = [
            'body'             => $data['body'],
            'out_trade_no'     => RandomHelper::alnum(11),
            'product_id'       =>$data['product_id'],
            'total_fee'        => $data['amount'],
            'trade_type'       => 'NATIVE',
            'spbill_create_ip' => '127.0.0.1',
        ];
        // 生成预支付码
        try {
            $result = self::WechatPay()->createOrder($options);
            $code_url = $result['code_url'];
            //$code_url = 'https://v1.demo.uknowing.com/';
            if($code_url)
            {
                return self::qrcode($code_url);
            }
            self::setError('二维码获取失败');
            return  false;
        } catch (\Exception $e) {
            self::setError($e->getMessage() . PHP_EOL);
            return  false;
        }
    }

    /**
     * 支付宝扫码支付
     * 返回为base64图片
     */
    public static function getAlipayScanImage($data=[])
    {
        $options = [
            'out_trade_no'=>$data['out_trade_no'],
            'total_amount'=>$data['amount'],
            'subject'=>$data['body'],
        ];
        try {
            $result = We::AliPayScan($options);
            if($result['qr_code'])
            {
                return self::qrcode($result['qr_code']);
            }
            self::setError('二维码获取失败');
            return  false;
        } catch (\Exception $e) {
            self::setError($e->getMessage() . PHP_EOL);
            return  false;
        }
    }

    /**
     * 查询订单支付状态
     * @param $out_trade_no
     * @return array|string
     */
    public static function checkOrderStatus($out_trade_no)
    {
        $options = [
            'out_trade_no'=>$out_trade_no
        ];
        try {
            return self::WechatPay()->queryOrder($options);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function qrcode($url): string
    {
        $qrCode = new QrCode($url);
        return $qrCode->writeDataUri();
    }

    /**
     * 设置错误信息
     * @param $error
     * @return mixed
     */
    public static function setError($error) {
        return self::$error = $error;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public static function getError(): string
    {
        return self::$error;
    }
}