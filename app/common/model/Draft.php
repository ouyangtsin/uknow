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

use app\ask\model\Topic;
use think\Model;

class Draft extends BaseModel
{
	protected $name = 'draft';

	/**
	 * 保存草稿
	 * @param $uid
	 * @param $item_type
	 * @param $data
	 * @param int $item_id
	 * @return Draft|bool|Model
	 */
	public static function saveDraft($uid,$item_type,$data,$item_id=0)
	{
		if(!$item_type || !$data) return false;
		$insertData = array(
			'item_id'=>$item_id,
			'item_type' =>$item_type,
			'data'=>json_encode($data),
			'uid'=>$uid
		);
		if($draft_id = self::where(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$uid])->value('id'))
		{
			$insertData['update_time']=time();
			$result = self::update($insertData,['id'=>$draft_id]);
		}else{
			$insertData['create_time']=time();
			$result =self::create($insertData);
		}
		return $result ;
	}

    /**
     * 获取草稿列表
     * @param $uid
     * @param $item_type
     * @param int $page
     * @param int $per_page
     * @param string $pjax
     * @return mixed
     */
	public static function getDraftByType($uid,$item_type,$page=1,$per_page=10,$pjax='uk-index-main'): array
    {
        $where=[
            ['uid','=',intval($uid)],
            ['item_type','=',$item_type],
        ];

        $draft_list = db('draft')->where($where)->paginate([
            'list_rows'=> $per_page,
            'page' => $page,
            'query'=>request()->param(),
            'pjax'=>$pjax
        ]);
        $pageVar = $draft_list->render();
        $list = $draft_list->toArray();

        $result = $list['data'];
        
        foreach ($result as $key=>$val)
        {
            $data = json_decode($val['data'],true);
            if(isset($data['detail']))
            {
                $data['detail'] = str_cut(strip_tags(htmlspecialchars_decode($data['detail'])),0,150);
            }

            if(isset($data['message']))
            {
                $data['message'] = str_cut(strip_tags(htmlspecialchars_decode($data['message'])),0,100);
            }
            $result[$key]['data']=$data;

            if(isset($result[$key]['topics']) && !empty($result[$key]['topics']))
            {
                $result[$key]['topics'] = Topic::getTopicByIds($data['topics']);
            }
        }

        return ['list'=>$result,'page'=>$pageVar,'total'=>$list['total'],'last_page'=>$list['last_page']];
    }

    /**
     * 获取草稿内容
     * @param $uid
     * @param $item_type
     * @param int $item_id
     * @return mixed
     */
    public static function getDraftByItemID($uid,$item_type,$item_id=0)
    {
        $where=['uid'=>intval($uid),'item_type'=>$item_type,'item_id'=>$item_id];
        $draft = db('draft')->where($where)->find();
        if($draft)
        {
            $draft['data']=json_decode($draft['data'],true);
        }
        return $draft;
    }

    public static function deleteDraftByItemID($uid,$item_type,$item_id=0)
    {
        $where=['uid'=>intval($uid),'item_type'=>$item_type,'item_id'=>$item_id];
        return db('draft')->where($where)->delete();
    }
}