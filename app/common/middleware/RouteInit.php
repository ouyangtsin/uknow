<?php
namespace app\common\middleware;
use app\admin\model\Module;
use think\exception\HttpResponseException;
use think\Request;

class RouteInit
{
    public function handle(Request $request, \Closure $next)
    {
        $module = app('http')->getName();
        if($module==='plugins')
        {
            app()->setNamespace('plugins');
        } else if(!Module::where(['name'=>$module,'status'=>1])->value('name'))
        {
            $response = view(app('config')->get('app.dispatch_error_tmpl'),[
                'code' => 0,
                'msg'  => '模块不存在或未启用',
                'data' => [],
                'url'  => base64_decode(session('return_url')),
                'wait' => 3,
            ]);
            throw new HttpResponseException($response);
        }
        return $next($request);
    }
}