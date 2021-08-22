<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use think\template\driver\File;

class FileHelper
{
	private $_values = array();
	public $error = "";
	// PHP禁用函数
	public static $disableFunc = 'phpinfo\(|eval\(|passthru\(|exec\(|system\(|chroot\(|scandir\(|chgrp\(|chown\(|shell_exec\(|proc_open\(|proc_get_status\(|ini_alter\(|ini_alter\(|ini_restore\(|dl\(|pfsockopen\(|openlog\(|syslog\(|readlink\(|symlink\(|popepassthru\(|stream_socket_server\(|fsocket\(|fsockopen|popen\(|assert\(';

	/**
	 * 检测目录并循环创建目录
	 * @param $dir
	 * @return bool
	 */
    public static function mkDirs($dir): bool
    {
        if (!file_exists($dir)) {
            self::mkdirs(dirname($dir));
            mkdir($dir, 0777);
        }
        return true;
    }

	/**
	 * 复制文件到指定文件
	 * @param $source
	 * @param $dest
	 * @return bool
	 */
    public static function copyDir($source, $dest)
    {
        if (!is_dir($dest)) {
            self::mkdirs($dest);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                $sonDir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                if (!is_dir($sonDir)) {
                    self::mkdirs($sonDir);
                }
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
        return true;
    }

    /*写入
    * @param  string  $type 1 为生成控制器 2 模型
    */
    public static function filePutContents($content,$filepath,$type){
        if($type==1){
            $str = file_get_contents($filepath);
            $parten = '/\s\/\*+start\*+\/(.*)\/\*+end\*+\//iUs';
            preg_match_all($parten,$str,$all);
            $ext_content = '';
            if($all[0]){
                foreach($all[0] as $key=>$val){
                    $ext_content .= $val."\n\n";
                }
            }
            $content .= $ext_content."\n\n";
            $content .="}\n\n";
        }
        ob_start();
        echo $content;
        $_cache=ob_get_contents();
        ob_end_clean();
        if($_cache){
            $File = new File();
            $File->write($filepath, $_cache);
        }
    }

    /**
     * 获取文件夹大小
     *
     * @param string $dir 根文件夹路径
     * @return int
     */
    public static function getDirSize(string $dir)
    {
        if(!is_dir($dir)){
            return false;
        }
        $handle = opendir($dir);
        $sizeResult = 0;
        while (false !== ($FolderOrFile = readdir($handle))) {
            if ($FolderOrFile != "." && $FolderOrFile != "..") {
                if (is_dir("$dir/$FolderOrFile")) {
                    $sizeResult += self::getDirSize("$dir/$FolderOrFile");
                } else {
                    $sizeResult += filesize("$dir/$FolderOrFile");
                }
            }
        }

        closedir($handle);
        return $sizeResult;
    }

	/**
	 * 创建文件
	 * @param $file
	 * @param $content
	 * @return bool
	 */
    public static function createFile($file,$content): bool
    {
        $myFile = fopen($file, "w") or die("Unable to open file!");
        fwrite($myFile, $content);
        fclose($myFile);
        return true;
    }

    /**
     * 基于数组创建目录
     * @param $files
     */
    public static function createDirOrFiles($files)
    {
        foreach ($files as $key => $value) {
            if (substr($value, -1) == '/') {
                mkdir($value);
            } else {
                file_put_contents($value, '');
            }
        }
    }

    // 判断文件或目录是否有写的权限
    public static function isWritable($file): bool
    {
        if (DIRECTORY_SEPARATOR == '/' AND @ ini_get("safe_mode") == FALSE) {
            return is_writable($file);
        }
        if (!is_file($file) OR ($fp = @fopen($file, "r+")) === FALSE) {
            return FALSE;
        }
        fclose($fp);
        return TRUE;
    }

    /**
     * 写入日志
     * @param $path
     * @param $content
     * @return bool|int
     */
    public static function writeLog($path, $content)
    {
        self::mkdirs(dirname($path));
        return file_put_contents($path, "\r\n" . $content, FILE_APPEND);
    }

    /**
     * 获取文件列表
     * @param $path
     * @param string $type
     * @return array
     */
    public static function getFileList($path,$type='')
    {
        $list = [];
        $temp_list = scandir($path);
        foreach ($temp_list as $file) {
            //排除根目录
            if ($file != ".." && $file != ".") {
                if (is_dir($path . "/" . $file)) {
                    //子文件夹，进行递归
                    $list[] = self::getFileList($path . "/" . $file,$type);
                } else {
                    if($type==''){
                        //根目录下的文件
                        $list[] = $file;
                    }else{
                        $fileType = mime_content_type($path.'/'.$file);
                        if(strpos($fileType,$type)!==false){
                            $list[] = $path.'/'.$file;
                        }
                    }
                }
            }
        }
        return $list;
    }

	/**
	 * 取得目录中的结构信息
	 * @param $directory
	 * @return array
	 */
	public static function getList($directory): array
    {
		$scanDir = scandir($directory);
		$dir = [];
		foreach ($scanDir as $k => $v) {
			if ($v == '.' || $v == '..') {
				continue;
			}
			$dir[] = $v;
		}
		return $dir;
	}

	/**
	 * 生成目录
	 * @param string $path 目录
	 * @param  integer $mode 权限
	 * @return boolean
	 */
	public static function create(string $path, $mode = 0755)
	{
		if (is_dir($path)) {
			return true;
		}
		$path = str_replace("\\", "/", $path);
		if (substr($path, -1) != '/') {
			$path = $path.'/';
		}
		$temp = explode('/', $path);
		$cur_dir = '';
		$max = count($temp) - 1;
		for ($i=0; $i<$max; $i++) {
			$cur_dir .= $temp[$i].'/';
			if (@is_dir($cur_dir)) {
				continue;
			}
			@mkdir($cur_dir, $mode, true);
			@chmod($cur_dir, $mode);
		}
		return is_dir($path);
	}

	/**
	 * 取得目录下面的文件信息
	 * @param $pathname
	 * @param string $pattern
	 * @return array
	 */
	public function listFile($pathname, $pattern = '*')
	{
		$dir = array();
		$list = glob($pathname . $pattern);
		foreach ($list as $i => $file) {
			$dir[$i] = pathinfo($file);
			$dir[$i]['pathname'] = realpath($file);
			$dir[$i]['isDir'] = is_dir($file);
			$dir[$i]['atime'] = fileatime($file);
			$dir[$i]['ctime'] = filectime($file);
			$dir[$i]['mtime'] = filemtime($file);
			$dir[$i]['size'] = filesize($file);
			$dir[$i]['type'] = filetype($file);
			$dir[$i]['isReadable'] = is_readable($file);
			$dir[$i]['isWritable'] = is_writable($file);
			$dir[$i]['isFile'] = is_file($file);
			$dir[$i]['isLink'] = is_link($file);
			$dir[$i]['owner'] = fileowner($file);
			$dir[$i]['perms'] = fileperms($file);
			$dir[$i]['inode'] = fileinode($file);
			$dir[$i]['group'] = filegroup($file);
		}

		// 对结果排序 保证目录在前面
		usort($dir, function ($a, $b) {
			if ($a['isDir']  ==  $b['isDir']) {
				return  0;
			}
			return  $a['isDir'] > $b['isDir'] ? -1 : 1;
		});

		$this->_values = $dir;

		return $this->_values;
	}

	/**
	 * 返回数组中的当前元素（单元）
	 * @param $arr
	 * @return bool|mixed
	 */
	public static function current($arr)
	{
		if (!is_array($arr)) {
			return false;
		}
		return current($arr);
	}

	/**
	 * 文件上次访问时间
	 * @return integer
	 */
	public function getATime()
	{
		$current = $this->current($this->_values);
		return $current['atime'];
	}

	/**
	 * 取得文件的 inode 修改时间
	 * @return mixed
	 */
	public function getCTime()
	{
		$current = $this->current($this->_values);
		return $current['ctime'];
	}

	/**
	 * 遍历子目录文件信息
	 * @return bool | mixed
	 */
	public function getChildren()
	{
		$current = $this->current($this->_values);
		if ($current['isDir']) {
			return new Dir($current['pathname']);
		}
		return false;
	}

	/**
	 * 取得文件名
	 * @return string
	 */
	public function getFilename()
	{
		$current = $this->current($this->_values);
		return $current['filename'];
	}

	/**
	 * 取得文件的组
	 * @return integer
	 */
	public function getGroup()
	{
		$current = $this->current($this->_values);
		return $current['group'];
	}

	/**
	 * 取得文件的 inode
	 * @return mixed
	 */
	public function getInode()
	{
		$current = $this->current($this->_values);
		return $current['inode'];
	}

	/**
	 * 取得文件的上次修改时间
	 * @return integer
	 */
	public function getMTime()
	{
		$current = $this->current($this->_values);
		return $current['mtime'];
	}

	/**
	 * 取得文件的所有者
	 * @return string
	 */
	public function getOwner()
	{
		$current = $this->current($this->_values);
		return $current['owner'];
	}

	/**
	 * 取得文件路径，不包括文件名
	 * @return string
	 */
	public function getPath()
	{
		$current = $this->current($this->_values);
		return $current['path'];
	}

	/**
	 * 取得文件的完整路径，包括文件名
	 * @return string
	 */
	public function getPathname()
	{
		$current = $this->current($this->_values);
		return $current['pathname'];
	}

	/**
	 * 取得文件的权限
	 * @return integer
	 */
	public function getPerms()
	{
		$current = $this->current($this->_values);
		return $current['perms'];
	}

	/**
	 * 取得文件的大小
	 * @return integer
	 */
	public function getSize()
	{
		$current = $this->current($this->_values);
		return $current['size'];
	}

	/**
	 * 取得文件类型
	 * @return string
	 */
	public function getType()
	{
		$current = $this->current($this->_values);
		return $current['type'];
	}

	/**
	 * 是否为目录
	 * @return mixed
	 */
	public function isDir()
	{
		$current = $this->current($this->_values);
		return $current ? $current['isDir'] : false;
	}

	/**
	 * 是否为文件
	 * @return mixed
	 */
	public function isFile()
	{
		$current = $this->current($this->_values);
		return $current ? $current['isFile'] :false;
	}

	/**
	 * 文件是否为一个符号连接
	 * @return mixed
	 */
	public function isLink()
	{
		$current = $this->current($this->_values);
		return $current ? $current['isLink']:false;
	}

	/**
	 * 文件是否可以执行
	 * @return mixed
	 */
	public function isExecutable()
	{
		$current = $this->current($this->_values);
		return $current ? $current['isExecutable']:false;
	}

	/**
	 * 文件是否可读
	 * @return mixed
	 */
	public function isReadable()
	{
		$current = $this->current($this->_values);
		return $current ? $current['isReadable']:false;
	}

	// 返回目录的数组信息
	public function toArray()
	{
		return $this->_values;
	}

	/**
	 * 判断目录是否为空
	 * @param $directory
	 * @return bool
	 */
	public function isEmpty($directory)
	{
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false) {
			if ($file != "." && $file != "..") {
				closedir($handle);
				return false;
			}
		}
		closedir($handle);
		return true;
	}


	/**
	 * 删除目录（包括下面的文件）
	 * @param $directory
	 * @param bool $subdir
	 * @return bool
	 */
	public static function delDir($directory, $subdir = true)
	{
		if (is_dir($directory) == false) {
			return false;
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false) {
			if ($file != "." && $file != "..") {
				is_dir("$directory/$file") ? self::delDir("$directory/$file") : @unlink("$directory/$file");
			}
		}

		if (readdir($handle) == false) {
			closedir($handle);
			@rmdir($directory);
		}
	}

	/**
	 * 删除目录下面的所有文件，但不删除目录
	 * @param $directory
	 * @return bool
	 */
	public static function del($directory)
	{
		if (is_dir($directory) == false) {
			return false;
		}
		$handle = opendir($directory);
		while (($file = readdir($handle)) !== false) {
			if ($file != "." && $file != ".." && is_file("$directory/$file")) {
				@unlink("$directory/$file");
			}
		}
		closedir($handle);
	}

	/**
	 * 获取指定文件夹下的指定后缀文件（含子目录）
	 * @param string $path 文件夹路径
	 * @param array $suffix 指定后缀名
	 * @param array $files 返回的结果集
	 * @return array
	 */
	public static function getFiles($path, $suffix = ['php', 'html'], &$files = [])
	{
		$response = opendir($path);
		while($file = readdir($response)) {
			if ($file != '..' && $file != '.') {
				if (is_dir($path.'/'.$file)) {
					self::getFiles($path.'/'.$file, $suffix, $files);
				} else {
					$pathInfo = pathinfo($file);
					if (in_array(strtolower($pathInfo ['extension']), $suffix)) {
						$files[] = $path.'/'.$file;
					}
				}
			}
		}
		closedir($response);
		return $files;
	}

	/**
	 * PHP文件危险函数检查
	 * @param string $path
	 * @param array $suffix 指定后缀名
	 * @return array
	 */
	public static function safeCheck($path, $suffix = ['php', 'html'])
	{
		$files = self::getFiles($path, $suffix);
		$result = [];
		foreach($files as $f) {
			$pattern = "/".self::$disableFunc."/i";
			$content = file_get_contents($f);
			if (preg_match_all($pattern, $content, $matches) && $matches[0]) {
                $result[] = ['file' => $f, 'function' => $matches[0]];
            }
		}
		return $result;
	}
}