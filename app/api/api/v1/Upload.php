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
namespace app\api\api\v1;
use app\common\controller\Api;
use app\common\library\helper\DataHelper;
use app\common\library\helper\ImageHelper;
use app\common\model\Attach as AttachModel;
use think\App;
use think\exception\ValidateException;
use think\facade\Filesystem;
use think\facade\Request;

class Upload extends Api
{
    // 上传方式 [chunk 大文件分片上传, tp 沿用TP上传]
    private $uploadType;
    private $file_path;

    // 上传验证规则
    protected $uploadValidate = [];

    // 构造方法
    public function __construct(App $app)
    {
        parent::__construct($app);
        // 默认上传方式
        $this->uploadType = "tp";
    }

    // 上传文件
    public function index()
    {
        $uploadPath=$this->file_path = $this->request->param('path','common');

        $uploadPath='uploads'.DIRECTORY_SEPARATOR.$uploadPath;

        $this->uploadValidate = $this->uploadVal();
        $files = $this->request->file('file') ?: $this->request->file('uk-upload-file');
        if ($this->uploadType == 'tp') {
            if(is_array($files))
            {
                return json($this->multipleUpload($files,$uploadPath));
            }
            return json($this->uploadFile($files,$uploadPath));
        } else {
            return json($this->bigUpload($uploadPath));
        }
    }

    // 上传验证规则
    private function uploadVal(): array
    {
        $file = [];
        if (Request::param('upload_type') == 'file') {
            // 文件限制
            if (get_setting('upload_file_ext')) {
                $file['fileExt'] = $this->removeExt(get_setting('upload_file_ext'));
            } else {
                $file['fileExt'] = 'rar,zip,avi,rmvb,3gp,flv,mp3,mp4,txt,doc,xls,ppt,pdf,xls,docx,xlsx,doc';
            }
            // 限制文件大小(单位b)
            if (get_setting('upload_file_size')) {
                $file['fileSize'] = get_setting('upload_file_size') * 1024;
            }
        } else {
            // 图片限制
            if (get_setting('upload_image_ext')) {
                $file['fileExt'] = $this->removeExt(get_setting('upload_image_ext'));
            } else {
                $file['fileExt'] = 'jpg,png,gif,jpeg';
            }
            // 限制图片大小(单位b)
            if (get_setting('upload_image_size')) {
                $file['fileSize'] = get_setting('upload_image_size') * 1024;
            }
        }
        return $file;
    }

    // tp上传文件
    private function uploadFile($file,$uploadPath): array
    {
        $file_size = $file->getSize();
        $md5 = $file->md5();
        $sha1 = $file->sha1();
        $file_mime = $file->getMime();
        $attach =  db('attach')->where('md5',$md5)->find();
        $result = [];
        if(!$attach)
        {
            try {
                validate($this->uploadValidate)->check(['file' => $file]);
                $saveName = Filesystem::disk('public')->putFile($uploadPath, $file);
                // windows系统中路径反斜杠处理
                $saveName = str_replace('\\', DIRECTORY_SEPARATOR, $saveName);
            } catch (\Exception $e) {
                $saveName = '';
                $result['url'] = '';
                $result['msg'] = $e->getMessage();
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
            }
            $file_ext =  strtolower(substr($saveName, strrpos($saveName, '.') + 1));
            $file_name = basename($saveName);
            $file_name_info = explode('.',$file_name);
            $file_full_name = DIRECTORY_SEPARATOR.$saveName;
            $save_dir = public_path().dirname($saveName);
            $waterCutfilename = $file_name_info[0] . '_water_thumb.' . $file_ext; 
            $waterCutfilePath = str_replace(public_path(),'',$save_dir.DIRECTORY_SEPARATOR .$waterCutfilename);
            $width = $height = 0;
            if (in_array($file_mime, ['image/gif', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/webp']) || in_array($file_ext, ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'webp'])) {
                $imgInfo = getimagesize($file->getPathname());
                if (!isset($imgInfo[0], $imgInfo[1]) || !$imgInfo) {
                    $this->error(lang('Uploaded file is not a valid image'));
                }
                $width = $imgInfo[0] ?? $width;
                $height = $imgInfo[1] ?? $height;
                if(!in_array($this->file_path, ['avatar','common'])){
                    $site_name=get_setting('site_name');
                    $water_author_text_enable=get_setting('water_author_text_enable');
                    $water_text=get_setting('water_text');
                    if($water_author_text_enable){
                        $uid=get_user_id();
                        $user_name=get_username($uid);
                        $water_text=$site_name.'-'.$user_name;
                    }else{
                        $water_text=$water_text?$site_name.'-'.$water_text:$site_name;
                    }
                    $water_url=ImageHelper::text($file_full_name,$water_text);
                    $saveName=$water_url;
                }
                //$saveName=ImageHelper::cutImage($saveName,$waterCutfilePath);
            }
            $saveName = str_replace('\\', DIRECTORY_SEPARATOR, $saveName);

            $result['state'] = 'SUCCESS'; //兼容百度
            $result['code'] = 1;
            $result["url"] = get_setting('cdn_url',Request::domain()).'/'.$saveName;
            $result["data"] = [get_setting('cdn_url',Request::domain()).'/'.$saveName];
            $result["errno"] = 0;
            $result['error'] = 1;
            $result['msg'] = lang('上传成功');
            if ($saveName) {
                $data = [
                    'uid'=>$this->user_id,
                    'name'=>$file_name,
                    'path'=>get_setting('cdn_url',Request::domain()).'/'.$saveName,
                    'thumb'=>get_setting('cdn_url',Request::domain()).'/'.$saveName,
                    'url'=> get_setting('cdn_url',Request::domain()).'/'.$saveName,
                    'ext'=>$file_ext,
                    'size'=>$file_size/1024,
                    'width'=>$width,
                    'height'=>$height,
                    'md5'=>$md5,
                    'sha1'=>$sha1,
                    'mime'=>$file_mime,
                    'driver'=>'local',
                ];
                db('attach')->insert($data);
            }
            return $result;
        }

        $result['state'] = 'SUCCESS'; //兼容百度
        $result['code'] = 1;
        $result["url"] = $attach['path'];
        $result["data"] = [$attach['path']];
        $result["errno"] = 0;
        $result['error'] = 1;
        $result['msg'] = lang('上传成功');
        $result['id'] =$attach['id'];
        return $result;
    }

    //多文件上传
    public function multipleUpload($files,$uploadPath): array
    {
        $paths = $attachIds= array();
        $error = '';
        foreach($files as $file){
            try {
                validate($this->uploadValidate)->check(DataHelper::objToArray($file));
                $saveName = Filesystem::disk('public')->putFile($uploadPath, $file);
                $path = $saveName;
                $paths[] = DIRECTORY_SEPARATOR.$path;
            } catch (ValidateException $e) {
                $error = $e->getMessage();
            }
        }

        if($paths)
        {
            $result['code'] = 1;
            $result['state'] = 'SUCCESS'; //兼容百度
            $result['id'] = implode(',',$attachIds);
            $result["url"] = $paths;
            $result["errno"] = 0;
            $result['error'] = 0;
            $result['msg'] = '上传成功';
            $result["data"] = $paths;
            return $result;
        }
        $result['url'] = '';
        $result['msg'] = $error;
        $result['code'] = 0;
        $result["errno"] = 1;
        $result['error'] = 1;
        $result['state'] = 'ERROR'; //兼容百度
        return $result;
    }

    // 大文件切片上传
    private function bigUpload($uploadPath): array
    {
        // 验证
        $file = request()->file('file') ?? request()->file('uk-upload-file');
        $org_file = $_FILES["file"]??$_FILES["uk-upload-file"];
        try {
            validate($this->uploadValidate)->check(['file' => $file]);
            $file_size = $file->getSize();
            $md5 = $file->md5();
            $sha1 = $file->sha1();
            $file_mime = $file->getMime();
        } catch (\Exception $e) {
            $result['url'] = '';
            $result['msg'] = $e->getMessage();
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['state'] = 'ERROR'; //兼容百度
            return$result;
        }

        // Make sure file is not cached (as it happens for example on iOS devices)
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // 跨域支持
        // header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }

        if (!empty($_REQUEST['debug'])) {
            $random = rand(0, intval($_REQUEST['debug']));
            if ($random === 0) {
                header("HTTP/1.0 500 Internal Server Error");
                exit;
            }
        }

        // 页面执行时间不限制
        @set_time_limit(5 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);

        // Settings
        // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        // 设置临时上传目录
        $targetDir = public_path() . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR . 'temp';
        // 设置上传目录
        $uploadDir = public_path() . DIRECTORY_SEPARATOR . $uploadPath . DIRECTORY_SEPARATOR . date('Ymd');
        // 上传完后清空临时目录
        $cleanupTargetDir = true;
        // 临时文件期限
        $maxFileAge = 5 * 3600;
        // 创建临时目录
        if (!file_exists($targetDir) && !Directory($targetDir) && !is_dir($targetDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
        }

        // 创建上传目录
        if (!file_exists($uploadDir) && !Directory($uploadDir) && !is_dir($uploadDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadDir));
        }

        // 获取上传文件名称
        $fileName = $file->getOriginalName();
        $fileName = iconv('UTF-8', 'gb2312', $fileName);
        // 临时上传完整目录信息
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // 定义命名规则
        $pathInfo = pathinfo($fileName);
        // md5
        $fileName = md5(time() . $pathInfo['basename']) . '.' . $pathInfo['extension'];
        $waterfilename = md5(time() . $pathInfo['basename']) . '_water.' . $pathInfo['extension'];
        $waterCutfilename = md5(time() . $pathInfo['basename']) . '_water_thumb.' . $pathInfo['extension'];

        // 正式上传完整目录信息
        $uploadFullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $waterfilePath = $uploadDir . DIRECTORY_SEPARATOR .$waterfilename;
        $waterCutfilePath = $uploadDir . DIRECTORY_SEPARATOR .$waterCutfilename;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // 清空临时目录
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open temp directory';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                return$result;
            }

            while (($file = readdir($dir)) !== false) {
                $tmpFilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // 如果临时文件是当前文件，则转到下一个
                if ($tmpFilePath == "{$filePath}_{$chunk}.part" || $tmpFilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }

                // 如果临时文件早于最大使用期限并且不是当前文件，则将其删除
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpFilePath) < time() - $maxFileAge)) {
                    @unlink($tmpFilePath);
                }
            }
            closedir($dir);
        }

        // 打开临时文件
        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            $result['url'] = '';
            $result['msg'] = 'Failed to open output stream';
            $result['code'] = 0;
            $result["errno"] = 1;
            $result['error'] = 1;
            $result['state'] = 'ERROR'; //兼容百度
            return$result;
        }

        if (!empty($_FILES)) {
            if ($org_file["error"] || !is_uploaded_file($org_file["tmp_name"])) {
                $result['url'] = '';
                $result['msg'] = 'Failed to move uploaded file';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                return$result;
            }

            // 读取二进制输入流并将其附加到临时文件
            if (!$in = @fopen($org_file["tmp_name"], "rb")) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open input stream';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                return$result;
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open input stream';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                return$result;
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $index = 0;
        $done = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (!file_exists("{$filePath}_{$index}.part")) {
                $done = false;
                break;
            }
        }
        if ($done) {
            if (!$out = @fopen($uploadFullPath, "wb")) {
                $result['url'] = '';
                $result['msg'] = 'Failed to open output stream';
                $result['code'] = 0;
                $result["errno"] = 1;
                $result['error'] = 1;
                $result['state'] = 'ERROR'; //兼容百度
                return$result;
            }

            if (flock($out, LOCK_EX)) {
                for ($index = 0; $index < $chunks; $index++) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }

                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }

                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }

                flock($out, LOCK_UN);
            }
            @fclose($out);

            // 输出
            // 移除public目录
            $uploadFullPath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $uploadFullPath);
            $waterCutfilePath = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $waterCutfilePath);
            // windows系统中路径反斜杠处理
            $uploadFullPath = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $uploadFullPath);
            $waterCutfilePath = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $waterCutfilePath);
            $width=$height=0;
            $_data = [
                'uid'=>$this->user_id,
                'name'=>$fileName,
                'path'=>$uploadFullPath,
                'thumb'=>root_path().$uploadFullPath,
                'url'=> get_setting('cdn_url',Request::domain()).DIRECTORY_SEPARATOR.$uploadFullPath,
                'ext'=>$pathInfo['extension'],
                'size'=>$file_size/1024,
                'width'=>$width,
                'height'=>$height,
                'md5'=>$md5,
                'sha1'=>$sha1,
                'mime'=>$file_mime,
                'driver'=>'local',
            ];
            if (in_array($file_mime, ['image/gif', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/webp']) || in_array($pathInfo['extension'], ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'webp'])) { 
                //图片裁剪
                $imgInfo = getimagesize(public_path().$uploadFullPath);
                if (!isset($imgInfo[0], $imgInfo[1]) || !$imgInfo) {
                    $this->error(lang('Uploaded file is not a valid image'));
                }
                $_data['width'] = $imgInfo[0] ?? $width;
                $_data['height'] = $imgInfo[1] ?? $height;
                if(!in_array($this->file_path, ['avatar','common'])){
                    $site_name=get_setting('site_name');
                    $water_author_text_enable=get_setting('water_author_text_enable');
                    $water_text=get_setting('water_text');
                    if($water_author_text_enable){
                        $uid=get_user_id();
                        $user_name=get_username($uid);
                        $water_text=$site_name.'-'.$user_name;
                    }else{
                        $water_text=$water_text?$site_name.'-'.$water_text:$site_name;
                    }
                    $water_url=ImageHelper::text($uploadFullPath,$water_text);
                    $uploadFullPath=$water_url;
                }
                $uploadFullPath=ImageHelper::cutImage($uploadFullPath,$waterCutfilePath);
                $_data['thumb'] = $uploadFullPath;
            }
            AttachModel::create($_data);
            $result['state'] = 'SUCCESS'; //兼容百度
            $result['code'] = 1;
            $result["url"] = $uploadFullPath;
            $result["data"] = [$uploadFullPath];
            $result["errno"] = 0;
            $result['error'] = 0;
            $result['msg'] = lang('上传成功');
            return $result;
        }
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    // 移除上传危险后缀
    private function removeExt(string $ext = ''): string
    {
        $ext = strtolower($ext);
        if (strpos($ext, 'php') !== false) {
            $ext = str_ireplace("php", "", $ext);
            return $this->removeExt($ext);
        }
        if (strpos($ext, 'asp') !== false) {
            $ext = str_ireplace("asp", "", $ext);
            return $this->removeExt($ext);
        }
        return $ext;
    }
}
