<?php

// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UK
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
// [ 应用入口文件 ]
namespace think;
// 声明全局变量
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__) . DS);
define('ENTRANCE', 'wap');
require __DIR__ . '/../vendor/autoload.php';

// 判断是否安装程序
if (!is_file(ROOT_PATH . 'install' . DS . 'lock' . DS . 'install.lock')) {
    exit(header("location:/install.php"));
}
//载入版本
include ROOT_PATH . 'version.php';
// 执行HTTP应用并响应
$http = (new App())->http;
$response = $http->run();
$response->send();
$http->end($response);
