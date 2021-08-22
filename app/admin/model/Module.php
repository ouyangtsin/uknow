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

class Module extends BaseModel
{
    /**
     * 检查模块是否存在
     * @param $name
     * @return mixed
     */
    public static function checkModuleExist($name)
    {
        return db('module')->where(['name'=>$name,'status'=>1])->value('name');
    }
}