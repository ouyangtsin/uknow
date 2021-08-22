<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

class HtmlHelper
{
	/**
	 * 获取内容中图片列表
	 * @param string $content 内容原始html
	 * @return array|bool 返回图片数组
	 */
	public static function parseImg($content)
	{
		$pattern='/<img((?!src).)*src[\s]*=[\s]*[\'"](?<src>[^\'"]*)[\'"]/i';
		preg_match_all($pattern,htmlspecialchars_decode($content),$out);
		$return = array();
		if(!empty($out['src']))
		{
			foreach ($out['src'] as $k => $v)
			{
				$return[$k] = $v;
			}
		}
		return !empty($return) ? $return : false;
	}


    public static function parseVideo($content)
    {
        preg_match_all('/<video[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',$content,$matches);
        $video = [];
        if(!empty($matches))
        {
            $video = $matches[1];
        }
        return !empty($video) ? $video : false;
    }
}