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

namespace app\common\model;

class Verify extends BaseModel
{
	protected $name = 'users_verify';

    //获取配置列表
    public static function getConfigList($map=[]): array
    {
        $keyList = db('verify_field')->where($map)->select()->toArray();
        foreach ($keyList as $k => $v) {
            if (in_array($v['type'], ['select','checkbox'])) {
                $keyList[$k]['value'] = explode(',', $v['value']);
            }elseif ($v['type'] === 'array')
            {
                $keyList[$k]['value'] = json_decode($v['option'],true);
            }
            $keyList[$k]['option'] = json_decode($v['option'], true);
            $keyList[$k]['tips'] = htmlspecialchars($v['tips']);
        }
        return $keyList;
    }
}