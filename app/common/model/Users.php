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

namespace app\common\model;
use app\common\library\helper\IpHelper;
use app\common\library\helper\LogHelper;
use app\common\library\helper\MailHelper;
use app\ask\model\Question;
use app\ask\model\Topic;
use app\ask\model\Vote;
use Overtrue\Pinyin\Pinyin;
use think\Exception;
use think\facade\Request;

/**
 * 公用用户模型
 * Class Users
 * @package app\common\model
 */
class Users extends BaseModel
{
	protected $name = 'users';
	public static $moduleName;
	public function __construct(array $data = [])
	{
		parent::__construct($data);
		self::$moduleName = Request::isMobile() ? 'wap' : 'ask';
	}

	public static function getList($where=[],$sort=['sort', 'id' => 'desc'])
    {
        $list = db('users')->where($where)
            ->order($sort)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' => 15,
            ])
            ->toArray();

        foreach ($list['data'] as $key=>$val)
        {
            $group_info = self::getUserGroupInfo($val['uid']);
            $list['data'][$key]['score_group_name'] = $group_info ? $group_info['score_group_name'] : '';
            $list['data'][$key]['power_group_name'] = $group_info ? $group_info['power_group_name'] : '';
            $list['data'][$key]['group_name'] = $group_info ? $group_info['group_name'] :'';
        }
        return $list;
    }

    /**
     * 删除用户
     * @param $uid
     * @param int $realMove 1真实删除
     * @return bool
     */
    public static function removeUser($uid, int $realMove=0)
    {
        $uid = is_array($uid) ? $uid : explode(',',$uid);
        try {
            if($realMove){
                //真实删除用户
                db('users')->whereIn('uid',$uid)->delete();
                //问题
                $question_ids = db('question')->whereIn('uid',$uid)->column('id');
                //删除问题
                db('question')->whereIn('id',$question_ids)->delete();
                //删除问题下的回答
                db('answer')->whereIn('question_id',$question_ids)->delete();
                //删除该用户的回答
                db('answer')->whereIn('uid',$uid)->delete();
                //删除操作记录
                db('action_log')->whereIn('uid',$uid)->delete();
                //删除关注好友记录
                db('users_follow')->whereRaw('fans_uid IN('.implode(',',$uid).') OR friend_uid IN('.implode(',',$uid).')')->delete();
                //删除用户的文章
                $article_ids = db('article')->whereIn('uid',$uid)->column('id');
                db('article')->whereIn('id',$article_ids)->delete();
                db('article_comment')->whereIn('article_id',$article_ids)->delete();
                db('article_comment')->whereIn('uid',$uid)->delete();

                //删除首页数据
                db('post_relation')->whereIn('uid',$uid)->delete();
            }else{
                db('users')->whereIn('uid',$uid)->update(['status'=>0]);
                //问题
                $question_ids = db('question')->whereIn('uid',$uid)->column('id');
                //删除问题
                db('question')->whereIn('id',$question_ids)->update(['status'=>0]);
                //删除问题下的回答
                db('answer')->whereIn('question_id',$question_ids)->update(['status'=>0]);
                //删除该用户的回答
                db('answer')->whereIn('uid',$uid)->update(['status'=>0]);
                //删除操作记录
                db('action_log')->whereIn('uid',$uid)->update(['status'=>0]);
                //删除用户的文章
                $article_ids = db('article')->whereIn('uid',$uid)->column('id');
                db('article')->whereIn('id',$article_ids)->update(['status'=>0]);
                db('article_comment')->whereIn('article_id',$article_ids)->update(['status'=>0]);
                db('article_comment')->whereIn('uid',$uid)->update(['status'=>0]);

                //删除首页数据
                db('post_relation')->whereIn('uid',$uid)->update(['status'=>0]);
            }
            return true;
        }
        catch (\Exception $e)
        {
            self::setError($e->getMessage());
            return false;
        }


        db('users')->whereIn('uid',$uid)->update(['status'=>0]);
    }

	//判断用户登录，并返回所有信息，写入session
	public static function getLogin($account, $password)
	{
		$user = self::checkUserExist($account);
		if (!$user) {
			self::setError('用户不存在');
			return false;
		}
		$errorCount = cache('error_count') ? : 0;
		if (!password_verify($password, $user['password']))
		{
		    //限制时长
		    if(cache('error_time'))
            {
                $time = (int)get_setting('password_error_limit_time') - round((time()- (int)cache('error_time'))/60) ;
                self::setError('请等待'.$time.'分钟后重试');
                cache('error_count',null);
            }else{
                ++$errorCount;
                if(get_setting('errors_exceeds_limit_password')<=$errorCount)
                {
                    cache('error_time',time(),['expire'=> (int)get_setting('password_error_limit_time') *60]);
                }else{
                    self::setError('用户密码不正确,您还可继续重试'. (int)(get_setting('errors_exceeds_limit_password') - $errorCount) .'次');
                    cache('error_count',$errorCount);
                }
            }
			return false;
		}

		$user = self::getUserInfo($user['uid']);

		$ip = IpHelper::getRealIp();

		if ($user['status']===1) {
			session('last_login_time', $user['last_login_time']);
			if ($user['last_login_ip']) {
				session('last_login_ip', $user['last_login_ip']);
			} else {
				session('last_login_ip', $ip);
			}
			session('access_time', time());

			$data['last_login_time'] = time();
			$data['last_login_ip'] = $ip;
			self::update($data,['uid'=>$user['uid']]);

			// 检测是否有同一IP的记录，有更新，否则 添加
			$map = array();
			$map[] = ['last_login_ip','=',$ip];
			$online_id = app('db')->name('users_online')->where($map)->value('id');
			$last_url = request()->url();
			$user_agent = request()->server('HTTP_USER_AGENT');

            $data = array();
            $data['uid'] = $user['uid'];
            $data['last_login_time'] = time();
            if (!$online_id) {
				// 插入在线用户表
                $data['last_url'] = $last_url;
				$data['user_agent'] = $user_agent;
				$data['last_login_ip'] = $ip;
                app('db')->name('users_online')->insert($data);
			}else{
				// 更新在线用户表
                $data['last_login_ip'] = $ip;
				$data['last_url'] = $last_url;
				$data['user_agent'] = $user_agent;
                app('db')->name('users_online')->where($map)->save($data);
			}
			unset($user['password']);
			session('login_uid', $user['uid']);
            session('login_user_info',$user);
            //添加行为记录
            LogHelper::addActionLog('user_login', 'users', $user['uid'], $user['uid']);
            //添加积分记录
            LogHelper::addScoreLog('user_login',$user['uid'],'users',$user['uid']);
			return $user;
		}
        if(!$user['status'])
        {
            self::setError('该账号已被管理员删除！');
        }

        if($user['status']===3 && $users_forbidden = db('users_forbidden')->where(['uid'=>$user['uid'],'status'=>1])->find())
        {
            self::setError('该账号已被管理员封禁！封禁原因：'.$users_forbidden['forbidden_reason'].';解封时间：'.date('Y-m-d H:i:s',$users_forbidden['forbidden_reason']));
        }
        self::setError('账号异常');
        return false;
        // 检测用户是否在线
		/*if (!Users::isOnline($user['uid']))
		{
            self::setError('此用户已在其他地方登陆,请' . get_setting('online_check_time') .'分钟后再试！');
			return false;
		}*/
	}

	/**
	 * 新用户注册
	 * @param string $account
	 * @param string $password
	 * @param array $extend
	 * @return bool|int|string
	 */
	public static function registerUser(string $account, string $password, array $extend = [])
	{
		if (!$account || !$password) {
			return false;
		}

		if (self::checkUserExist($account)) {
			return false;
		}

		$data = array();
		$data['create_time'] = time();
		$data['update_time'] = time();
		$data['user_name'] = $account;
		$data['password'] = password_hash($password, 1);
		$data['is_first_login'] = 1;
		$data = $extend ? array_merge($data, $extend) : $data;
		$data['nick_name'] = $data['nick_name'] ?? $account;
        $pinyin = new Pinyin();
        $url_token = $pinyin->permalink($account);
		$data['url_token'] = $url_token;
		$register_valid_type = get_setting('register_valid_type');
		$group_id = !$register_valid_type ? 3 : 4;
		if (isset($data['group_id'])) {
            $group_id = intval($data['group_id']);
			unset($data['group_id']);
		}

		$uid = db('users')->strict(false)->insertGetId($data);

		//用户和用户组关联
		$userGroupAccess = array(
			'uid'=>$uid,
			'group_id'=>$group_id,
			'score_group_id'=>1,
            'power_group_id'=>1,
			'create_time'=>time(),
			'update_time'=>time()
		);

        db('auth_group_access')->strict(false)->insert($userGroupAccess);

        //用户通知配置
        db('users_setting')->insert([
            'uid'=>$uid,
            'email_setting'=>implode(',',array_keys(config('email'))),
            'inbox_setting'=>'all',
            'notify_setting'=>implode(',',array_keys(config('notify'))),
            'create_time'=>time()
        ]);

        //添加积分记录
        LogHelper::addScoreLog('user_register',$uid,'users',$uid);
		return $uid;
	}

	//获取用户信息
	public static function getUserInfo($uid,$field='*')
	{
	    static $staticUserInfo=[];
		if (!$uid && !$uid=session('login_uid')) {
			return false;
		}

		if(!empty($staticUserInfo) && isset($staticUserInfo[$uid]))
        {
            return $staticUserInfo[$uid];
        }
		$user_info = db('users')->field($field)->find($uid);

		if(!$user_info)
        {
            return false;
        }
        $user_group_info = self::getUserGroupInfo($uid);
        $user_info['is_online'] = self::isOnline($uid);
        $user_info['avatar'] = $user_info['avatar'] ? : '/static/common/image/default-avatar.svg';
        $user_info['url'] = get_user_url($user_info['uid']);
        $user_info['name'] =$user_info[get_setting('show_name')];
        if($user_group_info)
        {
            $user_info = array_merge($user_info,$user_group_info);
        }else{
            $user_group_info['group_icon'] = '/static/common/image/group/1.png';
        }
        $staticUserInfo[$uid] = $user_info;
		return $user_info;
	}

    /**
     * 根据用户ids获取用户信息
     * @param $ids
     * @return array|false|false[]
     */
	public static function getUserInfoByIds($ids)
	{
		if (!is_array($ids) || count($ids) == 0) {
            return false;
        }
		$ids = array_unique($ids);
		if ((count($ids) === 1) && $one_user_info = self::getUserInfo(end($ids))) {
            return array(
                end($ids) => $one_user_info
            );
        }

		$user_info = db('users')->where(['status'=>1])->withoutField('password')->whereIn('uid',implode(',', $ids))->select()->toArray();

		$data = array();

		if ($user_info)
		{
			foreach ($user_info as $key => $val)
			{
				$data[$val['uid']] = $val;
				$data[$val['uid']]['is_online'] = self::isOnline($val['uid']);
				$data[$val['uid']]['avatar'] = $data[$val['uid']]['avatar'] ?: '/static/common/image/default-avatar.svg';
				$data[$val['uid']]['url'] = get_user_url($val['uid']);
				$data[$val['uid']]['name'] = $data[$val['uid']][get_setting('show_name')];
			}
		}
		return $data;
	}

	//获得用户权限组信息
	public static function getUserGroupInfo($uid)
    {
		static $groups = [];
		if (isset($groups[$uid])) {
			return $groups[$uid];
		}
		// 执行查询
		$map[] = [ 'auth_group.status', '=', 1];
		$map[] = [ 'auth_group_access.uid', '=', $uid];

		$user_groups = app('db')->view('auth_group_access', 'uid,group_id,score_group_id,power_group_id')
			->view('auth_group', 'title as group_name,permission,rules', "auth_group_access.group_id=auth_group.id", 'LEFT')
			->view('users_score_group', 'title as score_group_name,permission as score_permission,group_icon as score_group_icon', "auth_group_access.score_group_id=users_score_group.id", 'LEFT')
            ->view('users_power_group', 'title as power_group_name,permission as power_permission,group_icon as power_group_icon,power_factor', "auth_group_access.power_group_id=users_power_group.id", 'LEFT')
            ->where($map)
			->find();


		if($user_groups)
		{
			$user_groups['permission'] = json_decode($user_groups['permission'],true);
            $user_groups['score_permission'] = $user_groups['score_permission'] ? json_decode($user_groups['score_permission'],true) : [];
            $user_groups['power_permission'] = $user_groups['power_permission'] ? json_decode($user_groups['power_permission'],true) : [];
            $user_groups['group_icon'] = get_setting('user_group_factor')=='score' ? $user_groups['score_group_icon'] : $user_groups['power_group_icon'];
            $user_groups['user_group_name'] = get_setting('user_group_factor')=='score' ? $user_groups['score_group_name'] : $user_groups['power_group_name'];
            $user_groups['permission'] = get_setting('user_group_factor')=='score' ? array_merge($user_groups['permission'],$user_groups['score_permission']) : array_merge($user_groups['permission'],$user_groups['power_permission']);
		}

        $groups[$uid] = $user_groups ?: [];
		return $user_groups;
	}

	//更新用户字段
	public static function updateUserFiled($uid,$data)
    {
		if(isset($data['password']) && $data['password'])
		{
			$data['password'] = password_hash($data['password'], 1);
		}
        $pinyin = new Pinyin();
		if(isset($data['url_token']) && $data['url_token'])
        {
            $data['url_token'] = $pinyin->permalink($data['url_token']);
        }

		if(!db('users')->where(['uid'=>$uid])->update($data))
		{
			return false;
		}

		//更新缓存信息
		if ($uid === session('login_uid')) {
			$user =self::getUserInfo($uid);
			session('login_user_info', $user);
		}
		return true;
	}

	//检查用户名是否已存在
	public static function checkUserExist($account,$field='*') {
		$where = $whereOr = array();
		if (MailHelper::isEmail($account)) {
			$where['email'] = $account;
		} else if (preg_match("/^1[34578]\d{9}$/", $account)) {
			$where['mobile'] = $account;
		} else {
            $pinyin = new Pinyin();
            $url_token = $pinyin->permalink($account);
			$where['user_name'] = $account;
            $whereOr['url_token'] = $url_token;
		}
        return db('users')->where($where)->whereOr($whereOr)->field($field)->find();
	}

	/**
	 * 解析内容中的用户名
	 * @param $content
	 * @param bool $with_user
	 * @param bool $to_uid
	 * @return array
     */
	public static function parseAtUser($content, bool $with_user = false, bool $to_uid = false): array
    {
		$result = $all_users = array();
		$content_uid = '';
		preg_match_all('/@([^@,:\s,]+)/i', strip_tags($content), $result);
		if (is_array($result[1]))
		{
			$match_name = array();
			foreach ($result[1] as $user_name)
			{
				if (in_array($user_name, $match_name, true))
				{
					continue;
				}
				$match_name[] = $user_name;
			}
			$match_name = array_unique($match_name);
			arsort($match_name);
			$content_uid = $content;
			foreach ($match_name as $user_name)
			{
				$user_info = self::checkUserExist($user_name);
				if ($user_info)
				{
					$content = str_replace('@' . $user_name, '<a href="'.url('member/index/index',['uid'=>$user_info['uid']])  . '" class="uk-user-name text-primary mr-1">@' . $user_info['nick_name'] . '</a>', $content);
					if ($to_uid)
					{
						$content_uid = str_replace('@' . $user_name, '@' . $user_info['uid'], $content_uid);
					}
					if ($with_user)
					{
						$all_users[] = $user_info['uid'];
					}
				}
			}
		}
		return [$content,$all_users,$content_uid];
	}

	//获取用户列表
	public static function getUserList($sort,$param,$current_page,$per_page,$uid=0): array
    {
        $where = 'status=1';
		switch ($sort)
		{
            case 'power':
                $order['power'] = 'desc';
                break;

            case 'score':
                $order['score'] = 'desc';
                break;

            case 'verify':
                $where .= ' AND verified IS NOT NULL';
                break;

            case 'hot':
            default:
				break;
		}
        $order['create_time'] = 'desc';

		$list = db('users')->whereRaw($where)->withoutField('password')->order($order)->paginate([
			'list_rows' => $per_page ?: 10, //每页数量
			'var_page' => 'page', //分页变量
			'page' => $current_page ?: 1, //当前页面
			'query'=>$param
		]);

		$total = db('users')->where($where)->count();
		$page = $list->render();
		$user = $list->all();
		$result = array();
		foreach ($user as $key =>$value)
		{
            $result[$value['uid']] = $value;
		    $userGroup = self::getUserGroupInfo($value['uid']);
		    if($userGroup)
            {
                $result[$value['uid']] = array_merge($value,$userGroup);
            }else{
                $result[$value['uid']]['group_icon'] = '/static/common/image/group/1.png';
                $result[$value['uid']]['user_group_name'] = '未验证会员组';
            }
			$result[$value['uid']]['is_online'] = self::isOnline($value['uid']);
			$result[$value['uid']]['has_focus'] = self::checkFocus($uid,$value['uid']);
			$result[$value['uid']]['avatar'] = $value['avatar'] ?: '/static/common/image/default-avatar.svg';
			$result[$value['uid']]['url'] = get_user_url($value['uid']);
			$result[$value['uid']]['name'] = $value[get_setting('show_name')];
		}
		return ['list'=>$result,'page'=>$page,'total'=>ceil($total/$per_page)];
	}

    /**
     * 是否关注
     * @param $uid
     * @param $target_uid
     * @return mixed
     */
	public static function checkFocus($uid,$target_uid)
    {
		return db('users_follow')->where(['fans_uid'=>$uid,'friend_uid'=>$target_uid])->value('id');
	}

	//获取用户发布内容动态
	public static function getUserPostList($uid,$type,$page=1,$per_page=10,$all=0)
	{
		$where = array();
		$where[]=['uid','=',$uid];
		$where[] = $all ? ['status','>',0] : ['status','=',1];
		$list =app('db')->name($type)->where($where)->order(['create_time'=>'desc'])->paginate(
			[
				'list_rows'=> $per_page,
				'page' => (string)$page,
				'query'=>request()->param()
			]
		);

		$pageVar = $list->render();
		$list = $list->all();
		$result_list = $data_list_uid = array();
		if(!$list){
			return false;
		}
		$question_ids = $question_infos = [];
		if($type=='answer')
        {
            $question_ids = array_column($list,'question_id');
        }

		if($question_ids)
        {
            $question_infos = Question::getQuestionByIds($question_ids);
        }

		foreach ($list as $key=>$val)
		{
			$data_list_uid[$val['uid']] = $val['uid'];
			$result_list[$key] = $val;
			switch ($type)
			{
				case 'question':
					$result_list[$key]['item_type'] = 'question';
					$result_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'question',$uid);
					$result_list[$key]['topics'] = Topic::getTopicByItemType('question',$val['id']);
					$result_list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($val['detail'])),0,150);
					break;

				case 'article':
					$result_list[$key]['item_type'] = 'article';
					$result_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'question',$uid);
					$result_list[$key]['topics'] = Topic::getTopicByItemType('question',$val['id']);
					$result_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($val['message'])),0,150);
					break;

				case 'answer':
					$result_list[$key]['item_type'] = 'answer';
                    $result_list[$key]['question_info'] = $question_infos[$val['question_id']];
					$result_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$uid);
					$result_list[$key]['content'] = str_cut(strip_tags(htmlspecialchars_decode($val['content'])),0,150);
					break;
			}
		}

		$users_info = self::getUserInfoByIds(array_unique($data_list_uid));

		if(!$result_list)
		{
			return false;
		}

		foreach ($result_list as $key=>$val){
			$result_list[$key]['user_info'] = $users_info[$val['uid']];
		}

		return ['list'=>$result_list,'page'=>$pageVar];
	}

	//检测用户是否在线
	public static function isOnline($uid): bool
    {
		$map[] = array('uid', '=', $uid);
		$list = db('users_online')->where($map)->find();

		if(!$list) {
            return false;
        }

		if ($list['last_login_ip'] === IpHelper::getRealIp()) {
			return true;
		}

		if ($list['last_login_time'] < time() - (int)get_setting('online_check_time') * 60) {
			return true;
		}
		return false;
	}

    /**
     * 更新用户通知未读数
     * @param $recipient_uid
     * @return bool
     */
	public static function updateNotifyUnread($recipient_uid)
    {
		$unread_num = db('notify')->where(['recipient_uid'=>(int)$recipient_uid,'read_flag'=>0])->count();
		return self::updateUserFiled($recipient_uid,['notify_unread'=>$unread_num]);
	}

    /**
     * 更新私信数
     * @param $uid
     * @return mixed
     */
	public static function updateInboxUnread($uid)
    {
        $sender_unread_num = db('inbox_dialog')->where(['sender_uid'=> (int)$uid])->sum('sender_unread');
        $recipient_unread_num = db('inbox_dialog')->where(['recipient_uid'=> (int)$uid])->sum('recipient_unread');
        $unread_num = (int)($sender_unread_num + $recipient_unread_num);
        return self::updateUserFiled($uid,['inbox_unread'=>$unread_num]);
    }

    /**
     * 获取热门用户
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $per_page
     * @param int $page
     * @return mixed
     */
    public static function getHotUsers($uid=0,$where=[], $order=[], $per_page=5,$page=1)
    {
        $where = !empty($where) ? $where : ['status'=>1];
        $order = !empty($order) ? $order : ['agree_count'=>'DESC','answer_count'=>'DESC'];
        $list = db('users')
            ->where([['uid','<>',$uid]])
            ->where($where)
            ->order($order)
            ->orderRaw('RAND()')
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'query'=>request()->param()
                ]
            )->toArray();
        foreach ($list['data'] as $key=>$val)
        {
            $list['data'][$key]['url'] = get_user_url($val['uid']);
            $list['data'][$key]['is_focus'] = db('users_follow')->where(['fans_uid'=> (int)$val['uid']])->value('id');
        }
        return $list;
    }

    /**
     * 更新用户积分组
     * @param $uid
     * @return mixed
     */
    public static function updateUsersGroup($uid)
    {
        //积分组
        $user_score = db('users')->where(['uid'=> (int)$uid])->value('score');
        $user_score = $user_score?:0;
        $user_group_id = db('users_score_group')->where([
            ['min_score','<=',$user_score],
            ['max_score','>',$user_score]
        ])->value('id');

        if($user_group_id)
        {
            return db('auth_group_access')->where('uid', (int)$uid)->update(['score_group_id'=>$user_group_id]);
        }
        return false;
    }

    /**
     * 更新用户威望组
     * @param $uid
     * @return false
     */
    public static function updateUsersPowerGroup($uid): bool
    {
        $user_power = db('users')->where(['uid'=> (int)$uid,'status'=>1])->value('power');

        $user_group_info = db('users_power_group')->where([
            ['min_power','<=',$user_power],
            ['max_power','>',$user_power]
        ])->field('id,permission')->find();

        if($user_group_info)
        {
            $permission = json_decode($user_group_info['permission'],true);
            if(isset($permission['available_invite_count']))
            {
                self::updateUserFiled($uid,['available_invite_count'=>$permission['available_invite_count']]);
            }
            return db('auth_group_access')->where('uid', (int)$uid)->update(['power_group_id'=>$user_group_info['id']]);
        }
        return false;
    }

    /**
     * 更新主页访问量
     * @param $user_id
     * @param int $uid
     * @return bool
     */
    public static function updateQuestionViews($user_id, int $uid=0): bool
    {
        $cache_key = md5('cache_user_'.$user_id.'_'.$uid);
        $cache_result = cache($cache_key);
        if($cache_result) {
            return true;
        }
        cache($cache_key,$cache_key,['expire'=>60]);
        return db('users')->where(['uid'=>$user_id])->inc('views_count')->update();
    }
}