<?php
namespace app\admin\validate;
use think\Validate;

class AuthGroup extends Validate
{
    protected $rule = [
        'title|用户组名称' => [
            'require' => 'require',
        ],
    ];
}