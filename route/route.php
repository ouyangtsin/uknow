<?php
//单页路由
use think\facade\Route;
if(defined(ENTRANCE) && ENTRANCE=='frontend')
{
    $list= db('route_rule')->select()->toArray();
    foreach ($list as $key => $value)
    {
        if($value['module']){
            Route::rule($value['rule'], $value['module'].'/'.$value['url']);
        }else{
            Route::rule($value['rule'], $value['url']);
        }
    }
}


