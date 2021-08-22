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

namespace app\admin\backend\system;
use app\common\controller\Backend;
use app\common\library\helper\IpHelper;
use think\App;
use app\admin\model\Action as ActionModel;
use think\facade\Request;

/**
 * @title 行为管理
 * @description 行为管理
 */
class Action extends Backend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ActionModel();
		$this->table = 'action';
	}

    public function index()
    {
        $columns = [
            ['id','编号'],
            ['name', '行为标识'],
            ['title','行为名称'],
            ['remark','行为描述'],
            ['action_rule','行为规则'],
            ['log_rule','日志规则'],
            ['status', '状态', 'radio', '0',['0' => '禁用','1' => '启用']],
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
            return db('action')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = $this->model->create($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error($result['添加失败']);
            }
        }
        return $this->formBuilder
            ->addText('name','行为标识','输入行为标识 英文字母')
            ->addText('title','行为名称','输入行为名称')
            ->addTextarea('remark','行为描述','输入行为描述')
            ->addTextarea('action_rule','行为规则','输入行为规则，不写则只记录日志如:table:users|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;')
            ->addTextarea('log_rule','日志规则','记录日志备注时按此规则来生成，支持[变量|函数]。目前变量有：user,time,model,record,data;如:[user|get_username]在[time|formatTime]]登录了系统')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file']);
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->where('id',$id)->find()->toArray();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','行为标识','输入行为标识 英文字母',$info['name'])
            ->addText('title','行为名称','输入行为名称',$info['title'])
            ->addTextarea('remark','行为描述','输入行为描述',$info['remark'])
            ->addTextarea('action_rule','行为规则','输入行为规则，不写则只记录日志如:table:users|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;',$info['action_rule'])
            ->addTextarea('log_rule','日志规则','记录日志备注时按此规则来生成，支持[变量|函数]。目前变量有：user,time,model,record,data;如:[user|get_username]在[time|formatTime]]登录了系统',$info['log_rule'])
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }

    //行为日志列表
    public function log()
    {
        $columns = [
            ['id','编号'],
            ['action_id', '行为id'],
            ['uid','执行用户id'],
            ['action_ip','执行行为者ip'],
            ['model','触发行为的表'],
            ['record_id','触发行为的数据id'],
            ['anonymous', '是否匿名', 'radio', '0',['0' => '否','1' => '是']],
            ['remark','日志备注'],
            ['status', '状态', 'radio', '0',['0' => '禁用','1' => '启用']],
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
            $data = db('action_log')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();

            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['action_ip'] = IpHelper::queryIpLocalInfo($val['action_ip']);
            }
            return $data;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addTopButtons(['delete'])
            ->setDelUrl((string)url('deleteLog'))
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons([ 'edit'=>[
                'title'         => '记录详情',
            ]])
            ->setEditUrl((string)url('detail',['id'=>'__id__']))
            ->fetch();
    }

    public function deleteLog(string $id)
    {
        if ($this->request->isPost()) {
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if(db('action_log')->delete($ids)){
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }else{
                    return ['error' => 1, 'msg' => '删除失败'];
                }
            }

            if(db('action_log')->delete($id))
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => '删除失败'];
        }
    }

    public function detail(string $id)
    {

    }
}