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
use think\App;

/**
 * 公用搜索模块
 * Class Search
 * @package app\ask\controller
 */
class Search extends Frontend
{
	protected $handle;
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->handle = new \app\common\logic\search\Search();
	}

	public function index()
	{
        $keywords = $this->request->param('keywords','');
        $this->assign('keywords',urlencode($keywords));
		return $this->fetch();
	}

	//头部搜索
	public function header_list()
	{
		$keywords = $this->request->param('keywords','');
        $data = $this->handle->search($keywords,'all',$this->user_id,null);
        $this->assign([
            'type'=>'users',
            'keywords'=>$keywords,
            'list'=>$data ? $data['list'] : []
        ]);
        return $this->fetch();
	}

    /**
     * ajax搜索结果页面
     */
	public function search_result()
    {
        $order = $this->request->post('sort','all');
        $type = $this->request->post('type','all');
        $time = $this->request->post('time','365');

        $keywords = $this->request->post('keywords','');
        $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
        $page = $this->request->param('page',1);

        $data = $this->handle->search($keywords,$type,$time,$this->user_id,$order,$page);
        if($data)
        {
            $this->assign([
                'type'=>$type,
                'keywords'=>$keywords,
                'list'=>$data['list']
            ]);
            $this->result(['html'=>$this->fetch(),'page'=>$data['total']],1);
        }

        $this->result([],0,'暂无数据');
    }
}