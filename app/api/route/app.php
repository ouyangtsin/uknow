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

use think\facade\Route;
/*////一般路由规则，访问的url为：v1/user/1,对应的文件为Address类下的read方法
Route::get(':version/address/:id','api/:version.user/address');
//
////资源路由，详情查看tp手册资源路由
Route::resource(':version/user','api/:version.user')->app('api');*/

$version = request()->header('version');//默认跳转到v1版本
if($version==null)$version = "v1";
Route::rule(':controller/:function', $version.'.:controller/:function');
/*Route::any(':version/:controller/:function', ':version.:controller/:function')
    ->allowCrossDomain([
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET,POST,OPTIONS',
        'Access-Control-Allow-Headers' => 'x-requested-with,content-type,token']);*/

