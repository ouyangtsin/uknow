<?php
/**
 * lemocms
 * ============================================================================
 * 版权所有 2018-2027 lemocms，并保留所有权利。
 * 网站地址: https://www.lemocms.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/9/5
 */

namespace app\admin\validate;

use think\Validate;

class WxAccount extends Validate
{
    protected $rule = [
        'wx_name|wx_name' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'weixin_account',
        ],
        'app_id|app_id' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'weixin_account',
        ],
        'app_secret|APP_SECRET' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'weixin_account',
        ],
        'origin_id|原始id' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'weixin_account',
        ],
        'w_token|w_token' => [
            'require' => 'weixin_account',
            'max'     => '255',
        ],
        'type|类型' => [
            'require' => 'require',
            'max'     => '2',
        ],
        'status|状态' => [
            'require' => 'require',
            'max'     => '1',
        ],
    ];
}