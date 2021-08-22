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

namespace app\admin\backend;

use app\common\controller\Backend;
use app\common\library\helper\MailHelper;

/**
 * 系统工具箱
 * Class Tools
 * @package app\admin\controller
 */
class Tools extends Backend
{
    public function index()
    {
        if($this->request->isPost())
        {
            $action = $this->request->post('action');
            switch ($action)
            {
                //邮件测试
                case 'email_test':
                    $email_address = $this->request->post('email_address');
                    if(!$email_address || !MailHelper::isEmail($email_address))
                    {
                        $this->error('请输入正确的邮箱地址');
                    }

                    $result = MailHelper::sendEmail($email_address,'TYPE_EMAIL_TEST',[
                        'subject'=>get_setting('site_name').'邮件测试',
                        'message' =>'这是一封测试邮件'
                    ]);

                    if($result['code'])
                    {
                        $this->success('邮件发送成功');
                    }
                    $this->error($result['message']);
                    break;

            }
        }
        return $this->fetch();
    }
}
