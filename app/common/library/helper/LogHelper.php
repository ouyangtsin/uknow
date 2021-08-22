<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

use app\common\logic\common\FocusLogic;
use app\common\model\Favorite;
use app\common\model\Users;
use app\ask\model\Answer;
use app\ask\model\Article;
use app\ask\model\Column;
use app\ask\model\Question;
use app\ask\model\Report;
use app\ask\model\Topic;
use app\ask\model\Vote;
use think\facade\Db;

class LogHelper
{
    /**
     * 添加积分记录
     * @param $action
     * @param $record_id
     * @param string $record_db
     * @param int $uid
     * @return bool
     */
    public static function addScoreLog($action, $record_id, $record_db='', $uid=0)
    {
        $uid = $uid ? : session('login_uid');
        //参数检查
        if (empty($action) || empty($record_id) || empty($uid)) {
            return false;
        }
        //查询行为,判断是否执行
        $rule_info = db('score_rule')->where(['name'=>$action,'status'=>1])->find();

        if (!$rule_info) {
            return false;
        }

        $user_score = db('users')->where(['uid'=>$uid])->value('score');

        if($rule_info['score']<0 && $user_score<abs($rule_info['score']))
        {
            return false;
        }

        $balance = db('score_log')->sum('score');

        $balance = $balance + $rule_info['score'];
        //插入行为日志
        $data['action_type']   = $action;
        $data['uid']     = $uid;
        $data['record_id']   = $record_id;
        $data['record_db']   = $record_db;
        $data['create_time'] = time();
        $data['score'] = $rule_info['score'];
        $data['balance'] = $balance;
        //解析日志规则,生成日志备注
        if (!empty($rule_info['log']))
        {
            if (preg_match_all('/\[(\S+?)\]/', $rule_info['log'], $match)) {
                $log['user']   = $uid;
                $log['record'] = $record_id;
                $log['time']   = formatTime(time());
                $log['data']   = array('user' => $uid,'record' => $record_id, 'time' => formatTime(time()));
                $replace = [];
                foreach ($match[1] as $value) {
                    $param = explode('|', $value);
                    if (isset($param[1])) {
                        $replace[] = call_user_func($param[1], $log[$param[0]]);
                    } else {
                        $replace[] = $log[$param[0]];
                    }
                }
                $data['remark'] = str_replace($match[0], $replace, $rule_info['log']);
            } else {
                $data['remark'] = $rule_info['log'];
            }
        } else {
            //未定义日志规则，记录操作url
            $data['remark'] = '';
        }

        if($rule_info['cycle'])
        {
            $cycle_time = 0 ;
            switch($rule_info['cycle_type'])
            {
                case 'month':
                    $cycle_time = $rule_info['cycle']*365*24*60*60;
                    break;
                case 'week':
                    $cycle_time = $rule_info['cycle']*7*24*60*60;
                    break;
                case 'day':
                    $cycle_time = $rule_info['cycle']*24*60*60;
                    break;
                case 'hour':
                    $cycle_time = $rule_info['cycle']*60*60;
                    break;
                case 'minute':
                    $cycle_time = $rule_info['cycle']*60;
                    break;
                case 'second':
                    $cycle_time = $rule_info['cycle'];
                    break;
            }
            $map[] = ['create_time','>', time() - (int)$cycle_time];
            $exec_count = db('score_log')->where($map)->count();
            if ($rule_info['max']!=0 and $exec_count >= $rule_info['max']) {
                return true;
            }
        }

        db('score_log')->insert($data);
        $res = Users::updateUserFiled($uid,['score'=>$balance]);
        //更新积分组
        Users::updateUsersGroup($uid);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 检查用户积分是否足够操作
     * @param $action
     * @param int $uid
     * @return bool
     */
    public static function checkUserScore($action,$uid=0)
    {
        $rule_info = db('score_rule')->where(['name'=>$action,'status'=>1])->find();
        if ($rule_info) {
            $user_score = db('users')->where(['uid'=>$uid])->value('score');
            if($rule_info['score']<0 && $user_score<abs($rule_info['score']))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取操作记录
     * @param $action
     * @param $uid
     * @param $current_uid
     * @param int $page
     * @param int $per_page
     * @param string $pjax_page
     * @return array
     */
    public static function getActionLogList($action,$uid,$current_uid,$page=1,$per_page=10,$pjax_page='')
    {
        $action = is_array($action) ? $action : explode(',',$action);
        $uid = is_array($uid) ? $uid : explode(',',$uid);
        $action_ids = [];
        if($action)
        {
            $action_ids = db('action')->whereIn('name',$action)->where(['status'=>1])->column('id');
        }
        $where[] = ['uid','IN',$uid];
        if(!empty($action_ids))
        {
            $where[] = ['action_id','IN',$action_ids];
        }

        if(!in_array($current_uid,$uid))
        {
            $where[] = ['anonymous','=',0];
        }

        $param =request()->param();
        $action_log_list = db('action_log')->where($where)->paginate(
            [
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>$param,
                'pjax'=>$pjax_page
            ]
        );

        $pageVar = $action_log_list->render();
        $question_ids = $article_ids = $answer_ids = $column_ids = $article_comment_ids = $answer_comment_ids = $data_list_uid = $topic_infos = [];
        foreach ($action_log_list->all() as $key => $val)
        {
            switch ($val['model'])
            {
                case 'question':
                    $question_ids[] = $val['record_id'];
                    break;
                case 'article':
                    $article_ids[] = $val['record_id'];
                    break;
                case 'answer':
                    $answer_ids[] = $val['record_id'];
                    break;
                case 'column':
                    $column_ids[] = $val['record_id'];
                    break;
                case 'article_comment':
                    $article_comment_ids[] = $val['record_id'];
                    break;
                case 'answer_comment':
                    $answer_comment_ids[] = $val['record_id'];
                    break;
            }
            $data_list_uid[$val['uid']] = $val['uid'];
        }

        $question_infos = $article_infos = $answer_infos = array();

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
        if($answer_ids)
        {
            $answer_infos = Answer::getAnswerInfoByIds($answer_ids);
        }
        $users_info = Users::getUserInfoByIds($data_list_uid);

        $result_list = array();
        foreach ($action_log_list->all() as $key => $val)
        {
            switch ($val['model'])
            {
                case 'question':
                    if($question_infos && isset($question_infos[$val['record_id']]))
                    {
                        $result_list[$key] = $question_infos[$val['record_id']];
                        //$result_list[$key]['img_list'] =HtmlHelper::parseImg($question_infos[$val['record_id']]['detail']);
                        $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($current_uid,'question',$val['record_id']);
                        $result_list[$key]['vote_value'] = Vote::getVoteByType($val['record_id'],'question',$current_uid);
                        $result_list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);
                        $result_list[$key]['topics'] = $topic_infos['question'][$val['record_id']] ?? [];
                        $result_list[$key]['user_info'] = $users_info[$val['uid']];
                        $result_list[$key]['item_type'] = $val['model'];
                        $result_list[$key]['remark'] = $val['remark'];
                    }
                    break;
                case 'article':
                    if($article_infos)
                    {
                        $result_list[$key] = $article_infos[$val['record_id']];
                        //$result_list[$key]['img_list'] = HtmlHelper::parseImg($article_infos[$val['record_id']]['message']);
                        $result_list[$key]['message'] = str_cut(strip_tags($result_list[$key]['message']),0,100);
                        $result_list[$key]['topics'] = $topic_infos['article'][$val['record_id']] ?? [];
                        $result_list[$key]['user_info'] = $users_info[$val ['uid']];
                        //$result_list[$key]['column_info'] = $article_infos[$val['record_id']]['column_id'] ? Column::where(['verify'=>1])->column('name,cover,uid,post_count,join_count') : false;
                        $result_list[$key]['vote_value'] = Vote::getVoteByType($val['record_id'],'article',$current_uid);
                        $result_list[$key]['item_type'] = $val['model'];
                        $result_list[$key]['remark'] = $val['remark'];
                    }
                    break;
                case 'answer':
                    if($answer_infos && isset($answer_infos[$val['record_id']]))
                    {
                        $question_id = $answer_infos[$val['record_id']]['question_id'];
                        $result_list[$key] = Question::getQuestionInfo($question_id);
                        $result_list[$key]['answer_info'] = $answer_infos[$val['record_id']];
                        $result_list[$key]['vote_value'] = Vote::getVoteByType($question_id,'answer',$val['record_id']);
                        $result_list[$key]['detail'] = '<a href="'.$users_info[$val['uid']]['url'].'" class="uk-username" >'.$users_info[$val['uid']]['user_name'].'</a> :'.str_cut(strip_tags(htmlspecialchars_decode($answer_infos[$val['record_id']]['content'])),0,150);
                        $result_list[$key]['topics'] = $topic_infos['question'][$question_id] ?? [];
                        $result_list[$key]['user_info'] = $users_info[$val['uid']];
                        $result_list[$key]['item_type'] = $val['model'];
                        $result_list[$key]['remark'] = $val['remark'];
                    }
                    break;
            }
        }
        $action_log_list['data'] = $result_list;
        $allList = $action_log_list->toArray();
        return ['list'=>$result_list,'page'=>$pageVar,'total'=>ceil($allList['last_page']/$per_page)];
    }

    /**
     * 获取操作记录数量
     * @param $action
     * @param $uid
     * @param $current_uid
     * @return mixed
     */
    public static function getActionLogCount($action,$uid,$current_uid)
    {
        $action = is_array($action) ? $action : explode(',',$action);
        $uid = is_array($uid) ? $uid : explode(',',$uid);
        $action_ids = [];
        if($action)
        {
            $action_ids = db('action')->whereIn('name',$action)->where(['status'=>1])->column('id');
        }
        $where[] = ['uid','IN',$uid];
        if(!empty($action_ids))
        {
            $where[] = ['action_id','IN',$action_ids];
        }
        if(!in_array($current_uid,$uid))
        {
            $where[] = ['anonymous','=',0];
        }
        return db('action_log')->where($where)->count();
    }

    /**
     * 记录积分日志，并执行该行为的规则
     * @param null $action 行为标识
     * @param null $model 触发行为的模型名
     * @param null $record_id 触发行为的记录id
     * @param int $uid 执行行为的用户id
     * @return boolean
     */
    public static function addActionLog($action = null, $model = null, $record_id = null, $uid = 0,$anonymous=0)
    {
        //参数检查
        if (empty($action) || empty($model) || empty($record_id)) {
            return false;
        }

        //查询行为,判断是否执行
        $action_info = db('action')->where('name',$action)->find();

        if (!$action_info || $action_info['status'] != 1) {
            return false;
        }
        //插入行为日志
        $data['action_id']   = $action_info['id'];
        $data['uid']     = $uid;
        $data['action_ip']   = IpHelper::getRealIp();
        $data['model']       = $model;
        $data['record_id']   = $record_id;
        $data['anonymous']   = $anonymous;
        $data['create_time'] = time();
        //解析日志规则,生成日志备注
        if (!empty($action_info['log_rule']))
        {
            if (preg_match_all('/\[(\S+?)\]/', $action_info['log_rule'], $match)) {
                $log['user']   = $uid;
                $log['record'] = $record_id;
                $log['model']  = $model;
                $log['time']   = time();
                $log['data']   = array('user' => $uid, 'model' => $model, 'record' => $record_id, 'time' => time());
                $replaces = array();
                foreach ($match[1] as $key=> $value)
                {
                    $param = explode('|', $value);
                    if (isset($param[1])) {
                        $replaces[] = call_user_func($param[1], $log[$param[0]]);
                    } else {
                        $replaces[] = $log[$param[0]];
                    }
                }
                $data['remark'] = str_replace($match[0], $replaces, $action_info['log_rule']);
            } else {
                $data['remark'] = $action_info['log_rule'];
            }
        } else {
            //未定义日志规则，记录操作url
            $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
        }

        db('action_log')->insert($data);

        if (!empty($action_info['action_rule'])) {
            //解析行为
            $rules = self::parse_action($action, $uid);
            //执行行为
            self::execute_action($rules, $action_info['id'], $uid);
        }
    }

    /**
     * 解析行为规则
     * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
     * 规则字段解释：table->要操作的数据表，不需要加表前缀；
     *              field->要操作的字段；
     *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
     *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
     *              cycle->执行周期，单位（分钟），表示$cycle小时内最多执行$max次
     *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
     * 单个行为后可加 ； 连接其他规则
     * @param string|mixed $action 行为id或者name
     * @param string $self 替换规则里的变量为执行用户的id
     * @return array|bool
     */
    public static function parse_action($action, $self='')
    {
        if (empty($action)) {
            return false;
        }
        //参数支持id或者name
        if (is_numeric($action)) {
            $map = array('id' => $action);
        } else {
            $map = array('name' => $action);
        }

        //查询行为信息
        $info = db('action')->where($map)->find();
        if (!$info || $info['status'] != 1) {
            return false;
        }
        //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
        $rules  = $info['action_rule'];
        $rules  = str_replace('{$self}', $self, $rules);
        $rules  = array_filter(explode(';', $rules));
        $return = array();
        foreach ($rules as $key => $rule) {
            $rule = explode('|', $rule);
            foreach ($rule as $k => $fields) {
                $field = empty($fields) ? array() : explode(':', $fields);
                if (!empty($field)) {
                    $return[$key][$field[0]] = $field[1];
                }
            }
            //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
            if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
                unset($return[$key]['cycle'], $return[$key]['max']);
            }
        }
        return $return;
    }

    /**
     * 执行行为
     * @param array $rules 解析后的规则数组
     * @param int $action_id 行为id
     * @param int $user_id 执行的用户id
     * @return boolean false 失败 ， true 成功
     */
    public static function execute_action(array $rules, int $action_id, int $user_id): bool
    {
        if (!$rules || empty($action_id) || empty($user_id)) {
            return false;
        }
        $return = true;
        foreach ($rules as $rule) {
            //检查执行周期
            $map[]  = ['action_id','=', $action_id];
            $map[] = [ 'uid','=', $user_id];

            if(isset($rule['cycle']) && isset($rule['max']))
            {
                $map[] = ['create_time','>', time() - (int)$rule['cycle'] * 60];
                $exec_count         = db('action_log')->where($map)->count();
                if ($exec_count > $rule['max']) {
                    continue;
                }
            }

            //执行数据库操作
            $field = $rule['field'];
            $table_name = config('database.connections.mysql.prefix').$rule['table'];
            $sql = "update ".$table_name.' set '.$field.' = '.$rule['rule'] . ' where '.$rule['condition'];
            $res = Db::execute($sql);
            if (!$res) {
                $return = false;
            }
        }
        return $return;
    }
}
