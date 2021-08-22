<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UKCMS
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowCMS一款基于TP6开发的内容管理系统
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;

use Grafika\Color;
use Grafika\Grafika;

/**
 * 图像处理
 * Class ImageHelper
 * @package app\common\library\helper
 */
class ImageHelper
{
    /**
     * 文字水印
     */
    public static function text($source_img,$watermark_text)
    {
        $tmp_img = str_replace(public_path(),'',$source_img);
        if(strstr($tmp_img, '_water'))
        {
            return $tmp_img;
        }
        $font = public_path().'static'.DS.'common'.DS.'font'.DS.'msyh.ttf';
        $text_color = '#fff';
        $source_img_arr = explode('.',$source_img);
        $ext = end($source_img_arr);
        array_pop($source_img_arr);
        $new_img = public_path().implode('.',$source_img_arr) .'_water.'.$ext;
        try {
            $editor = Grafika::createEditor();
            $editor->open($image, public_path().$source_img);
            $width = $image->getWidth();
            $height = $image->getHeight();
            $font_size =$width*0.05;
            $leng=mb_strlen($watermark_text)*$font_size;
            $editor->text($image, $watermark_text, $font_size, $width-$leng, $height-$font_size*2,new Color($text_color),$font);
            $editor->save($image, $new_img);
            return file_exists($new_img) ? str_replace(public_path(),'',$new_img) : $tmp_img;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 提取内容中的图片并本地化处理
     * @param $content
     * @param $type
     * @param $uid
     * @return array|mixed|string|string[]
     */
    public static function fetchContentImagesToLocal($content,$type,$uid)
    {
        $matches = array();
        preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i',htmlspecialchars_decode($content), $matches);
        if(!is_array($matches) || !get_setting('download_image_to_local')) return $content;

        foreach ($matches[1] as  $v)
        {
            if((strpos($v,'http://')!==false || strpos($v,'https://')!==false) && strpos($v,request()->domain())===false)
            {
                $file = self::downloadImageToLocal(trim($v,"\"'"),$type,$uid);
                if($file) {
                    $content = str_replace($v, $file, $content);
                }
            }
        }
        return $content;
    }

    /**
     * 远程下载图片到本地
     * @param $url
     * @param string|null $save_dir 目录名称
     * @param int $uid
     * @return string
     */
    public static function downloadImageToLocal($url, $save_dir='common',$uid=0)
    {
        if($url !== "" && get_setting('water_enable'))
        {
            $url=str_replace(['&amp;'],['&'],$url); //url中特定字符替换
            $ext = strrchr($url, '.');
            $mimes = array('.gif', '.jpg', '.png','.ico');
            if (!in_array($ext, $mimes)) {
                $ext = '.jpg';
            }
            $save_path = public_path().'uploads'.DS.$save_dir .DS.date('Ymd',time()).DS;
            if (!file_exists($save_path)) {
                FileHelper::mkDirs($save_path);
            }
            $filename_r = md5(date('YmdHis',time())).$ext;	//给图片命名
            $filename = $save_path.$filename_r;
            $file_url = '/uploads/'.$save_dir.'/'.date('Ymd',time()).'/'.$filename_r;
            $imgByte = HttpHelper::get($url);
            FileHelper::createFile($filename,$imgByte);
            $file_url = is_file($filename) ? $file_url : '';
            if(get_setting('water_author_text_enable') && $user_name = get_username($uid))
            {
                $file_url = self::text($filename,get_setting('site_name').'@'.$user_name);
            }
            return $file_url;
        }else{
            return false;
        }
    }

    /**
     * 图片比例压缩
     * @param $url
     * @param $thumb_url
     * @return string
     */
    public static function cutImage($url,$thumb_url)
    {
        try {
            $editor = Grafika::createEditor();
            $new_img=public_path().$thumb_url;
            $editor->open($image1 , public_path().$url); 
            $width=$image1->getWidth();
            $cut_image_prop=get_setting('cut_image_prop');
            $editor->resizeExactWidth($image1 , $width*$cut_image_prop);
            $editor->save($image1 ,$new_img);
            return $thumb_url;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}