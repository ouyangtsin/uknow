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


namespace app\member\frontend;

use app\common\controller\Frontend;
use think\App;
use app\common\model\Draft as DraftModel;
/**
 * 草稿
 * Class Draft
 * @package app\ask\controller
 */
class Draft extends Frontend
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        if(!$this->user_id)
        {
            $this->redirect('/');
        }
        $this->model = new DraftModel;
    }


    public function index()
    {
        $item_type = $this->request->param('type','question');
        $this->assign('type',$item_type);
        $data = DraftModel::getDraftByType($this->user_id,$item_type);
        $this->assign($data);
        $this->assign('type',$item_type);
        return $this->fetch();
    }

    public function delete()
    {
        $item_type = $this->request->param('type','question');
        $item_id = $this->request->param('item_id',0);
        if(DraftModel::deleteDraftByItemID($this->user_id,$item_type,$item_id))
        {
            $this->result([],1,'删除成功');
        }
        $this->result([],0,'删除失败');
    }
}
