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
use app\common\model\PayLog;
use think\App;
use think\facade\Request;

class Log extends Backend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new PayLog();
    }

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['user_name', '用户'],
            ['order_id','交易记录'],
            ['item_id','关联ID'],
            ['item_type','关联类型'],
            ['money','交易金额'],
            ['balance','账户余额'],
            ['pay_type','付款方式', 'radio', '',[
                'wechat' => '微信支付',
                'alipay' => '支付宝支付',
                'balance'=>'余额支付'
            ]],
            ['status_text', '状态', 'radio', '0',[
                '0' => '待支付',
                '1' => '已支付',
                '2' => '支付失败'
            ]],
            ['remark','交易备注','text'],
            ['create_time', '创建时间','datetime'],
        ];

        $search = [

        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            $list = db('pay_log')
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
        $info =db('pay_log')->where('id',$id)->find();
        $pay_type = [
            ''=>'未选择',
            'wechat' => '微信支付',
            'alipay' => '支付宝支付',
            'balance'=>'余额支付'
        ];
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('pay_type','付款方式','',$pay_type[$info['pay_type']],'','disabled readonly')
            ->addText('money','交易金额','',$info['money'],'','disabled readonly')
            ->addText('balance','余额','',$info['balance'],'','disabled readonly')
            ->addText('item_type','关联类型','',$info['item_type'],'','disabled readonly')
            ->addText('item_id','关联ID','',$info['item_id'],'','disabled readonly')
            ->addRadio('status','支付状态','',[
                '0' => '待支付',
                '1' => '已支付',
                '2' => '支付失败'
            ],$info['status'],'disabled readonly')
            ->addTextarea('remark','备注','',$info['remark'],'disabled readonly')
            ->fetch();
    }
}