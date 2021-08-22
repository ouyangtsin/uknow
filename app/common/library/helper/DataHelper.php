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

namespace app\common\library\helper;

class DataHelper
{
    /**
     * 输出xml字符
     * @param array $values
     * @return string|bool
     **/
    public static function arrToXml($values)
    {
        if (!is_array($values) || count($values) <= 0) {
            return false;
        }
        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @return array|false
     */
    public static function xmlToArray($xml)
    {
        if (!$xml) {
            return false;
        }
        // 检查xml是否合法
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $xml, true)) {
            xml_parser_free($xml_parser);
            return false;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }

    /**
     * 将array或者对象转为json
     * @param $array
     * @return false|string
     */
    public static function arrToJson($array)
    {
        if(is_array($array)){
            return json_encode($array);
        } elseif(is_object($array)){
            return json_encode($array, JSON_FORCE_OBJECT);
        }else{
            return '';
        }
    }

    /**
     * 将OBJ或者对象转为ARRAY
     * @return array|false
     */
    public static function objToArray($object)
    {
        $array = array();
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }

    /**
     * google api 二维码生成【QRcode可以存储最多4296个字母数字类型的任意文本，具体可以查看二维码数据格式】
     * @param string $text 二维码包含的信息，可以是数字、字符、二进制信息、汉字。不能混合数据类型，数据必须经过UTF-8 URL-encoded
     * @param string $widthHeight 生成二维码的尺寸设置
     * @param string $ecLevel 可选纠错级别，QR码支持四个等级纠错，用来恢复丢失的、读错的、模糊的、数据。
     *                            L-默认：可以识别已损失的7%的数据
     *                            M-可以识别已损失15%的数据
     *                            Q-可以识别已损失25%的数据
     *                            H-可以识别已损失30%的数据
     *
     * @param string $margin 生成的二维码离图片边框的距离
     *
     * @return string
     */
    public static function toQRimg($text='', $widthHeight = '150', $ecLevel = 'L', $margin = '0')
    {
        $chl = urlencode($text);
        return "http://chart.apis.google.com/chart?chs={$widthHeight}x{$widthHeight}&cht=qr&chld={$ecLevel}|{$margin}&chl={$chl}";
    }

    /***
     * 格式化面包导航(用户后台面包导航)
     * @param $data
     * @return array
     */
    public static function formatBreadCrumb($data): array
    {
        $result = array();
        if (!empty($data)) {
            $data = array_reverse($data);
            if (count($data) == 4) {
                //非常规 添加或修改
                $result['right'] = $data[1];
                $result['left'][0] = $data[1]['title'];
                //查看是添加还是修改
                $result['left'][1] = $data[2]['title'] . '-' . str_replace('操作-', '', $data[3]['title']);
            } else if (count($data) == 3) {
                //常规 添加或修改
                $result['right'] = $data[1];
                $result['left'][0] = $data[1]['title'];
                //查看是添加还是修改
                $result['left'][1] = str_replace('操作-', '', $data[2]['title']);
            } else if (count($data) == 2) {
                //常规 列表
                $result['right'] = $data[1];
                $result['left'][0] = $data[1]['title'];
                $result['left'][1] = '列表';
            } else {
                //单独定义
                $result['right'] = $data[0];
                $result['left'][0] = $data[0]['title'];
                $result['left'][1] = '';
            }
        }
        return $result;
    }
}