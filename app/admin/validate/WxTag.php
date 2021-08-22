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

class WxTag extends Validate
{
    protected $rule = [
        'name|标签名' => [
            'require' => 'require',
            'max'     => '255',
            'unique'  => 'wx_tag',
        ],
        'status|状态' => [
            'require' => 'require',
            'max'     => '1',
        ],


    ];

}