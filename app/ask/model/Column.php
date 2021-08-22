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

class Column extends Model
{
	protected $name = 'column';

    /**
     * 申请保存专栏
     * @param $uid
     * @param $name
     * @param $description
     * @param null $cover
     * @param int $id
     * @param int $verify
     * @return int|string
     */
	public static function applyColumn($uid,$name,$description,$cover=null,$id=0,$verify=0)
	{
		$data = array(
			'uid' => intval($uid),
			'name' => $name,
			'description' => $description,
			'cover' => $cover,
			'create_time' => time(),
			'verify'=>0
		);
        //TODO 给网站管理员发送新的专栏申请通知

        if($id)
        {
            return db('column')->where('id',$id)->update($data);
        }
		return db('column')->insertGetId($data);
	}

    /**
     * 获取专栏列表
     * @param $column_ids
     * @return array|false
     */
    public static function getColumnByIds($column_ids)
    {
        if (!$column_ids) {
            return false;
        }

        $column_ids = is_array($column_ids) ? $column_ids : explode(',',$column_ids);

        $column_infos = db('column')->whereIn('id',implode(',', $column_ids))->select()->toArray();

        $result = array();

        foreach ($column_infos AS $key => $val)
        {
            $result[$val['id']] = $val;
        }
        return $result;
    }

	public static function getColumnByUid($uid)
	{
		return  db('column')->where(['verify'=>1,'uid'=>$uid])->column('id,name');
	}

	//获取专栏列表
	public static function getColumnListByPage($uid,$sort='new',$page=1,$per_page=10): array
    {
		$order = array();
		$where[] = ['verify','=',1];
		switch ($sort)
		{
			case 'new':
				$order['create_time'] = 'DESC';
				break;
			case 'hot':
				$order['view_count'] = 'DESC';
				break;
			case 'recommend':
				$order['view_count'] = 'DESC';
				$where[]=['recommend','=',1];
				break;
		}
		$list =  db('column')->where($where)->order($order)->paginate([
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param()
			]);
		$pageVar = $list->render();
		$list = $list->toArray();
		$total = $list['last_page'];
        foreach ($list['data'] as $key => $value)
        {
            $list['data'][$key]['description'] = str_cut(strip_tags(htmlspecialchars_decode($value['description'])),0,50) ;
            $list['data'][$key]['has_focus'] = 0 ;
            if(db('column_focus')->where(['uid'=>intval($uid),'column_id'=>$value['id']])->value('id'))
            {
                $list['data'][$key]['has_focus'] = 1 ;
            }
        }

		return ['list'=>$list['data'],'page'=>$pageVar,'total'=>$total,'count'=>$list['total']];
	}

    /**
     * 获取我的专栏列表
     * @param $uid
     * @param string $sort
     * @param int $verify
     * @param int $page
     * @param int $per_page
     * @return array
     */
	public static function getMyColumnList($uid,$sort='new',$verify=1,$page=1,$per_page=10): array
    {
		$order = array();
		$where[] = ['verify','=',$verify];
		$where[] = ['uid','=',$uid];
		switch ($sort)
		{
			case 'new':
				$order['create_time'] = 'DESC';
				break;
			case 'hot':
				$order['view_count'] = 'DESC';
				break;
		}
		$list = db('column')->where($where)->order($order)->paginate([
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param()
			]);
		$pageVar = $list->render();
		$list = $list->toArray();
		$total = $list['last_page'];
		foreach ($list['data'] as $key => $value) {
            $list['data'][$key]['description'] = str_cut(strip_tags(htmlspecialchars_decode($value['description'])),0,50) ;
            $list['data'][$key]['has_focus'] = 0 ;
			if(db('column_focus')->where(['uid'=>intval($uid),'column_id'=>$value['id']])->value('id'))
			{
                $list['data'][$key]['has_focus'] = 1 ;
                $list['data'][$key]['item_type'] = 'column' ;
			}
		}

		return ['list'=>$list['data'],'page'=>$pageVar,'total'=>$total,'count'=>$list['total']];
	}

    /**
     * 检查用户是否关注过专栏
     * @param $column_id
     * @param $uid
     * @return bool
     */
	public static function checkFocus($column_id,$uid): bool
    {
		if(db('column_focus')->where(['uid'=>intval($uid),'column_id'=>$column_id])->value('id')){
			return false;
		}
		return true;
	}
}