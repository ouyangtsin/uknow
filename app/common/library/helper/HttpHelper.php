<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

use think\facade\Request;

class HttpHelper
{

    /**
     * 发送一个POST请求
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param array $options 扩展参数
     * @return mixed|string
     */
    public static function post(string $url, $params = [], $options = []): string
    {
        return self::sendRequest($url, $params, 'POST', $options);
    }

    /**
     * 发送一个GET请求
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @param array $options 扩展参数
     * @return bool|string
     */
    public static function get(string $url, $params = [], $options = [])
    {
        return self::sendRequest($url, $params, 'GET', $options);
    }

    /**
     * CURL发送Request请求,含POST和REQUEST
     * @param string $url 请求的链接
     * @param mixed $params 传递的参数
     * @param string $method 请求的方法
     * @param mixed $options CURL的参数
     * @return bool|string
     */
    public static function sendRequest(string $url, $params = [], $method = 'POST', $options = [])
    {
        $method = strtoupper($method);
        $ip = request()->ip();
        $protocol = substr($url, 0, 5);
        $query_string = is_array($params) ? http_build_query($params) : $params;
        $ch = curl_init();
        $defaults = [];
        if ('GET' == $method) {
            $getUrl = $query_string ? $url . (stripos($url, "?") !== false ? "&" : "?") . $query_string : $url;
            $defaults[CURLOPT_URL] = $getUrl;
        } else {
            $defaults[CURLOPT_URL] = $url;
            if ($method == 'POST') {
                $defaults[CURLOPT_POST] = 1;
            } else {
                $defaults[CURLOPT_CUSTOMREQUEST] = $method;
            }
            $defaults[CURLOPT_POSTFIELDS] = $query_string;
        }
        $defaults[CURLOPT_HEADER] = false;
        $defaults[CURLOPT_USERAGENT] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.98 Safari/537.36";
        $defaults[CURLOPT_FOLLOWLOCATION] = true;
        $defaults[CURLOPT_RETURNTRANSFER] = true;
        $defaults[CURLOPT_CONNECTTIMEOUT] = 3;
        $defaults[CURLOPT_TIMEOUT] = 3;
        $defaults[CURLOPT_REFERER] = Request::domain();
        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:', 'CLIENT-IP:'. $ip));

        if ('https' == $protocol) {
            $defaults[CURLOPT_SSL_VERIFYPEER] = false;
            $defaults[CURLOPT_SSL_VERIFYHOST] = false;
        }

        curl_setopt_array($ch, (array)$options + $defaults);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    /**
     * 异步发送一个请求
     * @param string $url 请求的链接
     * @param mixed $params 请求的参数
     * @param string $method 请求的方法
     * @return boolean TRUE
     */
    public static function sendAsyncRequest(string $url, $params = [], $method = 'POST')
    {
        $method = strtoupper($method);
        $method = $method == 'POST' ? 'POST' : 'GET';
        //构造传递的参数
        if (is_array($params)) {
            $post_params = [];
            foreach ($params as $k => &$v) {
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
                $post_params[] = $k . '=' . urlencode($v);
            }
            $post_string = implode('&', $post_params);
        } else {
            $post_string = $params;
        }
        $parts = parse_url($url);
        //构造查询的参数
        if ($method == 'GET' && $post_string) {
            $parts['query'] = isset($parts['query']) ? $parts['query'] . '&' . $post_string : $post_string;
            $post_string = '';
        }
        $parts['query'] = isset($parts['query']) && $parts['query'] ? '?' . $parts['query'] : '';
        //发送socket请求,获得连接句柄
        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 3);
        if (!$fp) {
            return false;
        }
        //设置超时时间
        stream_set_timeout($fp, 3);
        $out = "{$method} {$parts['path']}{$parts['query']} HTTP/1.1\r\n";
        $out .= "Host: {$parts['host']}\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-Length: " . strlen($post_string) . "\r\n";
        $out .= "Connection: Close\r\n\r\n";
        if ($post_string !== '') {
            $out .= $post_string;
        }
        fwrite($fp, $out);
        fclose($fp);
        return true;
    }

    /**
     * 发送文件到客户端
     * @param string $file
     * @param bool   $delaftersend
     * @param bool   $exitaftersend
     */
    public static function sendToBrowser($file, $delaftersend = true, $exitaftersend = true)
    {
        if (file_exists($file) && is_readable($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename = ' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            if ($delaftersend) {
                unlink($file);
            }
            if ($exitaftersend) {
                exit;
            }
        }
    }

    /**
     * curl方式请求
     * @param $url
     * @param null $data
     * @param int $json 是否json方式
     * @return bool|string
     */
    public static function curlRequest($url,$data = null,$json=false)
    {
        $curl = curl_init();    // curl 设置
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  // 校验证书节点
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);// 校验证书主机
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect: ')); //头部要送出'Expect: '
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        if ($data )
        {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if($json)
        {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  // 以文件流的形式 把参数返回进来
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}
