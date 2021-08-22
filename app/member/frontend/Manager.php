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
use app\common\library\helper\LogHelper;
use app\common\logic\common\FocusLogic;
use app\common\model\Users;
use think\App;

/**
 * 用户管理中心
 * Class Manager
 * @package app\member\frontend
 */
class Manager extends Frontend
{
    public function index()
    {
        if(!$this->user_id)
        {
            $this->loading('/');
        }

        $user =Users::getUserInfo($this->user_id);
        $this->assign('user',$user);
        $user['draft_count'] = db('draft')->where(['uid'=> (int)$this->user_id])->count();
        $user['favorite_count'] = db('favorite')->where(['uid'=> (int)$this->user_id])->count();
        $user['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user['uid']);

        $type = $this->request->param('type','dynamic');
        $this->assign('type',$type);

        $this->assign('question_count',LogHelper::getActionLogCount('publish_question',$user['uid'],$this->user_id));
        $this->assign('answer_count',LogHelper::getActionLogCount('publish_answer',$user['uid'],$this->user_id));
        $this->assign('article_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('favorite_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('user',$user);

         $action = [];
         switch ($type)
         {
             case 'dynamic':
                 $action=[
                     'publish_question',
                     'publish_article',
                     'publish_answer',
                     'agree_question',
                     'agree_article',
                     'agree_answer',
                     'focus_question',
                     'modify_answer',
                     'modify_question',
                     'modify_article'
                 ];
                 break;

             case 'question':
                 $action=[
                     'publish_question',
                 ];
                 break;

             case 'article':
                 $action=[
                     'publish_article',
                 ];
                 break;

             case 'answer':
                 $action=[
                     'publish_answer',
                 ];
                 break;
         }

         $data = LogHelper::getActionLogList($action,$user['uid'],$this->user_id,request()->param('page'),10,'uk-index-main');
         $this->assign($data);

        $this->TDK($user['user_name'].'的主页');
        return $this->fetch();
        //$this->view->engine()->layout('center');
    }
}