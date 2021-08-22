<?php
return [
	//系统配置分组
	'fieldTypeList'=>[
		'text' => ['title' => '单行文本', 'type' => 'varchar'],
		'string' => ['title' => '字符串', 'type' => 'int'],
		'password' => ['title' => '密码', 'type' => 'varchar'],
		'textarea' => ['title' => '文本框', 'type' => 'text'],
		'bool' => ['title' => '布尔型', 'type' => 'int'],
		'select' => ['title' => '选择', 'type' => 'varchar'],
		'num' => ['title' => '数字', 'type' => 'int'],
		'decimal' => ['title' => '金额', 'type' => 'decimal'],
		'tags' => ['title' => '标签', 'type' => 'varchar'],
		'datetime' => ['title' => '时间控件', 'type' => 'int'],
		'date' => ['title' => '日期控件', 'type' => 'varchar'],
		'editor' => ['title' => '编辑器', 'type' => 'text'],
		'bind' => ['title' => '模型绑定', 'type' => 'int'],
		'image' => ['title' => '图片上传', 'type' => 'int'],
		'images' => ['title' => '多图上传', 'type' => 'varchar'],
		'attach' => ['title' => '文件上传', 'type' => 'varchar'],
	],

	'fieldType'=>[
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
		'images' =>'多图上传',
		'attach' => '文件上传',
	]
];