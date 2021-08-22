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

namespace app\member\widget;
use app\ask\model\Column as ColumnModel;
use app\common\controller\Widget;
use app\common\library\helper\LogHelper;
use app\common\logic\common\FocusLogic;
use app\common\model\PostRelation;
use app\common\model\Users;

/**
 * 通用小部件
 * Class Common
 * @package app\ask\widget
 */
class Member extends Widget
{
    /**
     * 用户内容列表
     * @param $uid
     * @param $type
     * @return mixed
     */
	public function get_user_post($uid,$type)
	{
        $action = 'publish_'.$type;
	    if($type=='dynamic')
        {
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
        }
	    if(in_array($type,['column','focus','favorite']))
        {
            switch ($type)
            {
                case 'column':
                    $data = ColumnModel::getMyColumnList($this->user_id,'new',1,request()->param('page'),10);
                    break;

                case 'focus':
                    break;
            }
        }else{
            $data = LogHelper::getActionLogList($action,$uid,$this->user_id,request()->param('page'),10,'uk-index-main');
        }
		$this->assign($data);
        $this->assign('type',$type);
		return $this->fetch('member/focus');
	}

    /**
     * 解析内容列表
     * @param $list
     * @param $page
     * @return mixed
     */
	public function parse($list,$page)
	{
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch('member/lists');
	}

    /*用户中心侧边栏导航*/
    public function user_nav($uid)
    {
        $user = Users::getUserInfo($uid);
        $user['draft_count'] = db('draft')->where(['uid'=>intval($uid)])->count();
        $user['favorite_count'] = db('favorite')->where(['uid'=>intval($uid)])->count();
        $user['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user['uid']);
        $this->assign('publish_question_count',LogHelper::getActionLogCount('publish_question',$user['uid'],$this->user_id));
        $this->assign('publish_answer_count',LogHelper::getActionLogCount('publish_answer',$user['uid'],$this->user_id));
        $this->assign('publish_article_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('user',$user);
        return $this->fetch('member/user_nav');
    }
}
