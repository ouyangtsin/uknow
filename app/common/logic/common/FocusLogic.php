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


namespace app\common\logic\common;

use app\common\library\helper\LogHelper;
use app\common\model\Users;
use app\ask\model\Column;
use app\ask\model\Question;
use app\ask\model\Topic;

class FocusLogic
{
    public static $error;

    /**
     * 更新全局关注
     * @param $item_id
     * @param $item_type
     * @param $uid
     * @return array|false
     */
    public static function updateFocusAction($item_id,$item_type,$uid)
    {
        if(!$item_id || !$item_type || !$uid)
        {
            self::setError('请求参数不正确');
            return false;
        }

        $dbName = 'question_focus';
        $where = $insertData = [];
        switch ($item_type)
        {
            case 'question':
                $dbName = 'question_focus';

                $where['uid'] = (int)$uid;
                $where['question_id']=(int)$item_id;
                $insertData = [
                    'question_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'topic':
                $dbName = 'topic_focus';

                $where['uid'] = (int)$uid;
                $where['topic_id']=(int)$item_id;
                $insertData = [
                    'topic_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'user':
                $dbName = 'users_follow';

                $where['fans_uid'] = (int)$uid;
                $where['friend_uid'] = (int)$item_id;
                $insertData = [
                    'friend_uid'=>(int)$item_id,
                    'fans_uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'column':
                $dbName = 'column_focus';
                $where['uid'] = (int)$uid;
                $where['column_id'] = (int)$item_id;
                $insertData = [
                    'column_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'favorite':
                $dbName = 'favorite_focus';
                $where['uid'] = (int)$uid;
                $where['tag_id'] = (int)$item_id;
                $insertData = [
                    'tag_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
        }

        if(db($dbName)->where($where)->value('id'))
        {
            if(!db($dbName)->where($where)->delete())
            {
                self::setError('取消关注失败');
                return false;
            }
            //添加行为日志
            LogHelper::addActionLog('chance_focus_'.$item_type,$item_type,$item_id,$uid);
            $count = self::updateFocusCount($item_id,$item_type);
            return ['count'=>$count,'type'=>'un_focus'];
        }

        if(!db($dbName)->insertGetId($insertData))
        {
            self::setError('关注失败');
            return false;
        }
        LogHelper::addActionLog('focus_'.$item_type,$item_type,$item_id,$uid);
        switch ($item_type){
            case 'user':
                $user_name=get_user_info($uid,'user_name');
                send_notify($uid,$item_id,'TYPE_PEOPLE_FOCUS_ME','【'.$user_name.'】 关注了您',$item_id,['item_type'=>'user','uid'=>$uid,'message'=>$user_name.'关注了您','url'=>url('user/detail',['id'=>$item_id]),'title'=>$user_name]);
                break;
        }

        $count = self::updateFocusCount($item_id,$item_type);
        return ['count'=>$count,'type'=>'focus'];
    }

    /**
     * 检查用户是否关注过
     * @param int $uid
     * @param string $item_type
     * @param int $item_id
     * @return mixed
     */
    public static function checkUserIsFocus(int $uid, string $item_type, int $item_id)
    {
        if(!$item_id || !$item_type || !$uid)
        {
            self::setError('请求参数不正确');
            return false;
        }
        $where = [];
        $dbName = '';

        switch ($item_type)
        {
            case 'question':
                $dbName = 'question_focus';
                $where['uid'] = (int)$uid;
                $where['question_id']=(int)$item_id;
                break;
            case 'topic':
                $dbName = 'topic_focus';
                $where['uid'] = (int)$uid;
                $where['topic_id']=(int)$item_id;
                break;
            case 'user':
                $dbName = 'users_follow';
                $where['fans_uid'] = (int)$uid;
                $where['friend_uid'] = (int)$item_id;
                break;
            case 'column':
                $dbName = 'column_focus';
                $where['uid'] = (int)$uid;
                $where['column_id'] = (int)$item_id;
                break;

            case 'favorite':
                $dbName = 'favorite_focus';
                $where['uid'] = (int)$uid;
                $where['tag_id'] = (int)$item_id;
                break;
        }
        return db($dbName)->where($where)->value('id');
    }

    /**
     * 更新关注数量
     * @param $item_id
     * @param $item_type
     * @return int
     */
    public static function updateFocusCount($item_id,$item_type): int
    {
        $count = 0;
        switch ($item_type)
        {
            case 'question':
                $dbName = 'question_focus';
                $countWhere = ['question_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                Question::update(['focus_count'=>$count],['id'=>$item_id]);
                break;
            case 'topic':
                $dbName = 'topic_focus';
                $countWhere =['topic_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                Topic::update(['focus'=>$count],['id'=>$item_id]);
                break;
            case 'user':
                $dbName = 'users_follow';
                $countWhere =['friend_uid'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                $fans_count = db($dbName)->where(['fans_uid'=>(int)$item_id])->count();
                Users::updateUserFiled($item_id,['friend_count'=>$count,'fans_count'=>$fans_count]);
                break;
            case 'column':
                $dbName = 'column_focus';
                $countWhere =['column_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                Column::update(['focus_count'=>$count],['id'=>(int)$item_id]);
                break;
            case 'favorite':
                $dbName = 'favorite_focus';
                $countWhere =['tag_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                db('favorite_tag')->where(['id'=>$item_id])->update(['focus_count'=>$count]);
                break;
        }

        return $count;
    }

    /**
     * 获取用户关注内容
     * @param $uid
     * @param $type
     * @param $page
     * @param $per_page
     * @return array
     */
    public static function getUserFocus($uid,$type=[],$page=1,$per_page=10)
    {
        $where['status'] = 1;
        $where['fans_uid'] = intval($uid);
        $follow_list = db('users_follow')->where($where)->column('friend_uid');
        return LogHelper::getActionLogList($type,$follow_list,$uid,$page,$per_page);
    }


    /**
     * 设置错误信息
     * @param $error
     * @return mixed
     */
    public static function setError($error)
    {
        return self::$error = $error;
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public static function getError() {
        return self::$error;
    }
}