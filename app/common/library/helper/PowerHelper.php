<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UKCMS
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowCMS一款基于TP6开发的内容管理系统
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\library\helper;
use app\common\model\Users;
use app\ask\model\Topic;
use app\ask\model\Vote;

/**
 * 声望计算
 * Class PowerHelper
 * @package app\common\library\helper
 */
class PowerHelper
{
    //计算用户声望，$calcType 0用户全部内容的总声望,其他数字代表xx天内的用户声望，如30代表一个月内的声望
    public static function calcUserPowerByUid($uid,$calcType=0)
    {
        //用户在文章内的声望计算：$user_article_power = 不同声望组用户声望系数 * （赞同数-反对数） + 认证用户声望系数*（赞同数-反对数）
        if (!$user_info = Users::getUserInfo($uid))
        {
            return false;
        }

        $user_reputation = 0;

        $user_articles = db('article')->where('uid',$uid)->select()->toArray();
        $articles_ids = $articles_vote_agree_users = $articles_vote_against_users = $article_topics = $user_topics = [];

        $verify_user_power_factor = intval(get_setting('verify_user_power_factor'));
        $power_agree_factor = intval(get_setting('power_agree_factor'));
        $power_against_factor = intval(get_setting('power_against_factor'));
        $publish_user_power_factor = intval(get_setting('publish_user_power_factor'));
        $power_best_answer_factor = intval(get_setting('power_best_answer_factor'));
        $reputation_log_factor = intval(get_setting('power_log_factor'));
        //计算文章声望
        if ($user_articles)
        {
            foreach ($user_articles as $key => $val)
            {
                $articles_ids[] = $val['id'];
            }
            if ($articles_ids)
            {
                //文章点赞
                $articles_vote_agree_users = Vote::getVoteByItemIds('article',$articles_ids,1);
                //文章反对
                $articles_vote_against_users = Vote::getVoteByItemIds('article',$articles_ids,-1);
            }
            $s_agree_value = $s_against_value = $verify_user_agree_value = $verify_user_against_value =  0;
            foreach ($user_articles as $key => $val)
            {
                // 赞同的用户
                if ($articles_vote_agree_users && $articles_vote_agree_users[$val['id']])
                {
                    foreach($articles_vote_agree_users[$val['id']] AS $articles_vote_agree_user)
                    {
                        $voteUserInfo =  Users::getUserInfo($articles_vote_agree_user['uid']);
                        if($voteUserInfo['verified'])
                        {
                            $verify_user_agree_value += $verify_user_power_factor;
                        }else{
                            $s_agree_value += $voteUserInfo['power_factor'];
                        }
                    }
                }

                // 反对的用户
                if ($articles_vote_against_users && $articles_vote_against_users[$val['id']])
                {
                    foreach($articles_vote_against_users[$val['id']] AS $articles_vote_against_user)
                    {
                        $voteUserInfo =  Users::getUserInfo($articles_vote_agree_user['uid']);
                        if($voteUserInfo['verified'])
                        {
                            $verify_user_against_value += $verify_user_power_factor;
                        }else{
                            $s_against_value += $voteUserInfo['power_factor'];
                        }
                    }
                }

                //文章声望权重
                $article_agree_reputation = $s_agree_value + $verify_user_against_value;
                $article_against_reputation = $s_against_value + $verify_user_against_value;
                //赞同
                if ($article_agree_reputation < 0)
                {
                    $article_reputation = (0 - $article_agree_reputation) - 0.5;
                    if ($power_agree_factor > 1)
                    {
                        $article_agree_reputation = (0 - log($article_agree_reputation, $power_agree_factor));
                    }
                }
                if ($article_agree_reputation > 0)
                {
                    $article_agree_reputation = $article_agree_reputation + 0.5;
                    if ($power_agree_factor > 1)
                    {
                        $article_agree_reputation = log($article_agree_reputation, $power_agree_factor);
                    }
                }

                //反对
                if ($article_against_reputation < 0)
                {
                    $article_against_reputation = (0 - $article_against_reputation) - 0.5;
                    if ($power_agree_factor > 1)
                    {
                        $article_against_reputation = (0 - log($article_against_reputation, $power_against_factor));
                    }
                }
                if ($article_against_reputation > 0)
                {
                    $article_against_reputation = $article_against_reputation + 0.5;
                    if ($power_agree_factor > 1)
                    {
                        $article_against_reputation = log($article_against_reputation, $power_against_factor);
                    }
                }
                $user_reputation += $user_reputation + $article_against_reputation + $article_agree_reputation;
            }
        }

        //用户在问题回答中的声望计算（不包含问题发起者的回答）：
        //$user_answer_power =
        //（不同声望组用户声望系数 * （赞同数-反对数）） + （最佳回复数 * 最佳回复声望系数） + （感谢数 * 感谢声望系数） + （问题发起者声望系数*（赞同数-反对数）） + 认证用户声望系数*（赞同数-反对数）
        $user_answers = db('answer')->where('uid',$uid)->column('id, question_id, agree_count, thanks_count,uid');
        if ($user_answers)
        {
            $question_ids = $answer_ids = $questions_info = $vote_agree_users = $vote_against_users =  [];
            foreach ($user_answers as $key => $val)
            {
                $answer_ids[] = $val['id'];
                $question_ids[] = $val['question_id'];
            }

            if ($question_ids)
            {
                if ($questions_info_query = db('question')->whereIn('id',$question_ids)->column('id, best_answer, uid, category_id'))
                {
                    foreach ($questions_info_query AS $key => $val)
                    {
                        $questions_info[$val['id']] = $val;
                    }
                    unset($questions_info_query);
                }
            }

            if ($answer_ids)
            {
                $vote_agree_users = Vote::getVoteByItemIds('answer',$answer_ids,1);
                $vote_against_users = Vote::getVoteByItemIds('answer',$answer_ids,-1);
            }

            foreach ($user_answers as $key => $val)
            {
                if (!$questions_info[$val['question_id']])
                {
                    continue;
                }

                $s_publisher_agree = 0;	// 得到发起者赞同
                $s_publisher_against = 0;	// 得到发起者反对
                $s_verify_user_agree = 0;	// 得到发起者赞同
                $s_verify_user_against = 0;	// 得到发起者反对
                $s_agree_value = 0;	// 赞同声望系数
                $s_against_value = 0;	// 反对声望系数
                $s_best_answer = 0;

                // 是否最佳回复
                if ($questions_info && $questions_info[$val['question_id']]['best_answer'] == $val['id'])
                {
                    $s_best_answer+= $power_best_answer_factor;
                }
                else
                {
                    $s_best_answer = 0;
                }
                // 赞同的用户
                if ($vote_agree_users && $vote_agree_users[$val['id']])
                {
                    foreach ($vote_agree_users[$val['id']] AS $k => $v)
                    {
                        // 排除发起者
                        if ($questions_info[$val['question_id']]['uid'] != $val['uid'])
                        {
                            $voteUserInfo =  Users::getUserInfo($v['uid']);
                            if($voteUserInfo['verified'])
                            {
                                $s_verify_user_agree += $verify_user_power_factor;
                            }else if ($questions_info[$val['question_id']]['uid'] == $v['uid'] AND !$s_publisher_agree)
                            {
                                $s_publisher_agree += $publish_user_power_factor;
                            }else{
                                $s_agree_value += $voteUserInfo['power_factor'];
                            }
                        }
                    }
                }

                // 反对的用户
                if ($vote_against_users && $vote_against_users[$val['id']])
                {
                    foreach ($vote_against_users[$val['id']] AS $k => $v)
                    {
                        // 排除发起者
                        if ($questions_info[$val['question_id']]['uid'] != $val['uid'])
                        {
                            $voteUserInfo =  Users::getUserInfo($v['uid']);
                            if($voteUserInfo['verified'])
                            {
                                $s_verify_user_against += $verify_user_power_factor;
                            }else if ($questions_info[$val['question_id']]['uid'] == $v['uid'] AND !$s_publisher_agree)
                            {
                                $s_publisher_against += $publish_user_power_factor;
                            }else{
                                $s_against_value += $voteUserInfo['power_factor'];
                            }
                        }
                    }
                }

                $answer_agree_reputation = intval($s_agree_value + $s_publisher_agree  + $s_best_answer + $s_verify_user_agree);
                $answer_against_reputation = intval($s_against_value + $s_publisher_against + $s_verify_user_against);

                if ($answer_agree_reputation < 0)
                {
                    $answer_agree_reputation = (0 - $answer_agree_reputation) - 0.5;
                    if ($answer_agree_reputation >0 && $power_agree_factor > 1)
                    {
                        $answer_agree_reputation = (0 - log($answer_agree_reputation, $power_agree_factor));
                    }
                }
                else if ($answer_agree_reputation > 0)
                {
                    $answer_agree_reputation = $answer_agree_reputation + 0.5;

                    if ($answer_agree_reputation >0 && $power_agree_factor > 1)
                    {
                        $answer_agree_reputation = log($answer_agree_reputation, $power_agree_factor);
                    }
                }

                if ($answer_against_reputation < 0)
                {
                    $answer_against_reputation = (0 - $answer_against_reputation) - 0.5;
                    if ($power_against_factor > 1)
                    {
                        $answer_against_reputation = (0 - log($answer_against_reputation, $power_against_factor));
                    }
                }
                else if ($answer_against_reputation > 0)
                {
                    $answer_against_reputation = $answer_against_reputation + 0.5;

                    if ($power_against_factor > 1)
                    {
                        $answer_against_reputation = log($answer_against_reputation, $power_against_factor);
                    }
                }

                $user_reputation += $answer_against_reputation + $answer_agree_reputation;
            }

            Users::updateUserFiled($uid,array(
                'power' => round($user_reputation),
            ));
            return Users::updateUsersPowerGroup($uid);
        }

        //最终声望计算： $user_power = $user_article_power + $user_answer_power;
        //$user_power > 0 时 $user_power = (0 - $user_power) - 0.5; 对数底数 $log_factor ,  $user_power = (0 - log($user_power, $log_factor));
        //$user_power < 0 时 $user_power =$user_power + 0.5; 对数底数 $log_factor , $user_power = log($user_power, $log_factor);
    }
}