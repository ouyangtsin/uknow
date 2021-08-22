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

namespace app\common\library\helper;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\AddShortUrlResponseBody\data;

/**
 * 后台管理员通知处理
 * Class AdminNotifyHelper
 * @package app\common\library\helper
 */
class AdminNotifyHelper
{

    /**
     * 获取通知数量
     * @return int
     */
    public static function getNotifyCount()
    {
        $notifications = self::notifyCount();
        $count = 0;
        if (!empty($notifications)) {
            foreach ($notifications as $key => $val) {
                $count += intval($val);
            }
        }
        return $count;
    }

    public static function notifyCount(){
        return [
            'question_approval_count' => db('approval')->where(['type' => 'question', 'status' => 0])->count(),
            'article_approval_count' => db('approval')->where(['type' => 'article', 'status' => 0])->count(),
            'answer_approval_count' => db('approval')->where(['type' => 'answer', 'status' => 0])->count(),
            'modify_question_approval_count' => db('approval')->where(['type' => 'modify_question', 'status' => 0])->count(),
            'modify_answer_approval_count' => db('approval')->where(['type' => 'modify_answer', 'status' => 0])->count(),
            'modify_article_approval_count' => db('approval')->where(['type' => 'modify_article', 'status' => 0])->count(),
            'article_comment_approval_count' => db('approval')->where(['type' => 'article_comment', 'status' => 0])->count(),
            'user_report_count' => db('report')->where(['status' => 0])->count(),
            'register_approval_count' => db('users')->where(['status' => 2])->count(),
            'verify_approval_count' => db('users_verify')->where(['status' => 0])->count(),
            'column_approval_count' => db('column')->where(['verify' => 0])->count(),
        ];
    }
    /**
     * 获取通知内容
     * @return array
     */
    public static function getNotifyTextList()
    {
        $notifications_texts = [];
        $notifications = self::notifyCount();
        /*问题*/
        if ($notifications['question_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'question']),
                'text' => lang('有 %s 个问题待审核', [$notifications['question_approval_count']])
            );
        }

        /*文章*/
        if ($notifications['article_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'article']),
                'text' => lang('有 %s 个文章待审核', [$notifications['article_approval_count']])
            );
        }

        /*回答*/
        if ($notifications['answer_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'answer']),
                'text' => lang('有 %s 个回答待审核', [$notifications['answer_approval_count']])
            );
        }

        /*问题修改*/
        if ($notifications['modify_question_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'modify_question']),
                'text' => lang('有 %s 个问题修改待审核', [$notifications['modify_question_approval_count']])
            );
        }

        /*文章修改*/
        if ($notifications['modify_article_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'modify_article']),
                'text' => lang('有 %s 个文章修改待审核', [$notifications['modify_article_approval_count']])
            );
        }

        /*回答修改*/
        if ($notifications['modify_answer_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'modify_answer']),
                'text' => lang('有 %s 个回答修改待审核', [$notifications['modify_answer_approval_count']])
            );
        }

        /*文章评论*/
        if ($notifications['article_comment_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Approval/index', ['status' => 0, 'type' => 'article_comment']),
                'text' => lang('有 %s 个文章评论待审核', [$notifications['article_comment_approval_count']])
            );
        }

        /*用户举报*/
        if ($notifications['user_report_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/member.Report/index', ['status' => 0]),
                'text' => lang('有 %s 个用户举报待查看', [$notifications['user_report_count']])
            );
        }

        /*用户注册*/
        if (get_setting('register_valid_type') == 'admin' and $notifications['register_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/member.Users/index', ['status' => 2]),
                'text' => lang('有 %s 个新用户待审核', [$notifications['register_approval']])
            );
        }

        /*认证申请*/
        if ($notifications['verify_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/member.Verify/index', ['status' => 0]),
                'text' => lang('有 %s 个认证申请待审核', [$notifications['verify_approval_count']])
            );
        }

        if ($notifications['column_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('admin/content.Column/index', ['verify' => 0]),
                'text' => lang('有 %s 个专栏申请待审核', [$notifications['verify_approval_count']])
            );
        }

        return $notifications_texts;
    }
}