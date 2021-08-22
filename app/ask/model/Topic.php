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


namespace app\ask\model;
use app\common\library\helper\ArrayHelper;
use app\common\logic\common\FocusLogic;
use app\common\model\BaseModel;
use app\common\model\PostRelation;
use app\common\model\Users;
use Overtrue\Pinyin\Pinyin;
use think\facade\Db;
use tools\Tree;
use WordAnalysis\Analysis;

/**
 * 话题模型类
 * Class Topic
 * @package app\ask\model
 */
class Topic extends BaseModel
{
	//模糊搜索话题列表
    public static function getTopic($where,$page,$per_page)
    {
		return db('topic')->where($where)->order('discuss desc')->page($page,$per_page)->select()->toarray();
	}

	//ajax话题列表
	public static function getAjaxTopicList($where,$uid,$page,$per_page=10): array
    {
		$list = db('topic')->where($where)->order('discuss desc')->paginate([
			'list_rows'=> $per_page,
			'page' => $page,
			'query'=>request()->param()
		]);
		$total = db('topic')->where($where)->count();
		$pageVar = $list->render();
		$list = $list->all();
		foreach ($list as $key=>$val)
		{
			$list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'topic',$val['id']);
		}
		return ['list'=>$list,'page'=>$pageVar,'total'=>ceil($total/$per_page)];
	}

	//获取话题列表
	public static function getTopicByItemType($item_type,$item_id)
    {
		$ids = db('topic_relation')->where(['item_type' => $item_type, 'item_id' => $item_id])->column('topic_id');
		if (empty($ids)) {
			return false;
		}
        return db('topic')->whereIn('id', $ids)->column('id,title');
	}

	//添加话题
	public static function saveTopic($topic_title, $uid = null, $auto_create = true, $topic_description = null)
	{
		if(is_numeric($topic_title))
		{
			$topic_id = $topic_title;
		}else{
			$topic_title = str_replace(array('-', '/'), '_', $topic_title);
			$topic_id = db('topic')->where(['title' => $topic_title])->value('id');
		}

		if (!$topic_id AND $auto_create)
		{
            $pinyin = new Pinyin();
            $url_token = $pinyin->permalink($topic_title);
			$topic_id = db('topic')->insertGetId([
				'title' => htmlspecialchars($topic_title),
				'description' => $topic_description ? htmlspecialchars($topic_description) : '',
				'seo_title'=>htmlspecialchars($topic_title),
				'seo_keywords'=> $topic_description ? Analysis::getKeywords(htmlspecialchars($topic_description), 5) : '',
				'seo_description'=>$topic_description ? str_cut(strip_tags($topic_description),0,150) : '',
				'url_token'=>$url_token,
				'lock' => 0,
				'create_time' => time(),
			]);
			if ($uid) {
				self::addFocusTopic($uid, $topic_id);
				self::save_log(['uid'=>$uid,'type'=>1, 'topic_id'=>$topic_id,'topic_title'=>$topic_title]);
			}
		} else {
			self::updateDiscuss($topic_id);
			// if ($uid) {
			// 	self::save_log(['uid'=>$uid,'type'=>2, 'topic_id'=>$topic_id,'topic_title'=>$topic_title]);
			// }
		}
		return $topic_id;
	}

	//更新话题
	public static function updateTopic($data,$topic_id): Topic
    {
		$data['seo_title'] = $data['seo_title'] ?: $data['title'];
		$data['seo_keywords'] = $data['seo_keywords'] ?: Analysis::getKeywords(htmlspecialchars($data['description']), 5);
		$data['seo_description'] = $data['seo_description'] ?: str_cut(strip_tags($data['description']), 0, 150);

        $pinyin = new Pinyin();
        $data['url_token'] = $pinyin->permalink($data['title']);
		self::save_log(['uid'=>$data['uid'],'type'=>2, 'topic_id'=>$topic_id,'topic_title'=>$data['title']]);
		return self::update($data,['id'=>$topic_id]);
	}

	//添加话题关注
	public static function addFocusTopic($uid, $topic_id)
	{
		$focus_id = db('topic_focus')->where([['uid','=',intval($uid)], ['topic_id','=',intval($topic_id)]])->find();
		//TODO 事务处理方式

		if (!$focus_id) {
			$res = db('topic_focus')->insert(array(
				"topic_id" => (int)$topic_id,
				"uid" => (int)$uid,
				"create_time" => time(),
			));
			if ($res) {
				db('topic')->where(['id' => $topic_id])->inc('focus')->update();
			}
		} else if (db('topic_focus')->where(['id' => $focus_id])->delete()) {
            db('topic')->where(['id' => $topic_id])->dec('focus')->update();
        }

		// 更新个人计数
		$focus_count = db('topic_focus')->where(['uid' => (int)$uid])->count();
		Users::updateUserFiled($uid, (array(
			'topic_focus_count' => $focus_count,
		)));
		return $focus_id;
	}

	/**
	 * 更新话题讨论数
	 * @param $topic_id
	 * @return Topic|bool
	 */
	public static function updateDiscuss($topic_id) {
		if (!$topic_id) {
			return false;
		}
		$discuss_count = db('topic_relation')->where([
			'status' =>1,
			'topic_id' => (int)$topic_id,
		])->count();

		$discuss_week_count = db('topic_relation')->where([
			['create_time', '>', time() - 604800],
			['topic_id','=', (int)$topic_id],
			['status','=',1],
		])->count();

		$discuss_month_count = db('topic_relation')->where([
			['create_time', '>', time() - 2592000],
			['topic_id','=', (int)$topic_id],
			['status','=',1],
		])->count();

		$discuss_update = db('topic_relation')->where(['status' =>1, 'topic_id' => (int)$topic_id])->order('create_time', 'desc')->value('create_time');
		return self::update(array(
			'discuss' => $discuss_count,
			'discuss_week' => $discuss_week_count,
			'discuss_month' => $discuss_month_count,
			'discuss_update' => $discuss_update,
		), ['id' => (int)$topic_id]);
	}

	//获取话题关注用户列表
	public static function getTopicFocusUser($topic_id)
	{
		$focus_uid =  db('topic_focus')->where(['topic_id'=> (int)$topic_id])->column('uid');
        return Users::getUserInfoByIds($focus_uid);
	}

	//获取话题内容的数量及浏览量
	public static function getTopicPostCountResult($topic_id): array
    {
		$where[] = ['topic_id', '=', (int)$topic_id];
		$where[] =['status','=',1];
		$topic_relation = db('topic_relation')->where($where)->select();

		$article_count = $question_count = $question_view_count = $article_view_count = $answer_count = 0;
		$article_ids = $question_ids = array();
		foreach ($topic_relation as $key=>$val)
		{
			switch ($val['item_type'])
			{
				case 'question':
					$question_ids[] = $val['item_id'];
					++$question_count;
					break;
				case 'article':
					$article_ids[] = $val['item_id'];
					++$article_count;
					break;
			}
		}

		$answer_count = Answer::where(['status'=>1])->whereIn('question_id',array_unique($question_ids))->count();
		$question_view_count = Question::where(['status'=>1])->whereIn('id',array_unique($question_ids))->sum('view_count');
		$article_view_count = Article::where(['status'=>1])->whereIn('id',array_unique($article_ids))->sum('view_count');
		return ['article_count'=>$article_count,'question_count'=>$question_count,'answer_count'=>$answer_count,'question_view_count'=>$question_view_count,'article_view_count'=>$article_view_count];
	}

	/**
	 * 添加关联话题
	 * @param $uid
	 * @param $topic_id
	 * @param $item_id
	 * @param $item_type
	 * @return bool|int|string|Db
	 */
	public static function saveTopicRelation($uid, $topic_id, $item_id, $item_type) {
		if (!$topic_id || !$item_id || !$item_type) {
			return false;
		}

		if (!$topic_info = self::getById($topic_id)) {
			return false;
		}

		if ($id = self::checkTopicRelation($topic_id, $item_id, $item_type)) {
			return $id;
		}

		$insert_id = db('topic_relation')->insertGetId(array(
			'topic_id' => (int)$topic_id,
			'item_id' => (int)$item_id,
			'create_time' => time(),
			'uid' => (int)$uid,
			'item_type' => $item_type,
		));
		self::updateDiscuss($topic_id);
		return $insert_id;
	}

    /**
     * 检查是否已有关联记录
     * @param $topic_id
     * @param $item_id
     * @param $item_type
     * @return mixed
     */
	public static function checkTopicRelation($topic_id, $item_id, $item_type)
    {
		$where[] = ['topic_id', '=', (int)$topic_id];
		$where[] = ['item_type', '=', $item_type];
		$where[] = ['item_id', '=', (int)$item_id];
		return db('topic_relation')->where($where)->value('id');
	}

    /**
     * 更新关联话题
     * @param $type
     * @param $item_id
     * @param $topics
     * @param $uid
     * @return mixed
     */
	public static function updateRelation($type,$item_id,$topics, $uid)
    {
        if(!$item_id || !$type) return false;
        db('topic_relation')->where(['item_type' => $type, 'item_id' => $item_id])->delete();
		$_data = [];
        $topics = is_array($topics) ? $topics : explode(',', $topics);
		foreach ($topics as $key => $value) {
			$tmp['topic_id'] = (int)$value;
			$tmp['uid'] = $uid;
			$tmp['create_time'] = time();
			$tmp['item_id'] = (int)$item_id;
			$tmp['item_type'] = $type;
			$_data[] = $tmp;
		}
        return db('topic_relation')->insertAll($_data);
	}

	//根据话题ids获取话题列表
	public static function getTopicByIds($topic_ids,$item_type=null)
	{
		if (!$topic_ids) {
            return false;
        }

		$topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);

		if($item_type)
		{
			$topics = db('topic')->whereIn('id',implode(',', $topic_ids))->where(['item_type'=>$item_type])->select()->toArray();
		}else{
			$topics = db('topic')->whereIn('id',implode(',', $topic_ids))->select()->toArray();
		}

		$result = array();

		foreach ($topics AS $key => $val)
		{
		    $val['url'] = (string)url('ask/topic/detail?id='.$val['id']);
			if (!$val['url_token'])
			{
				$val['url_token'] = urlencode($val['title']);
			}
			$result[$val['id']] = $val;
		}
		return $result;
	}

	//根据关联ids和类型获取话题列表
	public static function getTopicByItemIds($item_ids, $item_type)
	{
		if (!is_array($item_ids) || count($item_ids) == 0) {
            return false;
        }
		$item_topics = db('topic_relation')
			->whereIn('item_id',$item_ids)
			->where(['item_type'=>$item_type])
			->select()
            ->toArray();

		$result = array();
		if (!$item_topics)
		{
			return false;
		}

		$topic_ids = array_column($item_topics,'topic_id');
		$topics_info = self::getTopicByIds($topic_ids);
		foreach ($item_topics AS $key => $val)
		{
			$result[$val['item_id']][] = $topics_info[$val['topic_id']];
		}
		return $result;
	}

	//获取话题内容列表
	public static function getTopicRelationList($uid,$item_type, $topic_id,$page = 1, $per_page = 10)
	{
		$where = $order = array();
		$order['create_time'] = 'desc';
		$where[]=['topic_id','=',$topic_id];
		$where[]=['status','=',1];
		if($item_type === 'hot')
		{

		}else{
			$where[] = ['item_type','=',$item_type];
		}
		$contents = db('topic_relation')->where($where)->page($page,$per_page)->select();
		return PostRelation::processPostList($contents,$uid);
	}

    /**
     * 我关注的话题
     * @param $uid
     * @param int $limit
     * @return false
     */
	public static function getFocusTopicByRand($uid, $limit = 5)
    {
		if (!$uid) {
            return false;
        }

		$focus_topics = db('topic_focus')->where(['uid'=>intval($uid)])->select()->toArray();
		if (!$focus_topics) return false;

		$topic_ids = array_column($focus_topics,'topic_id');

		if(empty($topic_ids)) return false;

        $topic_list = db('topic')->whereRaw("id IN(".implode(',',$topic_ids).")")->orderRaw('RAND()')->limit($limit)->select()->toArray();

        foreach ($topic_list as $k=> $v)
		{
			$topic_list[$k]['question_count'] = db('topic_relation')->where(['topic_id'=>intval($v['id']),'item_type'=>'question'])->count();
			$topic_list[$k]['article_count'] = db('topic_relation')->where(['topic_id'=>intval($v['id']),'item_type'=>'article'])->count();
		}
		return $topic_list;
	}

    /**
     * 获取话题列表
     * @param null $item_type
     * @param int $item_id
     * @return array|false
     */
    public static function getTopics($item_type=null,$item_id=0)
    {
        $topic_list = db('topic')->select()->toArray();
        if(!$topic_list)
        {
            return false;
        }
        
        foreach ($topic_list as $key=>$val)
        {
            $topic_list[$key]['is_checked'] = 0;
            if(self::checkTopicRelation($val['id'], $item_id, $item_type))
            {
                $topic_list[$key]['is_checked'] = 1;
            }
        }
        return Tree::toTree($topic_list);
    }

    /**
     * 获取热门话题
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $per_page
     * @param int $page
     * @return mixed
     */
    public static function getHotTopics($uid=0,$where=[], $order=[], $per_page=5,$page=1)
    {
        $where[] = ['status','=',1];
        if(empty($where))
        {
            $where[] = ['discuss_update','>',time()-30*24*60*60];
        }
        $order = !empty($order) ? $order : ['focus'=>'DESC','discuss_month'=>'DESC'];
        $list = db('topic')
            ->where($where)
            ->order($order)
            //->orderRaw('RAND()')
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'query'=>request()->param()
                ]
            )->toArray();
        foreach ($list['data'] as $key=>$val)
        {
            $list['data'][$key]['is_focus'] = db('topic_focus')->where(['uid'=>intval($uid),'topic_id'=>$val['id']])->value('id');
        }
        return $list;
    }

    /**
     * 删除话题
     * @param $topic_id
     * @return bool|null
     */
    public static function removeTopic($topic_id)
    {
        if(!$topic_id)
        {
            self::setError('参数错误');
            return false;
        }

        if(db('topic')->where(['id'=>$topic_id])->delete())
        {
            //删除话题关联
            db('topic_relation')->where(['topic_id'=>$topic_id])->delete();
            //删除话题关注
            db('topic_focus')->where(['topic_id'=>$topic_id])->delete();
            return true;
        }
        return false;
    }

    /**
     * 锁定话题
     * @param $id
     * @return bool
     */
    public static function lockTopic($id): bool
    {
    	$lock= db('topic')->where(['id'=>$id])->value('lock')?0:1;
    	if(!self::update(['lock'=>$lock],['id'=>$id])){
            return false;
    	}
        return true;
    }

    /**
     * 添加话题记录
     * @param $data
     */
    public static function save_log($data){
    	$arr['create_time']=time();
    	$arr['topic_id']=$data['topic_id'];
    	$arr['type']=$data['type'];
    	$user_info = db('users')->where(['uid'=>$data['uid']])->field('uid,user_name')->find();
    	$arr['user_info']=json_encode(['url'=>url('user/detail',['uid'=>$user_info['uid']]),'title'=>$user_info['user_name']],JSON_UNESCAPED_UNICODE);
    	switch ($data['type']) {
    		case 1:
    			$arr['content']='添加了话题';
    			$arr['item_info']=json_encode(['url'=>url('topic/detail',['id'=>$data['topic_id']]),'title'=>$data['topic_title']],JSON_UNESCAPED_UNICODE);
    			break;
    		case 2:
    			$arr['content']='修改了话题';
    			$arr['item_info']=json_encode(['url'=>url('topic/detail',['id'=>$data['topic_id']]),'title'=>$data['topic_title']],JSON_UNESCAPED_UNICODE);
    			break;
    		case 3:
    			$arr['content']='向话题添加了文章';
    			$arr['item_info']=json_encode(['url'=>url('article/detail',['id'=>$data['article_id']]),'title'=>$data['title']],JSON_UNESCAPED_UNICODE);
    			break;
    		case 4:
    			$arr['content']='向话题添加了问题';
    			$arr['item_info']=json_encode(['url'=>url('question/detail',['id'=>$data['question_id']]),'title'=>$data['title']],JSON_UNESCAPED_UNICODE);
    			break;
    	}
    	$ret=db('topic_logs')->insert($arr);
    }

    /**
     * 获取话题记录
     * @param $id
     * @return mixed
     */
    public static function getLogs($id)
    {
    	return db('topic_logs')->where(['topic_id'=>$id])->select()->toArray();
    }

    /**
     * 根据话题获取相关内容
     * @param $item_id
     * @param $item_type
     * @param $topic_ids
     * @param int $uid
     * @param int $limit
     * @return array|bool
     */
    public static function getRecommendPost($item_id,$item_type,$topic_ids,$uid=0,$limit=10)
    {
        $res = PostRelation::getPostRelationList($uid,null,'hot',$topic_ids,0,1,$limit);
        $contents = $res['list'];
        foreach ($contents as $key=>$val)
        {
            if($val['item_type']==$item_type && $val['id'] == $item_id)
            {
                unset($contents[$key]);
            }
        }
        return $contents;
    }
}