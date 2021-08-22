<?php

namespace app\admin\validate;

use think\Validate;

class Module extends Validate
{
    protected $rule = [
        'module_name|模块名称' => [
            'require' => 'require',
            'max'     => '100',
        ],
        'table_name|表名称' => [
            'require' => 'require',
            'max'     => '50',
        ],
        'table_comment|表描述' => [
            'max'     => '200',
        ],
        'module_type|表类型' => [
            'require' => 'require',
            'max'     => '10',
        ],
        'pk|主键' => [
            'require' => 'require',
            'max'     => '50',
        ],
        'sort|排序' => [
            'require' => 'require',
            'number'  => 'number',
            'max'     => '3',
        ]
    ];

}