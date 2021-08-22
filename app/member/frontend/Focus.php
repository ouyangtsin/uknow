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
namespace app\member\frontend;

use app\common\controller\Frontend;
use app\common\logic\common\FocusLogic;
use app\common\model\Common;
use app\common\model\Users;
use think\App;

/**
 * 我的关注
 * Class Focus
 * @package app\ask\controller
 */
class Focus extends Frontend
{
	public function index()
	{
        $uid = $this->request->param('uid',0);
        if(!$user = Users::getUserInfo($uid))
        {
            $this->error('用户不存在');
        }
        $this->assign('user',$user);
		return $this->fetch();
	}

	public function focus_list()
    {
        $type = $this->request->post('type','question');
        $uid = $this->request->post('uid',0);
        $page = $this->request->param('page',1);
        $data = Common::getUserFocus($uid,$type,$page);
        if(!empty($data['data']))
        {
            foreach ($data['data'] as $key =>$val)
            {
                if($type=='fans' || $type=='friend')
                {
                    $data['data'][$key]['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$val['uid']);
                }
            }
        }
        $this->assign(['list'=>$data['data'],'type'=>$type]);
        $this->result(['last_page'=>$data['last_page'],'total'=>$data['total'],'html'=>$this->fetch()]);
    }
}