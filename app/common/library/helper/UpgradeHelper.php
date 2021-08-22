<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

use think\facade\Db;

/**
 * Class UpgradeHelper
 * @package app\common\library\helper
 */
class UpgradeHelper
{
    protected static $instance;
    public static function instance(): UpgradeHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    // 检测是否有新版本
    public function checkVersion(): array
    {
        $server = HttpHelper::post(UK_URL.'/api/cloud/check');
        $server = str_replace('.','',$server);
        if($server)
        {
            if (intval($server) > intval(str_replace('.','',UK_VERSION))) {
                $result= [
                    'code'=>200,
                    'msg'=>'服务器有新版本',
                    'data'=>$server
                ];
            } else {
                $result= [
                    'code'=>201,
                    'msg'=>'已经是最新版本',
                    'data'=>$server
                ];
            }
        }else{
            $result= [
                'code'=>0,
                'msg'=>'通信异常',
                'data'=>''
            ];
        }
        return $result;
    }

    /**
     * 解压缩
     * @param string $file 要解压的文件
     * @param string $to_dir 要存放的目录
     * @return int
     */
    public function dealZip(string $file, string $to_dir): int
    {
        if (trim($file) == '') {
            return 406;
        }
        if (trim($to_dir) == '') {
            return 406;
        }
        $zip = new \ZipArchive;
        // 中文文件名要使用ANSI编码的文件格式
        if ($zip->open($file) === TRUE) {
            //提取全部文件
            $zip->extractTo($to_dir);
            $zip->close();
            $result = 200;
        } else {
            $result = 406;
        }
        return $result;
    }

    /**
     * 遍历当前目录不包含下级目录
     * @param string $dir 要遍历的目录
     * @param string $file 要过滤的文件
     * @return array|false
     */
    public function scanDir(string $dir, $file='')
    {
        if (trim($dir) == '') {
            return false;
        }
        $file_arr = scandir($dir);
        $new_arr = [];
        foreach($file_arr as $item){
            if($item!=".." && $item !="." && $item != $file){
                $new_arr[] = $item;
            }
        }
        return $new_arr;
    }

    /**
     * 合并目录且只覆盖不一致的文件
     * @param string $source 要合并的文件夹
     * @param string $target 要合并的目的地
     * @return int 处理的文件数
     */
    public function copyMerge(string $source, string $target) {
        if (trim($source) == '') {
            return false;
        }
        if (trim($target) == '') {
            return false;
        }
        // 路径处理
        $source = preg_replace ( '#/\\\\#', DIRECTORY_SEPARATOR, $source );
        $target = preg_replace ( '#\/#', DIRECTORY_SEPARATOR, $target );
        $source = rtrim ( $source, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        $target = rtrim ( $target, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        // 记录处理了多少文件
        $count = 0;
        // 如果目标目录不存在，则创建。
        if (! is_dir ( $target )) {
            mkdir ( $target, 0777, true );
            $count ++;
        }
        // 搜索目录下的所有文件
        foreach ( glob ( $source . '*' ) as $filename ) {
            if (is_dir ( $filename )) {
                // 如果是目录，递归合并子目录下的文件。
                $count += $this->copy_merge ( $filename, $target . basename ( $filename ) );
            } elseif (is_file ( $filename )) {
                // 如果是文件，判断当前文件与目标文件是否一样，不一样则拷贝覆盖。
                // 这里使用的是文件md5进行的一致性判断，可靠但性能低。
                if (! file_exists ( $target . basename ( $filename ) ) || md5 ( file_get_contents ( $filename ) ) != md5 ( file_get_contents ( $target . basename ( $filename ) ) )) {
                    copy ( $filename, $target . basename ( $filename ) );
                    $count ++;
                }
            }
        }

        // 返回处理了多少个文件
        return $count;
    }

    /**
     * 遍历删除文件
     * @param string $dir 要删除的目录
     * @return bool 成功与否
     */
    public function delDir(string $dir): bool
    {
        if (trim($dir) == '') {
            return false;
        }
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullPath=$dir."/".$file;
                if(!is_dir($fullPath)) {
                    unlink($fullPath);
                } else {
                    $this-> delDir($fullPath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 遍历执行sql文件
     * @param string $dir 要执行的目录
     * @return bool 成功与否
     */
    public function fetchSql(string $dir): bool
    {
        if (trim($dir) == '') {
            return false;
        }
        $sql_file_res = $this->scanDir($dir);
        if (!empty($sql_file_res)) {
            foreach ($sql_file_res as $k => $v) {
                if (!empty(strstr($v,'.sql'))) {
                    $sql_content = file_get_contents($dir.$v);
                    $sql_arr = explode(';', $sql_content);

                    //执行sql语句
                    foreach ($sql_arr as $vv) {
                        if (!empty($vv)) {
                            $sql_res = Db::execute($vv.';');
                            if (empty($sql_res)) {
                                return false;
                            }
                        }
                    }
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * 下载程序压缩包文件
     * @param string $url 要下载的url
     * @param string $save_dir 要存放的目录
     * @return array|false
     */
    public function downFile(string $url, string $save_dir) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            return false;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        $filename = basename($url);
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //开始下载
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);

        // 判断执行结果
        if ($status['http_code'] ==200) {
            $size = strlen($content);
            //文件大小
            $fp2 = @fopen($save_dir . $filename , 'a');
            fwrite($fp2, $content);
            fclose($fp2);
            unset($content, $url);
            $res = [
                'status' =>$status['http_code'] ,
                'file_name' => $filename,
                'save_path' => $save_dir . $filename
            ];
        } else {
            $res = false;
        }

        return $res;
    }

    /**
     * 获取文件内容
     * @param $url
     * @return false|string
     */
    public function getFile($url){
        if (trim($url) == '') {
            return false;
        }
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'timeout'=>3,//单位秒
            )
        );
        $cnt=0;
        while($cnt<3 && ($res=@file_get_contents($url, false, stream_context_create($opts)))===FALSE) $cnt++;
        if ($res === false) {
            return false;
        } else {
            return $res;
        }
    }

    // 在线更新
    public function system_update()
    {
        // 访问服务器判断有效期：
        $res = [];

        if ($res->data === false) {
            // 不在有效期
            $result = [
                'code'=>401,
                'msg'=>'已过服务有效期',
                'data'=>''
            ];
        } else {
            // 有效期内 开始更新
            // 设定目录
            // 根目录
            $base_dir = ROOT_PATH;
            // 服务器更新路径
            $update_res = 'http://192.168.31.64/tp5/public/update/';
            // 本地更新路径
            $local_up_dir = $base_dir.'public/update/';
            // 本地缓存路径
            $path = $base_dir . 'public\update\cache';
            // 没有就创建
            if(!is_dir($path)){
                mkdir(iconv("UTF-8", "GBK", $path),0777,true);
            }
            // 设定缓存目录名称
            $cache_dir = $path.'\\';


            // 看看需要下载几个版本的压缩包
            // 服务器更新日志存放路径
            $server = $this->getFile($update_res.'up_log.txt');
            if ($server === false) {
                $result = [
                    'code'=>406,
                    'msg'=>'服务器更新日志获取失败',
                    'data'=>''
                ];
            }else{
                // 版本记录
                $server = explode(",", $server);
                $local = $this->getFile($local_up_dir.'ver.txt');
                if ($local === false) {
                    $result = [
                        'code'=>406,
                        'msg'=>'本地更新日志获取失败',
                        'data'=>''
                    ];
                } else {
                    // 循环比较是否需要下载 更新
                    foreach ($server as $key => $value) {
                        if ($local < $value) {
                            // 获取更新信息
                            // 服务器各个程序包日志存放路径
                            $up_info = $this->getFile($update_res.$value.'/version.txt');

                            // 判断是否存在
                            if ($up_info === false) {
                                $result = [
                                    'code'=>406,
                                    'msg'=>'服务器更新包不存在',
                                    'data'=>''
                                ];
                            } else {
                                // 信息以json格式存储便于增减和取值 故解析json对象
                                $up_info = json_decode($up_info);


                                // 下载文件
                                $back = $this->getFile($up_info->download,$cache_dir);

                                if (empty($back)) {
                                    $result = [
                                        'code'=>406,
                                        'msg'=>'升级程序包下载失败',
                                        'data'=>''
                                    ];
                                } else {
                                    //下载成功 解压缩
                                    $zip_res = $this->dealZip($back['save_path'] ,$cache_dir);

                                    // 判断解压是否成功
                                    if ($zip_res == 406) {
                                        $result = [
                                            'code'=>406,
                                            'msg'=>'文件解压缩失败',
                                            'data'=>''
                                        ];
                                    } else {
                                        // 开始更新数据库和文件

                                        // sql文件
                                        //读取文件内容遍历执行sql
                                        $sql_res = $this->fetchSql($cache_dir.'mysql\\');
                                        if ($sql_res === false) {
                                            $result = [
                                                'code'=>406,
                                                'msg'=>'sql文件写入失败',
                                                'data'=>''
                                            ];
                                        } else {
                                            // php文件合并 返回处理的文件数
                                            $file_up_res = $this->copyMerge($cache_dir.'program\\',$base_dir);
                                            if (empty($file_up_res)) {
                                                $result = [
                                                    'code'=>406,
                                                    'msg'=>'文件移动合并失败',
                                                    'data'=>''
                                                ];
                                            }else{
                                                // 更新完改写网站本地版号
                                                $write_res = file_put_contents($local_up_dir . 'ver.txt', $value);
                                                if (empty($write_res)) {
                                                    $result = [
                                                        'code'=>406,
                                                        'msg'=>'本地更新日志改写失败',
                                                        'data'=>''
                                                    ];
                                                }else{
                                                    // 删除临时文件
                                                    $del_res = $this->delDir($cache_dir);
                                                    if (empty($del_res)) {
                                                        $result = [
                                                            'code'=>406,
                                                            'msg'=>'更新缓存文件删除失败',
                                                            'data'=>''
                                                        ];
                                                    }else{
                                                        $result = [
                                                            'code'=>200,
                                                            'msg'=>'在线升级已完成',
                                                            'data'=>''
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                    }

                                }
                            }

                        }else{
                            $result = [
                                'code'=>406,
                                'msg'=>'本地已经是最新版',
                                'data'=>''
                            ];
                        }

                    }
                }

            }

        }
        return json($result);
    }
}