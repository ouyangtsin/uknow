<?php
namespace app\common\middleware;
use app\admin\model\Module;
use think\exception\HttpResponseException;
use think\facade\Config;
use think\Request;

class AppInit
{
    public function handle(Request $request, \Closure $next)
    {
        //设置默认模块
        $defaultModule = Module::where(['default'=>1,'status'=>1])->value('name');
        $defaultModule  = $defaultModule ?: 'ask';
        Config::set(['default_app'=>$defaultModule],'app');
        return $next($request);
    }
}