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
namespace app\admin\backend\module;
use app\common\controller\Backend;
use app\common\model\PayOrder;
use think\App;
use think\facade\Request;

class Order extends Backend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new PayOrder();
    }

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['title', '标题'],
            ['user_name','用户名'],
            ['trade_no','系统订单号'],
            ['out_trade_no','三方订单号'],
            ['pay_type', '付款方式', 'radio', '',[
                'wechat' => '微信支付',
                'alipay' => '支付宝支付',
                'balance'=>'余额支付'
            ]],
            ['amount','交易金额', 'number'],
            ['status_text','交易状态', 'radio', '',[
                '0' => '待支付',
                '1' => '已支付',
                '2' => '支付失败'
            ]],
            ['remark','交易备注','text'],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['select', 'status', '支付状态', '=','',['0' => '待支付','1' => '已支付','2' => '支付失败']],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            $list = db('pay_order')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();

            foreach ($list['data'] as $key=>$val)
            {
                $list['data'][$key]['user_name'] = db('users')->where('uid',$val['uid'])->value('user_name');
                $list['data'][$key]['status_text'] = $val['status'];
            }
            return $list;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['detail'=>[
                'title'       => '详情',
                'icon'        => '',
                'class'       => 'btn btn-warning btn-xs uk-ajax-open',
                'url'        => (string)url('detail', ['id' => '__id__']),
                'href' =>''
            ],'delete'])
            ->addTopButtons(['delete'])
            ->fetch();
    }

    public function detail($id)
    {
        $info =db('pay_order')->where('id',$id)->find();
        $pay_type = [
            ''=>'未选择',
            'wechat' => '微信支付',
            'alipay' => '支付宝支付',
            'balance'=>'余额支付'
        ];
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('title','标题','',$info['title'],'','disabled readonly')
            ->addText('trade_no','系统订单号','',$info['trade_no'],'','disabled readonly')
            ->addText('out_trade_no','三方订单号','',$info['out_trade_no'],'','disabled readonly')
            ->addText('pay_type','付款方式','',$pay_type[$info['pay_type']],'','disabled readonly')
            ->addText('amount','交易金额','',$info['amount'],'','disabled readonly')
            ->addText('order_type','交易类型','',$info['order_type'],'','disabled readonly')
            ->addText('relation_type','关联类型','',$info['relation_type'],'','disabled readonly')
            ->addText('relation_id','关联ID','',$info['relation_id'],'','disabled readonly')
            ->addRadio('status','支付状态','',[
                '0' => '待支付',
                '1' => '已支付',
                '2' => '支付失败'
            ],$info['status'],'disabled readonly')
            ->addTextarea('remark','备注','',$info['remark'],'disabled readonly')
            ->fetch();
    }
}