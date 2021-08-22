<?php
return [
	'fileMaxSize'=>[
		'title' => '上传限制',
		'type' => 'text',
		'value' => '10',
        'options' => [

        ],
		'tips' => '最大上传限制(单位M)'
	],
    'timeout'=>array(
		'title' => '超时时间',
		'type' => 'text',
        'options' => [

        ],
		'value' => '30000',
		'tips' => '超时时间,单位(毫秒)'
	),
];