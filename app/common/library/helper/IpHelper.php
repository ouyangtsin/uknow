<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;

class IpHelper
{
    public static function is_ip($str)
    {
        $ip = explode('.', $str);
        for ($i = 0; $i < count($ip); $i++) {
            if ($ip[$i] > 255) {
                return false;
            }
        }
        return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
    }

    public static function  ip(){
        $ip='未知IP';
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            return self::is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            return self::is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
        }else{
            return self::is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
        }
    }

	/**
	 * 获取真实ip地址
	 * @param bool $int
	 * @return array|false|float|int|mixed|string
	 */
	public static function getRealIp($int=false)
	{
		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$realIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$realIp = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$realIp = $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$realIp = getenv('HTTP_X_FORWARDED_FOR');
			} else if (getenv('HTTP_CLIENT_IP')) {
				$realIp = getenv('HTTP_CLIENT_IP');
			} else {
				$realIp = getenv('REMOTE_ADDR');
			}
		}
		return $int ? self::ipToInt('0'.$realIp) : $realIp;
	}

	/**
	 * IP转换成整形
	 * @param $ip
	 * @return float|int
	 */
	public static function ipToInt($ip){
		$ipArr = explode('.',$ip);
		$num = 0;
		for($i=0;$i<count($ipArr);$i++){
			$num += intval($ipArr[$i]) * pow(256,count($ipArr)-($i+1));
		}
		return $num;
	}

    /**
     * 获取IP所属归属地
     * @param $ip
     * @param string $type
     * @return mixed|string
     */
	public static function queryIpLocalInfo($ip,$type='text')
    {
        $url = 'http://ip-api.com/json/'.$ip.'?lang=zh-CN';
        $result = json_decode(file_get_contents($url),true);
        if($result['status']!='success')
        {
            return $ip;
        }
        return $type!='text' ? $result : $result['regionName'].$result['city'];
    }

    /**
     * 验证 IP 地址是否为内网 IP
     * @param string
     * @return bool
     */
    public static function validInternalIp($ip): bool
    {
        if (!self::is_ip($ip))
        {
            return false;
        }
        $ip_address = explode('.', $ip);
        if ($ip_address[0] == 10)
        {
            return true;
        }
        if ($ip_address[0] == 172 and $ip_address[1] > 15 and $ip_address[1] < 32)
        {
            return true;
        }
        if ($ip_address[0] == 192 and $ip_address[1] == 168)
        {
            return true;
        }
        return false;
    }

}