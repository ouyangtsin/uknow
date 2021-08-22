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

namespace app\ask\frontend;

use app\common\controller\Frontend;
use app\ask\model\Answer;
use app\ask\model\Question as QuestionModel;

/**
 * 评论控制器
 * Class Comment
 * @package app\ask\controller
 */
class Comment extends Frontend
{
    /**
     * 问题评论
     * @return mixed
     */
    public function question()
    {
        $question_id = $this->request->param('question_id',0);
        $order = $this->request->param('sort','new');
        $page = $this->request->param('page',0);
        $sort = ['create_time'=>'desc'];
        if($order=='hot')
        {
            $sort = ['create_time'=>'desc'];
        }
        $data = QuestionModel::getQuestionComments($question_id,$page,$sort);
        $this->assign(['question_id'=>$question_id,'sort'=>$sort]);
        $this->assign($data);
        return $this->fetch();
    }

    /**
     * 问题回答评论
     * @return mixed
     */
    public function answer()
    {
        $answer_id = $this->request->param('answer_id',0);
        $list = Answer::getAnswerComments($answer_id,$this->request->param('page'));
        $this->assign('answer_id',$answer_id);
        $this->assign($list);
        return $this->fetch();
    }
}