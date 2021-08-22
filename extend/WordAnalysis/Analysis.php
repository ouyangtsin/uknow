<?php
namespace WordAnalysis;

/**
 * 中文分词提取关键字
 */
class Analysis
{
    /**
     * 关键字提取
     * @param string $content
     * @param int $num 获取数量
     * @return string
     */
    public static function getKeywords($content = "",$num = 3)
    {
        if (empty ( $content )) {
            return '';
        }
        require_once 'phpanalysis.class.php';
        \PhpAnalysis::$loadInit = false;
        $pa = new \PhpAnalysis ( 'utf-8', 'utf-8', false );
        $pa->LoadDict ();
        $pa->SetSource ($content);
        $pa->StartAnalysis ( true );
        $tags = $pa->GetFinallyKeywords ($num); // 获取文章中的n个关键字
        return $tags;//返回关键字
    }
}