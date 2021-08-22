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
namespace app\admin\model;
use app\common\model\BaseModel;

class WechatFans extends BaseModel
{
    /**
     * 获取粉丝标签组
     * @param $wechat_account_id
     * @return array
     */
    public static function getTagGroup($wechat_account_id): array
    {
        $list = db('wechat_tag')->where('wechat_account_id',$wechat_account_id)->select()->toArray();
        $newList = [];
        if($list)
        {
            foreach ($list as $key=>$val)
            {
                $newList[$val['tag_id']] = $val['name'];
            }
        }
        $newList[0]='默认组';
        return $newList;
    }
}