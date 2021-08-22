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
namespace app\api\api\v1;

use app\common\controller\Api;
use app\common\model\Users;

class Account extends Api
{
    /**
     * 登录接口
     */
    public function login()
    {
        if($this->request->isAjax())
        {
            $user_name = $this->request->post('user_name');
            $password = $this->request->post('password');
            if(!$user_name)
            {
                $this->result([],-1,'请输入用户名');
            }

            if(!$password)
            {
                $this->result([],-1,'请输入用户密码');
            }

            if(!$user = Users::getLogin($user_name,$password))
            {
                $this->result([],-1,Users::getError());
            }

            $this->result(['user_info'=>$user],1,'登录成功');
        }
    }
}