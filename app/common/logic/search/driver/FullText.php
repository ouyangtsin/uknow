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

namespace app\common\logic\search\driver;
use app\common\model\Users;
use app\ask\model\Article;
use app\ask\model\Question;
use app\ask\model\Topic;
use app\ask\model\Vote;
use think\facade\Db;

/**
 * 本地搜索引擎
 * Class LocalSearch
 * @package app\common\logic\search\driver
 */
class FullText
{
    /**
     * 聚合搜索
     * @param $keywords
     * @param $uid
     * @param $sort
     * @param $page
     * @param $per_page
     * @return mixed
     */
    public function searchMixed($keywords,$uid,$sort='',$page=1,$per_page=10)
    {
        $prefix = config('database.connections.mysql.prefix');
        $searchResult = Db::table($prefix.'question')
            ->alias('q')
            ->field('q.id,q.title,q.uid,q.detail,"question" as type')
            ->whereRaw('q.status=1 AND match(q.title,q.detail) against("'.$keywords.'")')
            ->union('select a.id,a.title,a.uid,a.message as detail,"article" as type from '.$prefix.'article a where a.status=1 AND match(a.title,a.message) against("'.$keywords.'")')
            ->union('select u.uid as id,u.uid,u.user_name as title,u.signature as detail,"users" as type from '.$prefix.'users u where u.status=1 AND match(u.nick_name,u.user_name) against("'.$keywords.'")')
            ->union('select t.id,0 as uid,t.title,t.description as detail,"topic" as type from '.$prefix.'topic t where match(t.title,t.description) against("'.$keywords.'")')
            //->order($sort)
            ->page(intval($page),intval($per_page))
            ->select()
            ->toArray();
        $total = Db::table($prefix.'question')
            ->alias('q')
            ->field('q.id,q.title,q.uid,q.detail,"question" as type')
            ->whereRaw('q.status=1 AND match(q.title,q.detail) against("'.$keywords.'")')
            ->union('select a.id,a.title,a.uid,a.message as detail,"article" as type from '.$prefix.'article a where a.status=1 AND match(a.title,a.message) against("'.$keywords.'")')
            ->union('select u.uid as id,u.uid,u.user_name as title,u.signature as detail,"users" as type from '.$prefix.'users u where u.status=1 AND match(u.nick_name,u.user_name) against("'.$keywords.'")')
            ->union('select t.id,0 as uid,t.title,t.description as detail,"topic" as type from '.$prefix.'topic t where match(t.title,t.description) against("'.$keywords.'")')
            //->order($sort)
            ->select()
            ->toArray();
        return $this->parseMixResult($searchResult,$uid,ceil(count($total)/intval($per_page)));
    }

    /**
     * 搜索问题
     * @param $searchText
     * @param $uid
     * @param $sort
     * @param $page
     * @param $per_page
     * @return mixed
     */
    public function searchQuestion($searchText,$uid,$sort,$page=1,$per_page=2)
    {
        $table = config('database.connections.mysql.prefix') .'question';
        $sql = 'select SQL_CALC_FOUND_ROWS id,title,detail,uid,"question" as type from '.$table. ' where status=1 AND match(title,detail) against("'.$searchText.'") order by '.$sort.' limit '.$page.','.$per_page;
        $searchResult =  Db::query($sql);
        $totalRow = Db::query('SELECT FOUND_ROWS() as count');
        $totalRow = $totalRow[0]['count'] ? ceil(intval($totalRow[0]['count'])/$per_page) : 0;
        return $this->parseMixResult($searchResult,$uid,$totalRow);
    }

    /**
     * 搜索文章
     * @param $searchText
     * @param $uid
     * @param $sort
     * @param $page
     * @param $per_page
     * @return mixed
     */
    public function searchArticle($searchText,$uid,$sort,$page=1,$per_page=10)
    {
        $table = config('database.connections.mysql.prefix') .'article';
        $sql = 'select SQL_CALC_FOUND_ROWS id,title,uid,message as detail,"article" as type from '.$table. ' where status=1 AND match(title,message) against("'.$searchText.'") order by '.$sort.' limit '.$page.','.$per_page;
        $searchResult =  Db::query($sql);
        $totalRow = Db::query('SELECT FOUND_ROWS() as count');
        $totalRow = $totalRow[0]['count'] ? ceil(intval($totalRow[0]['count'])/$per_page) : 0;
        return $this->parseMixResult($searchResult,$uid,$totalRow);
    }

    /**
     * 搜索用户
     * @param $searchText
     * @param $uid
     * @param $sort
     * @param $page
     * @param $per_page
     * @return mixed
     */
    public function searchUser($searchText,$uid,$sort,$page=1,$per_page=10)
    {
        $table = config('database.connections.mysql.prefix') .'users';
        $sql = 'select SQL_CALC_FOUND_ROWS uid as id,uid,user_name as title,signature as detail,"users" as type from '.$table. ' where status=1 AND match(nick_name,user_name) against("'.$searchText.'") order by '.$sort.' limit '.$page.','.$per_page;
        $searchResult = Db::query($sql);
        $totalRow = Db::query('SELECT FOUND_ROWS() as count');
        $totalRow = $totalRow[0]['count'] ? ceil(intval($totalRow[0]['count'])/$per_page) : 0;
        return $this->parseMixResult($searchResult,$uid,$totalRow);
    }

    /**
     * 搜索话题
     * @param $searchText
     * @param $uid
     * @param $sort
     * @param $page
     * @param $per_page
     * @return mixed
     */
    public function searchTopic($searchText,$uid,$sort,$page=1,$per_page=10)
    {
        $table = config('database.connections.mysql.prefix') .'topic';
        $sql = 'select SQL_CALC_FOUND_ROWS id,uid,title,description as detail,"topic" as type from '.$table. ' where match(title,description) against("'.$searchText.'") order by '.$sort.' limit '.$page.','.$per_page;
        $searchResult = Db::query($sql);
        $totalRow = Db::query('SELECT FOUND_ROWS() as count');
        $totalRow = $totalRow[0]['count'] ? ceil(intval($totalRow[0]['count'])/$per_page) : 0;
        return $this->parseMixResult($searchResult,$uid,$totalRow);
    }

    /**
     * 聚合解析
     * @param $result
     * @param int $uid
     * @param int $totalRow
     * @return mixed
     */
    public function parseMixResult($result,$uid=0,$totalRow=0)
    {
        if(empty($result)) return false;

        foreach ($result as $key=>$val)
        {
            switch ($val['type'])
            {
                case 'question':
                    $question_ids[] = $val['id'];
                    break;
                case 'article':
                    $article_ids[] = $val['id'];
                    break;
                case 'users':
                    $user_ids[] = $val['id'];
                    break;
                case 'topic':
                    $topic_ids[] = $val['id'];
                    break;
            }
        }

        if(isset($question_ids) && !empty($question_ids))
        {
            $question_infos = Question::getQuestionByIds($question_ids);
        }

        if(isset($article_ids) && !empty($article_ids))
        {
            $article_infos = Article::getArticleByIds($article_ids);
        }

        if(isset($user_ids) && !empty($user_ids))
        {
            $user_infos = Users::getUserInfoByIds($user_ids);
        }

        if(isset($topic_ids) && !empty($topic_ids))
        {
            $topic_infos = Topic::getTopicByIds($topic_ids);
        }

        $list = [];

        foreach ($result as $key=>$val)
        {
            switch ($val['type'])
            {
                case 'question':
                    if(isset($question_infos))
                    {
                        $list[$key] = $question_infos[$val['id']];
                        $list[$key]['search_type'] = 'question';
                        $list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'question',$uid);
                        $list[$key]['topics'] = Topic::getTopicByItemType('question',$val['id']);
                        $list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($val['detail'])),0,150);
                        $list[$key]['user_info'] = Users::getUserInfo($val['uid']);
                    }
                    break;

                case 'article':
                    if(isset($article_infos)) {
                        $list[$key] = $article_infos[$val['id']];
                        $list[$key]['search_type'] = 'article';
                        $list[$key]['vote_value'] = Vote::getVoteByType($val['id'], 'question', $uid);
                        $list[$key]['topics'] = Topic::getTopicByItemType('question', $val['id']);
                        $list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($val['detail'])), 0, 150);
                        $list[$key]['user_info'] = Users::getUserInfo($val['uid']);
                    }
                    break;

                case 'users':
                    if(isset($user_infos))
                    {
                        $list[$key] = $user_infos[$val['id']];
                        $list[$key]['search_type'] = 'users';
                    }
                    break;
                case 'topic':
                    if(isset($topic_infos))
                    {
                        $list[$key] = $topic_infos[$val['id']];
                        $list[$key]['search_type'] = 'topic';
                    }
                    break;
            }
        }

        return ['list'=>$list,'total'=>$totalRow];
    }
}