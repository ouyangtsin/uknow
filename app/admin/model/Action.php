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
use think\Model;

class Action extends Model
{
	static $fieldList = array(
		array('name'=>'id','title'=>'ID','type'=>'hidden'),
		array('name'=>'name','title'=>'行为标识','type'=>'text','tips'=>'输入行为标识 英文字母'),
		array('name'=>'title','title'=>'行为名称','type'=>'text','tips'=>'输入行为名称'),
		array('name'=>'remark','title'=>'行为描述','type'=>'textarea','tips'=>'输入行为描述'),
		array('name'=>'action_rule','title'=>'行为规则','type'=>'textarea','tips'=>'输入行为规则，不写则只记录日志'),
		array('name'=>'log_rule','title'=>'日志规则','type'=>'textarea','tips'=>'记录日志备注时按此规则来生成，支持[变量|函数]。目前变量有：user,time,model,record,data'),
	);
}