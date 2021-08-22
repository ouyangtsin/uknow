<?php

namespace app\admin\validate;

use think\Validate;

class ModuleField extends Validate
{
    protected $rule = [
        'module_id|所属模块' => [
            'require' => 'require',
            'max'     => '3',
        ],
        'type|字段类型' => [
            'require' => 'require',
            'max'     => '40',
        ],
        'field|字段名' => [
            'require' => 'require',
            'max'     => '40',
        ],
        'name|别名' => [
            'require' => 'require',
            'max'     => '30',
        ],
        'minlength|字符长度' => [
            'max' => '10',
        ],
        'maxlength|字符长度' => [
            'max' => '10',
        ],
        'sort|排序' => [
            'require' => 'require',
            'number'  => 'number',
            'max'     => '10',
        ]
    ];
}