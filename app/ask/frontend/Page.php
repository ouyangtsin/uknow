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

class Page extends Frontend
{
    public function index()
    {
        $id = $this->request->get('id',0);
        $url_name = $this->request->get('url_name','');
        $where[]=['status','=',1];
        if($id)
        {
            $where[]=['id','=',$id];
        }

        if($url_name)
        {
            $where[]=['url_name','=',$url_name];
        }

        $info = db('page')->where($where)->find();
        if($info)
        {
            $info['contents'] = htmlspecialchars_decode($info['contents']);
            $this->assign(['info' => $info]);
            $this->TDK($info['title'],$info['keywords'],$info['description']);
            return $this->fetch();
        }
        $this->error('页面不存在','/');
    }
}