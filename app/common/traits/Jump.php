<?php

namespace app\common\traits;

use think\exception\ClassNotFoundException;
use think\exception\HttpResponseException;
use think\Response;

/**
 * Trait Jump
 * @package app\common\traits
 */
trait Jump
{

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param null $url 跳转的 URL 地址
     * @param mixed $data 返回的数据
     * @param int $wait 跳转等待时间
     * @param array $header 发送的 Header 信息
     * @return void
     */
    protected function success($msg = '', $url = null,$data = '', $wait = 3, array $header = []): void
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = base64_decode(session('return_url'));
        } elseif ($url) {
            $url = (string)$url;
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }

        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        $type = $type = $this->getResponseType();
        if ($type === 'html') {
            $response = view(app('config')->get('app.dispatch_success_tmpl'), $result);
        } elseif ($type === 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param null $url 跳转的 URL 地址
     * @param mixed $data 返回的数据
     * @param int $wait 跳转等待时间
     * @param array $header 发送的 Header 信息
     * @return void
     */
    protected function error($msg = '',  $url = null,$data = '', $wait = 3, array $header = []): void
    {
        if (is_null($url)) {
            $url = request()->isAjax() ? '' : base64_decode(session('return_url'));
        } elseif ($url) {
            $url = (string)$url;
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }

        $type   = $this->getResponseType();
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];
        if ($type === 'html') {
            $response = view(app('config')->get('app.dispatch_error_tmpl'), $result);

        } elseif ($type === 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 自定义加载中动画
     * @access protected
     * @param null $url 跳转的 URL 地
     * @param int $wait 跳转等待时间
     * @return void
     */
    protected function loading($url = null,$wait = 3): void
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = base64_decode(session('return_url'));
        } elseif ($url) {
            $url = (string)$url;
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }

        $result = [
            'code' => 1,
            'msg'  => '',
            'data' => '',
            'url'  => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type === 'html') {
            $response = view(app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'jump.tpl', $result);
        } elseif ($type === 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param int $code 返回的 code
     * @param mixed $msg 提示信息
     * @param string $type 返回数据格式
     * @param array $header 发送的 Header 信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = []): void
    {
        $result   = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * URL 重定向
     * @access protected
     * @param $url
     * @param array|int $params 其它 URL 参数
     * @param int $code http code
     * @return void
     */
    protected function redirect($url, $params = [], $code = 302): void
    {
        if (is_int($params)) {
            $code   = $params;
            $params = [];
        }
		$url = is_object($url) ? $url.http_build_query($params) : url($url,$params);
        $response = Response::create($url, 'redirect', $code);
        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(): string
    {
        return (request()->isJson() || request()->isAjax() || request()->isPost()) ? 'json' : 'html';
    }
}
