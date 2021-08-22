<?php
return [
	//系统配置分组
	'fieldType' => [
		'text' => '单行文本',
		'string' => '字符串',
		'password' => '密码',
		'textarea' => '文本框',
		'array' => '数组类型',
		'bool' => '布尔型',
		'select' => '下拉选择',
		'num' => '数字类型',
		'decimal' => '金额类型',
		'datetime' => '时间控件',
		'date' => '日期控件',
		'editor' => '编辑器',
		'bind' => '模型绑定',
		'image' => '图片上传',
		'images' => '多图上传',
		'attach' => '文件上传',
	],
    //多级菜单
	'multipleNav' => '0',
    //默认用户组
	'default_group' => 3,
    // 系统数据表
    'tables'            => [
        'auth_rule',
        'auth_group',
        'auth_group_access',
    ],
    // 系统标准模块
    'modules' => ['admin', 'common', 'install', 'api'],
];