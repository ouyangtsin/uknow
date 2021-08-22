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
use app\common\library\helper\PayHelper;
use app\common\library\helper\RandomHelper;
use app\common\model\PayOrder;

class Pay extends Frontend
{
    /**
     * 扫码弹窗
     */
    public function pay_img()
    {
        $order_id = $this->request->param('order_id');
        $pay_type = $this->request->param('pay_type');

        $order_info = PayOrder::getOrderInfo($order_id);
        if(!$order_info)
        {
            $this->result([],0,'订单不存在');
        }
        PayOrder::updateOrder($order_id,['pay_type'=>$pay_type]);
        $options = [
            'body'             => $order_info['title'].'#'.$order_info['relation_id'],
            'out_trade_no'     =>$order_info['out_trade_no'],
            'product_id'       =>$order_info['relation_id'],
            'amount'        => $order_info['amount'],
        ];
        $pay_type_text = [
            'wechat'=>'微信',
            'alipay'=>'支付宝'
        ];
        $codeImg = '';
        if($pay_type=='wechat' && !$codeImg = PayHelper::getWechatScanImage($options))
        {
            $this->error(PayHelper::getError());
        }

        if($pay_type=='alipay' && !$codeImg = PayHelper::getAlipayScanImage($options))
        {
            $this->error(PayHelper::getError());
        }
        $this->view->engine()->layout(false);
        $this->assign('img',$codeImg);
        $this->result(['html'=>$this->fetch(),'text'=>$pay_type_text[$pay_type]],1,'');
    }

    /**
     * 发起扫码请求
     */
    public function apply_scan_pay()
    {
        $postData = $this->request->post();
        if(!$order_info = PayOrder::insertOrder($this->user_id,$postData))
        {
            return false;
        }

        $this->assign($order_info);
        $this->result(['html'=>$this->fetch()],1);
    }

    /**
     * 检查支付状态
     */
    public function check_status()
    {
        $order_id = $this->request->param('order_id)');
        $order_info = PayOrder::getOrderInfo($order_id);
        if(PayHelper::checkOrderStatus($order_info['out_trade_code']))
        {
            $this->result([],1,'支付成功');
        }
    }

    /**
     * 余额支付
     */
    public function balance_pay()
    {
        if(!$this->request->isPost())
        {
            $this->error('支付方式不正确');
        }
        $password = $this->request->post('password');
        $order_id = $this->request->post('order_id');
        if(!password_verify($password,db('users')->where('uid',$this->user_id)->value('deal_password')))
        {
            $this->error('交易密码不正确');
        }

        $order_info = PayOrder::getOrderInfo($order_id);
        if($order_info['amount']>$this->user_info['money'])
        {
            $this->error('您的余额不足，无法发起支付！');
        }

        if(!PayOrder::updateOrder($order_id,['status'=>1,'pay_type'=>'balance']))
        {
            $this->error('支付失败！');
        }
        $this->success('支付成功','',['id'=>$order_info['relation_id']]);
    }
}