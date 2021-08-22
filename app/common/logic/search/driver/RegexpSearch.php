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

use app\common\library\helper\HtmlHelper;
use app\common\model\Favorite;
use app\common\model\Users;
use app\ask\model\Answer;
use app\ask\model\Article;
use app\ask\model\Column;
use app\ask\model\Question;
use app\ask\model\Report;
use app\ask\model\Topic;
use app\ask\model\Vote;

class RegexpSearch
{
    /**
     * 聚合搜索
     * @param $keywords
     * @param array $where
     * @param int $uid
     * @param string $sort
     * @param int $page
     * @param int $per_page
     * @return array|false|mixed
     */
    public function searchMixed($keywords,$where=[],$uid=0,$sort=[],$page=1,$per_page=10)
    {
        $articleSql = db('article')
            ->whereRaw("status=1 AND (message regexp '".implode('|', $keywords)."' OR title regexp '".implode('|', $keywords)."')")
            ->field('id,title,uid,message as detail,"article" as search_type,create_time')
            ->fetchSql()
            ->select();

        $userSql = db('users')
            ->whereRaw("status=1 AND (user_name regexp '".implode('|', $keywords)."' OR nick_name regexp '".implode('|', $keywords)."')")
            ->field('uid as id,uid,user_name as title,signature as detail,"users" as search_type,create_time')
            ->fetchSql()
            ->select();

        $topicSql = db('topic')
            ->whereRaw("title regexp '".implode('|', $keywords)."' OR description regexp '".implode('|', $keywords)."'")
            ->field('id,0 as uid,title,description as detail,"topic" as search_type,create_time')
            ->fetchSql()
            ->select();

        $searchResult =db('question')
            ->field("id,title,uid,detail,'question' as search_type,create_time")
            ->where($where)
            ->whereRaw("status=1 AND (`title` regexp '".implode('|', $keywords)."' OR `detail` regexp '".implode('|', $keywords)."')")
            ->union($articleSql)
            ->union($userSql)
            ->union($topicSql)
            ->order($sort)
            ->page(intval($page),intval($per_page))
            ->select()
            ->toArray();

        $totalCount = db('question')
            ->where($where)
            ->field("id,title,uid,detail,'question' as search_type,create_time")
            ->whereRaw("status=1 AND (`title` regexp '".implode('|', $keywords)."' OR `detail` regexp '".implode('|', $keywords)."')")
            ->union($articleSql)
            ->union($userSql)
            ->union($topicSql)
            ->select();
        $totalCount = count($totalCount);
        return $this->parseMixResult($searchResult,$uid,ceil($totalCount/$per_page));
    }

    /**
     * 搜索问题
     * @param $searchText
     * @param array $where
     * @param int $uid
     * @param array $sort
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function searchQuestion($searchText,$where=[],$uid=0,$sort=[],$page=1,$per_page=2)
    {
        $searchResult = db('question')
            ->where($where)
            ->field('id,title,uid,detail,"question" as search_type,create_time')
            ->whereRaw("status=1 AND (`title` regexp '".implode('|', $searchText)."' OR `detail` regexp '".implode('|', $searchText)."')")
            ->order($sort)
            ->fetchSql()
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ])->toArray();
        return $this->parseMixResult($searchResult['data'],$uid,$searchResult['last_page']);
    }

    /**
     * 搜索文章
     * @param $searchText
     * @param $where
     * @param int $uid
     * @param array $sort
     * @param int $page
     * @param int $per_page
     * @return array|false|mixed
     */
    public function searchArticle($searchText,$where,$uid=0,$sort=[],$page=1,$per_page=2)
    {
        $searchResult = db('article')
            ->where($where)
            ->field('id,title,uid,message as detail,"article" as search_type,create_time')
            ->whereRaw("status=1 AND (message regexp '".implode('|', $searchText)."' OR title regexp '".implode('|', $searchText)."')")
            ->order($sort)
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ])->toArray();
        return $this->parseMixResult($searchResult['data'],$uid,$searchResult['last_page']);
    }

    /**
     * 搜索用户
     * @param $searchText
     * @param array $where
     * @param int $uid
     * @param array $sort
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function searchUser($searchText,$where=[],$uid=0,$sort=[],$page=1,$per_page=10)
    {
        $searchResult = db('users')
            ->where($where)
            ->field('uid as id,uid,user_name as title,signature as detail,"users" as search_type,create_time')
            ->whereRaw("status=1 AND (user_name regexp '".implode('|', $searchText)."' OR nick_name regexp '".implode('|', $searchText)."')")
            ->order($sort)
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ])->toArray();
        return $this->parseMixResult($searchResult['data'],$uid,$searchResult['last_page']);
    }

    /**
     * 搜索话题
     * @param $searchText
     * @param array $where
     * @param int $uid
     * @param array $sort
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function searchTopic($searchText,$where=[],$uid=0,$sort=[],$page=1,$per_page=10)
    {
        $searchResult = db('topic')
            ->where($where)
            ->field('id,0 as uid,title,description as detail,"topic" as search_type,create_time')
            ->whereRaw("title regexp '".implode('|', $searchText)."' OR description regexp '".implode('|', $searchText)."')")
            ->order($sort)
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ])->toArray();
        return $this->parseMixResult($searchResult['data'],$uid,$searchResult['last_page']);
    }

    /**
     * 聚合解析
     * @param $contents
     * @param int $uid
     * @param int $totalRow
     * @return mixed
     */
    public function parseMixResult($contents,$uid=0,$totalRow=0)
    {
        if (empty($contents)) {
            return false;
        }

        $question_ids = $article_ids = $data_list_uid = $question_infos = $article_infos =  array();

        foreach ($contents as $key => $data)
        {
            switch ($data['search_type'])
            {
                case 'question':
                    $question_ids[] = $data['id'];
                    break;

                case 'article':
                    $article_ids[] = $data['id'];
                    break;
            }
            $data_list_uid[$data['uid']] = $data['uid'];
        }

        $last_answers = array();
        $topic_infos = array();

        if ($question_ids)
        {
            if ($last_answers = Answer::getLastAnswerByIds($question_ids))
            {
                foreach ($last_answers as $key => $val)
                {
                    $data_list_uid[$val['uid']] = $val['uid'];
                }
            }
            $topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
            $question_infos = Question::getQuestionByIds($question_ids);
        }

        if ($article_ids)
        {
            $topic_infos['article'] = Topic::getTopicByItemIds($article_ids, 'article');
            $article_infos = Article::getArticleByIds($article_ids);
        }
        $users_info = Users::getUserInfoByIds($data_list_uid);

        $result_list = array();
        foreach ($contents as $key => $data)
        {
            switch ($data['search_type'])
            {
                case 'question':
                    if($question_infos && isset($question_infos[$data['id']]))
                    {
                        $result_list[$key] = $question_infos[$data['id']];
                        $result_list[$key]['answer_info'] = $last_answers ? $last_answers[$data['id']] : false;

                        if($result_list[$key]['answer_info']){
                            $result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$data['id']]['uid']];
                            $result_list[$key]['detail'] = '<a href="'.$result_list[$key]['answer_info']['user_info']['url'].'" class="uk-username" >'.$result_list[$key]['answer_info']['user_info']['nick_name'].'</a> :'.str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['answer_info']['content'])),0,150);
                            $result_list[$key]['img_list'] =  HtmlHelper::parseImg($last_answers[$data['id']]['content']);
                        }else{
                            $result_list[$key]['img_list'] =HtmlHelper::parseImg($question_infos[$data['id']]['detail']);
                            $result_list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);
                        }
                        $result_list[$key]['is_favorite'] = Favorite::checkFavorite($uid,'question',$data['id']);
                        $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
                        $result_list[$key]['search_type'] = 'question';
                        $result_list[$key]['topics'] = $topic_infos['question'][$data['id']] ?? [];
                        $result_list[$key]['user_info'] = $users_info[$data['uid']];
                    }
                    break;

                case 'article':
                    if($article_infos)
                    {
                        $result_list[$key] = $article_infos[$data['id']];
                        $result_list[$key]['img_list'] = HtmlHelper::parseImg($article_infos[$data['id']]['message']);
                        $result_list[$key]['message'] = str_cut(strip_tags($result_list[$key]['message']),0,100);
                        $result_list[$key]['search_type'] = 'article';
                        $result_list[$key]['is_favorite'] = Favorite::checkFavorite($uid,'article',$data['id']);
                        $result_list[$key]['is_report'] = Report::getReportInfo($data['id'],'article',$uid);
                        $result_list[$key]['topics'] = $topic_infos['article'][$data['id']] ?? [];
                        $result_list[$key]['user_info'] = $users_info[$data['uid']];
                        $result_list[$key]['column_info'] = $article_infos[$data['id']]['column_id'] ? Column::where(['verify'=>1])->column('name,cover,uid,post_count,join_count') : false;
                        $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'article',$uid);
                    }
                    break;
            }
        }

        return ['list'=>$result_list,'total'=>$totalRow];
    }
}