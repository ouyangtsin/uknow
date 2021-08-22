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

class PayOrder extends BaseModel
{
    /**
     * 插入订单信息
     */
    public static function insertOrder($uid,$data)
    {
        $insertData = [
            'title'=>$data['title'],
            'uid'=>$uid,
            'out_trade_no'=>$data['out_trade_no'],
            'order_type'=>$data['order_type'],
            'relation_type'=>$data['relation_type'],
            'relation_id'=>$data['relation_id'],
            'amount'=>$data['amount'],
            'remark'=>$data['remark'] ?? $data['title'].'#'.$data['relation_id'],
            'status'=>0,
            'create_time'=>time()
        ];
        return self::create($insertData)->toArray();
    }

    /**
     * 检查用户订单是否存在
     * @param $uid
     * @param $out_trade_no
     * @return mixed
     */
    public static function checkOrderExist($uid,$out_trade_no)
    {
        return db('pay_order')->where(['out_trade_no'=>$out_trade_no,'uid'=>$uid])->value('id');
    }

    public static function updateOrder($order_id,$data)
    {
        return db('pay_order')->where(['id'=>$order_id])->update($data);
    }

    public static function getOrderInfo($order_id)
    {
        return db('pay_order')->where(['id'=>$order_id])->find();
    }
}