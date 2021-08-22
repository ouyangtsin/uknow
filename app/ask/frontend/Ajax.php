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
use app\common\logic\common\FocusLogic;
use app\common\model\Draft;
use app\common\model\Favorite;
use app\common\model\Inbox as InboxModel;
use app\common\model\Users;
use app\common\model\Verify;
use app\ask\model\Report;
use app\ask\model\Topic;
use app\ask\model\Vote;
use app\ask\model\Article;

class Ajax extends Frontend
{
	//投票操作
	public function set_vote()
    {
		$item_id = $this->request->post('item_id');
		$item_type = $this->request->post('item_type');
		$vote_value = intval($this->request->post('vote_value'));
		$result = Vote::saveVote($this->user_id, $item_id, $item_type, $vote_value);
		if (!$result) {
			$this->result([], 0, Vote::getError());
		}
		$this->result($result, 1, '操作成功');
	}

    /**
     * 获取用户信息
     */
	public function get_user_info()
    {
		$uid = $this->request->param('uid');
		$user_info = Users::getUserInfo($uid);
        $user_info['is_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user_info['uid']);
		$this->result($user_info);
	}

    /**
     * 获取话题信息
     */
    public function get_topic_info()
    {
        $id = $this->request->param('id');
        $topic_info = db('topic')->where('id',intval($id))->find();
        $topic_info['is_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'topic',$topic_info['id']);
        $topic_info['description'] = $topic_info['description'] ? str_cut(strip_tags(htmlspecialchars_decode($topic_info['description'])), 0, 45) : '暂无话题简介';
        $topic_info['url'] = (string)url('ask/topic/detail',['id'=>$id]);
        $topic_info['pic'] = $topic_info['pic'] ? : '/static/common/image/topic.svg';
        $this->result($topic_info);
    }

	/**
	 * 保存草稿
	 */
	public function save_draft()
    {
		if ($this->request->isPost()) {
			$item_id = $this->request->post('item_id');
			$item_type = $this->request->post('item_type');
			$data = $this->request->post('data');
			if (empty($data) || !$data['title']) {
				$this->error('保存草稿失败');
			}
            $data['is_anonymous'] = isset($data['is_anonymous']) ? intval( $data['is_anonymous']) : 0;
			unset($data['__token__']);
			if (Draft::saveDraft($this->user_id, $item_type, $data, $item_id)) {
				$this->success('保存草稿成功');
			}
			$this->error('保存草稿失败');
		}
	}

	/**
	 * 收藏
	 * @param $item_type
	 * @param $item_id
	 * @return mixed
	 */
	public function favorite($item_type, $item_id) {
		if ($this->request->isPost()) {
			$tag_id = $this->request->param('tag_id');
			if ($return = Favorite::saveFavorite($this->user_id, $tag_id, $item_id, $item_type)) {
				$this->result($return, 1);
			}
			$this->result([], 0);
		}

		$favorite_list = Favorite::getFavoriteTags($this->user_id);
		foreach ($favorite_list['list'] as $key => $value) {
			$favorite_list['list'][$key]['is_favorite'] = Favorite::where(['item_type' => $item_type, 'item_id' => (int) $item_id, 'tag_id' => $value['id'], 'uid' => $this->user_id])->value('id');
		}
		$this->assign($favorite_list);
		$this->assign('item_type', $item_type);
		$this->assign('item_id', $item_id);
		return $this->fetch();
	}

	/**
	 * 举报
	 * @param $item_type
	 * @param $item_id
	 * @return mixed
	 */
	public function report($item_type, $item_id) {
		if ($this->request->isPost()) {
			$reason = $this->request->post('reason');
			$report_type = $this->request->post('report_type');
			$result = Report::saveReport($item_id, $item_type, $report_type, $reason, $this->user_id);
			$this->result([], $result['code'], $result['msg']);
		}
		$this->assign('item_id', $item_id);
		$this->assign('item_type', $item_type);
		$this->assign('report_category', $this->settings['report_category']);
		return $this->fetch();
	}

	/**
	 * 话题编辑
	 * @param $item_type
	 * @param int $item_id
	 * @return mixed
	 */
	public function topic($item_type, $item_id = 0)
    {
		if ($this->request->isPost()) {
			$topics = $this->request->post('tags');
			if (empty($topics)) {
				$this->error('请至少设置一个话题');
			}

			if (count($topics) > 5) {
				$this->error('最多设置五个话题');
			}

            Topic::updateRelation($item_type, $item_id, $topics, $this->user_id);
            $list = Topic::getTopicByIds($topics);
            $this->result(['list' => $list, 'total' => count($list)], 1, '保存成功');
		}
		$topic_list = Topic::getTopics($item_type, $item_id);
		$this->assign('topic_list', $topic_list);
		$this->assign('item_type', $item_type);
		$this->assign('item_id', $item_id);
		return $this->fetch();
	}

	/**
	 * 更新关注
	 */
	public function update_focus(): void{
		$item_id = $this->request->post('id');
		$item_type = $this->request->post('type');
		if (!$data = FocusLogic::updateFocusAction($item_id, $item_type, $this->user_id)) {
			$this->result([], 0, FocusLogic::getError());
		}

		$this->result($data, 1, '操作成功');
	}

    /**
     * 锁定话题
     */
	public function lock()
    {
        $id = $this->request->param('id');
        if($this->user_id && ($this->user_info['group_id']===1 || $this->user_info['group_id']===2))
        {
            if(Topic::lockTopic((int)$id)){
        		$this->success('操作成功');
            }
        }
        $this->error('您没有锁定话题权限');
    }

    /**
     * 私信对话记录ajax请求
     */
    public function dialog()
    {
        $user_name = $this->request->param('recipient_uid','');
        $page = $this->request->param('page',1);
        $recipient_uid = db('users')->where('user_name',$user_name)->value('uid');
        $dialog_id = InboxModel::getDialogByUser($this->user_id, $recipient_uid);
        $list = $dialog_id ? InboxModel::getMessageByDialogId($dialog_id,$this->user_id,intval($page)) : [];
        if($this->user_info['inbox_unread'])
        {
            InboxModel::updateRead($dialog_id,$this->user_id);
        }
        $this->assign($list);
        return $this->fetch();
    }

    /*私信弹窗*/
    public function inbox()
    {
        $user_name = $this->request->param('user_name','');
        $this->assign(['user_name'=>$user_name]);
        return $this->fetch();
    }

    /**
     * 热门搜索
     */
    public function hot_search()
    {
        return $this->fetch();
    }

    /**
     * 发送短信
     */
    public function sms()
    {
        $mobile = $this->request->param('mobile');
        $result = hook('sms',['mobile'=>$mobile]);
        return json_decode($result,true);
    }

    /**
     * 认证类型
     * @return mixed
     */
    public function verify_type()
    {
        $type = $this->request->param('type');
        $where = ['verify_type'=>$type];
        $info = db('users_verify')->where(['uid'=>intval($this->user_id)])->find();
        $result = $info ? json_decode($info['data'],true) : [];
        $result['type'] = $info ? $info['type'] : $type;
        $data = array(
            'keyList' => Verify::getConfigList($where),
            'info'=>$result
        );
        $this->assign('verify_info',$info);
        $this->assign($data);
        return $this->fetch();
    }

    /**
     * 不感兴趣
     */
    public function uninterested()
    {
        if(!$this->user_id){
            $this->error('请先登录');
        }

        $item_id = $this->request->post('id');
        $item_type = $this->request->post('type');
        if(db('uninterested')->where(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$this->user_id])->value('id'))
        {
            $this->error('您已进行过此操作');
        }

        if(db('uninterested')->insert(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$this->user_id,'create_time'=>time()]))
        {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }
}