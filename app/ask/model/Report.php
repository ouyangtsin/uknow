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

use think\Model;

class Report extends Model
{
    /**
     * 保存举报信息
     * @param $item_id
     * @param $item_type
     * @param $report_type
     * @param $reason
     * @param $uid
     * @return array
     */
	public static function saveReport($item_id,$item_type,$report_type,$reason,$uid)
	{
		switch ($item_type)
		{
			case 'question':
				$url = (string) url('ask/question/detail', ['id' => $item_id]);
				break;

			case 'answer':
				if (strpos($item_id, '-')) {
					$item_id = explode('-', $item_id);
					$item_id = $item_id[1];
					$url = (string) url('question/detail', ['id' => $item_id[0]]) . '#comment-id-' . $item_id[1];
				}
				break;

			case 'article':
				$url = (string) url('ask/article/detail', ['id' => $item_id]);
				break;

			case 'article_comment':
				break;
			default:
				$url = '';
				break;
		}

		if (self::getReportInfo($item_id,$item_type,$uid))
		{
			return ['code' => 0, 'msg' => '您已经举报过了！'];
		}

		self::create([
			'item_id'=>$item_id,
			'item_type'=>$item_type,
			'report_type'=>$report_type,
			'reason'=>$reason,
			'url'=>$url,
			'uid'=>$uid,
			'create_time'=>time()
		]);
		return ['code' => 1, 'msg' => '举报成功'];
	}

	//获取举报信息
	public static function getReportInfo($item_id,$item_type,$uid)
	{
		return db('report')->where(['item_id' => $item_id, 'uid' => $uid, 'item_type' => $item_type])->find();
	}
	
}