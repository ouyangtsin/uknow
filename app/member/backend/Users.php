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
namespace app\member\backend;
use app\common\library\helper\ExcelHelper;
use app\common\library\helper\LogHelper;
use app\common\model\Users as UserModel;
use app\common\controller\Backend;
use think\App;
use think\facade\Request;

/**
 * 后台用户管理模块
 * Class User
 * @package app\admin\controller\member
 */
class Users extends Backend
{
	public function initialize()
    {
        parent::initialize();
        $this->model = new UserModel();
        $this->table = 'users';
        $this->validate = 'app\common\validate\Users';
    }

    public function index()
    {
        $columns = [
            ['uid','用户ID'],
            ['user_name', '用户名','link',(string)url('member/index//index',['uid'=>'__uid__'])],
            ['avatar','用户头像','image'],
            ['nick_name','用户昵称'],
            ['email','用户邮箱'],
            ['power_group_name','声望组','tag'],
            ['score_group_name','积分组','tag'],
            ['group_name','系统组','tag'],
            ['last_login_time','最后登录时间','datetime'],
            ['last_login_ip','最后登录IP'],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];
        $search = [
            ['text', 'user_name', '用户名', 'LIKE'],
            ['select', 'score_group_id', '积分组', '=','',array_column(db('users_score_group')->column('title,id'),'title','id')],
            ['select', 'power_group_id', '声望组', '=','',array_column(db('users_power_group')->column('title,id'),'title','id')],
            ['select', 'group_id', '系统组', '=','',array_column(db('auth_group')->column('title,id'),'title','id')],
        ];
        $status = $this->request->param('status',1);
        //正常用户操作
        $right_button = [
            'edit',
            'delete',
            'forbidden' => [
                'title'       => '封禁',
                'icon'        => '',
                'class'       => 'btn btn-success btn-xs uk-ajax-open',
                'url'        => (string)url('forbidden', ['id' => '__id__']),
                'target'      => '',
                'href' => ''
            ]
        ];
        $top_button = [
            'add',
            'delete',
            'export',
            'forbidden'=>
                [
                    'title'   => '封禁',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectDialog("'. url('forbidden') .'","封禁用户")',
                ]
        ];

        //删除用户操作
        if(!$status)
        {
            $right_button = [
                'edit',
                'delete',
                'recover' => [
                    'title'       => '恢复',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-xs uk-ajax-get',
                    'url'        => (string)url('manager', ['id' => '__id__','action'=>'recover']),
                    'target'      => '',
                    'href' => ''
                ],
                'remove' => [
                    'title'       => '彻底删除',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-xs uk-ajax-get',
                    'url'        => (string)url('manager', ['id' => '__id__','action'=>'remove']),
                    'target'      => '',
                    'href' => ''
                ]
            ];
            $top_button = [
                'add',
                'export',
                'recover'=>[
                    'title'   => '恢复删除',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectAll("'. url('manager') .'","恢复删除用户","recover")',
                ],
                'remove'=>[
                    'title'   => '彻底删除',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectAll("'. url('manager') .'","恢复删除用户","remove")',
                ]
            ];
        }

        //待审核用户操作
        if($status==2)
        {
            $right_button = [
                'edit',
                'delete',
                'approval' => [
                    'title'       => '审核通过',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-xs uk-ajax-get',
                    'url'        => (string)url('approval', ['id' => '__id__']),
                    'target'      => '',
                    'href'=>''
                ],
                'decline' => [
                    'title'       => '拒绝审核',
                    'icon'        => 'fa fa-edit',
                    'class'       => 'btn btn-success btn-xs uk-ajax-get',
                    'url'        => (string)url('decline', ['id' => '__id__']),
                    'target'      => '',
                    'href'=>''
                ]
            ];
            $top_button = [
                'add',
                'delete',
                'export',
                'approval'=>[
                    'title'   => '审核通过',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectAll("'. url('approval') .'","审核通过","approval")',
                ],
                'decline'=>[
                    'title'   => '拒绝审核',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectAll("'. url('decline') .'","拒绝审核","decline")',
                ]
            ];
        }

        //拒绝审核用户操作
        if($status==4)
        {
            $right_button = [
                'edit',
                'delete',
                'approval' => [
                    'title'       => '审核通过',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-xs uk-ajax-get',
                    'url'        => (string)url('approval', ['id' => '__id__']),
                    'target'      => '',
                    'href'=>''
                ]
            ];
            $top_button = [
                'add',
                'delete',
                'export',
                'approval'=>[
                    'title'   => '审核通过',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectAll("'. url('approval') .'","审核通过","approval")',
                ],
            ];
        }

        //封禁用户操作
        if($status==3)
        {
            $right_button = [
                'edit',
                'delete',
                'un_forbidden' =>
                    [
                        'title'   => '解除封禁',
                        'icon'    => 'fa fa-times',
                        'class'   => 'btn btn-warning multiple disabled',
                        'href'    => '',
                        'onclick' => 'UK.operate.selectAll("'. url('un_forbidden') .'","解除封禁")',
                    ]
            ];
            $top_button = [
                'add',
                'delete',
                'export',
                'un_forbidden' =>
                [
                    'title'   => '解除封禁',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => '',
                    'onclick' => 'UK.operate.selectAll("'. url('un_forbidden') .'","解除封禁")',
                ]];
        }

        if ($this->request->param('_list'))
        {
            $where1 = [];
            $where['status'] = $status;
            if($score_group_id = $this->request->post('score_group_id'))
            {
                $where1['g.score_group_id'] = $score_group_id;
            }
            if($group_id = $this->request->post('group_id'))
            {
                $where1['g.group_id'] = $group_id;
            }
            if($power_group_id = $this->request->post('power_group_id'))
            {
                $where1['g.power_group_id'] = $power_group_id;
            }

            if($user_name = $this->request->post('user_name'))
            {
                $where['u.user_name'] = $user_name;
            }

            // 排序规则
            $isAsc = $this->request->param('isAsc') ?? 'desc';

            $list = db('users')
                ->alias('u')
                ->order('u.uid',$isAsc)
                ->where($where)
                ->join('auth_group_access g','u.uid=g.uid')
                ->where($where1)
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();

            foreach ($list['data'] as $key=>$val)
            {
                $group_info = UserModel::getUserGroupInfo($val['uid']);
                $list['data'][$key]['score_group_name'] = $group_info ? $group_info['score_group_name'] : '';
                $list['data'][$key]['power_group_name'] = $group_info ? $group_info['power_group_name'] : '';
                $list['data'][$key]['group_name'] = $group_info ? $group_info['group_name'] :'';
            }
            return $list;

        }

        return $this->tableBuilder
            ->setUniqueId('uid')
            ->addColumns($columns)
            ->setSearch($search)
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons($right_button)
            ->addTopButtons($top_button)
            ->setLinkGroup([
                [
                    'title'=>'用户列表',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'删除列表',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ],
                [
                    'title'=>'拒绝审核',
                    'link'=>(string)url('index', ['status' => 4]),
                    'active'=> $status==4
                ],
                [
                    'title'=>'封禁用户',
                    'link'=>(string)url('index', ['status' => 3]),
                    'active'=> $status==3
                ]
            ])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $group_id = $data['group_id'] ?: 3;
            $score_group_id = $data['score_group_id'] ?: 1;
            $power_group_id = $data['power_group_id'] ?: 1;
            unset($data['group_id'],$data['power_group_id'],$data['score_group_id']);
            $data['password'] = password_hash($data['password'],1);
            $data['status'] = intval($data['status']);
            $data['sex'] = intval($data['sex']);
            $result = db('users')->insertGetId($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                db('auth_group_access')->insert(['uid'=>intval($result),'group_id'=>$group_id,'power_group_id'=>$power_group_id,'score_group_id'=>$score_group_id,'create_time'=>time()]);
                //添加积分记录
                LogHelper::addScoreLog('user_register',intval($result),'users',intval($result));
                $this->success('添加成功','index');
            }
        }
        $group = db('auth_group')->column('title','id');
        $users_score_group = db('users_score_group')->column('title','id');
        $users_power_group = db('users_power_group')->column('title','id');
        return $this->formBuilder
            ->addSelect('group_id','系统组','请选择用户系统组',$group)
            ->addSelect('score_group_id','积分组','请选择积分组',$users_score_group)
            ->addSelect('power_group_id','声望组','请选择声望组',$users_power_group)
            ->addText('nick_name','用户昵称','填写用户昵称')
            ->addText('user_name','用户名','填写用户名')
            ->addPassword('password','用户密码','填写用户密码')
            ->addText('email','用户邮箱','填写用户邮箱')
            ->addText('mobile','用户手机','填写用户手机')
            ->addImage('avatar','用户头像','上传用户头像')
            ->addTextarea('signature','个人签名','填写个人签名')
            ->addRadio('sex','用户性别','选择用户性别',['0' => '保密','1' => '男',2=>'女'])
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit(string $id='')
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file']);
            $group_id = $data['group_id'] ? $data['group_id'] : 3;
            $score_group_id = $data['score_group_id'] ?: 1;
            $power_group_id = $data['power_group_id'] ?: 1;
            unset($data['group_id'],$data['power_group_id'],$data['score_group_id']);
            if(isset($data['password']) && $data['password']!=''){
                $data['password']=password_hash($data['password'],1);
            }else{
                unset($data['password']);
            }
            $id = $data['uid'];
            $data['avatar'] = $data['avatar'] ? : null;
            $result = $this->model->where(['uid'=>$data['uid']])->update($data);
            db('auth_group_access')->where('uid',$id)->update(['group_id'=>$group_id,'power_group_id'=>$power_group_id,'score_group_id'=>$score_group_id,'update_time'=>time()]);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->where('uid',$id)->find()->toArray();
        $auth_group_access = db('auth_group_access')->where(['uid'=>$info['uid']])->find();
        $group = db('auth_group')->column('title','id');
        $users_score_group = db('users_score_group')->column('title','id');
        $users_power_group = db('users_power_group')->column('title','id');
        return $this->formBuilder
            ->addHidden('uid',$info['uid'])
            ->addSelect('group_id','系统组','请选择用户系统组',$group,$auth_group_access['group_id'])
            ->addSelect('score_group_id','积分组','请选择积分组',$users_score_group,$auth_group_access['score_group_id'])
            ->addSelect('power_group_id','声望组','请选择声望组',$users_power_group,$auth_group_access['power_group_id'])
            ->addText('nick_name','用户昵称','填写用户昵称',$info['nick_name'])
            ->addText('user_name','用户名','填写用户名',$info['user_name'])
            ->addPassword('password','用户密码','填写用户密码','')
            ->addText('email','用户邮箱','填写用户邮箱',$info['email'])
            ->addText('mobile','用户手机','填写用户手机',$info['mobile'])
            ->addImage('avatar','用户头像','上传用户头像',$info['avatar'])
            ->addTextarea('signature','个人签名','填写个人签名',$info['signature'])
            ->addRadio('sex','用户性别','选择用户性别',['0' => '保密','1' => '男',2=>'女'],$info['sex'])
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }

    //删除用户
    public function delete(string $id)
    {
        if ($this->request->isPost()) {
            if(UserModel::removeUser($id))
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => UserModel::getError()];
        }
    }

    //导出
    public function export()
    {
        $columns = [
            ['uid','用户ID'],
            ['user_name', '用户名','link',(string)url('member/index//index',['uid'=>'__uid__'])],
            ['avatar','用户头像','image'],
            ['nick_name','用户昵称'],
            ['email','用户邮箱'],
            ['user_group_name','用户组','tag'],
            ['group_name','系统组','tag'],
            ['status', '用户状态', 'radio', '0',[
                ['0' => '已删除'],
                ['1' => '正常']
            ]],
            ['last_login_time','最后登录时间','datetime'],
            ['last_login_ip','最后登录IP'],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];
        $search = [
            ['text', 'user_name', '用户名', 'LIKE'],
        ];
        $orderByColumn = $this->request->param('orderByColumn') ?? 'uid';
        $isAsc = $this->request->param('isAsc') ?? 'desc';
        $where = $this->makeBuilder->getWhere('uid',$search);
        $list = UserModel::getList($where,[$orderByColumn => $isAsc]);
        return ExcelHelper::exportData($list['data'],$columns,'用户信息导出');
    }

    /**
     * 封禁操作
     */
    public function forbidden()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $data['id'] = is_array($data['id']) ? $data['id'] : explode(',',$data['id']);
            if($data['id'])
            {
                foreach ($data['id'] as $key=>$val)
                {
                    if(!db('users_forbidden')->where(['uid'=>$val])->find())
                    {
                        db('users_forbidden')->insert([
                            'uid'=>$val,
                            'forbidden_time'=>strtotime($data['forbidden_time']),
                            'forbidden_reason'=>trim($data['forbidden_reason']),
                            'create_time'=>time(),
                            'status'=>1
                        ]);
                        db('users')->whereIn('uid',$data['id'])->update(['status'=>3]);
                    }
                }
            }
            return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
        }
        $id = $this->request->param('id');
        return $this->formBuilder
            ->addHidden('id',$id)
            ->addDatetime('forbidden_time','封禁时长')
            ->addTextarea('forbidden_reason','封禁原因','填写封禁原因')
            ->fetch();
    }

    /**
     * 解除封禁
     */
    public function un_forbidden()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $data['id'] = is_array($data['id']) ? $data['id'] : explode(',',$data['id']);
            if($data['id'])
            {
                foreach ($data['id'] as $key=>$val)
                {
                    if($id=db('users_forbidden')->where(['uid'=>$val])->value('id'))
                    {
                        db('users_forbidden')->where(['id'=>$id])->update([
                            'status'=>0
                        ]);
                    }
                    db('users')->whereIn('uid',$data['id'])->update(['status'=>1]);
                }
            }

            return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
        }
    }

    /**
     * 审核用户
     */
    public function approval()
    {
        $id = $this->request->param('id');
        $id = is_array($id) ? $id : explode(',',$id);
        if($id)
        {
            db('users')->whereIn('uid',$id)->update(['status'=>2]);
        }
        return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
    }

    /**
     * 拒绝审核
     */
    public function decline()
    {
        $id = $this->request->param('id');
        $id = is_array($id) ? $id : explode(',',$id);
        if($id)
        {
            db('users')->whereIn('uid',$id)->update(['status'=>4]);
        }

        return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
    }

    /**
     * 通用管理
     */
    public function manager()
    {
        if($this->request->isPost())
        {
            $id = $this->request->post('id');
            $type = $this->request->post('type');
            $id = is_array($id) ? $id : explode(',',$id);

            if(empty($id))
            {
                return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
            }

            switch ($type)
            {
                case 'recover':
                    if(db('users')->whereIn('uid',$id)->update(['status'=>1]))
                    {
                        return json(['error'=>0,'msg'=>'恢复成功!']);
                    }
                    return ['error' => 1, 'msg' => '恢复失败'];
                    break;

                case 'remove':

                    if(UserModel::removeUser($id))
                    {
                        return json(['error'=>0,'msg'=>'删除成功!']);
                    }
                    return ['error' => 1, 'msg' => UserModel::getError()];
                    break;
            }
        }
    }
}