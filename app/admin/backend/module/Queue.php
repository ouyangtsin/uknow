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
use app\common\service\QueueService;

use think\App;
use think\facade\Request;

class Queue extends Backend
{
    //队列主页
	public function index()
	{
        $columns = [
            ['id'  , '编号'],
            ['code', '任务编号'],
            ['title','任务名称','text'],
            ['command','执行指令'],
            ['exec_pid','执行进程'],
            ['exec_data','执行参数'],
            ['exec_time','执行时间','datetime'],
            ['exec_desc','任务描述'],
            ['enter_time', '开始时间','datetime'],
            ['outer_time', '结束时间','datetime'],
            ['loops_time','循环时间'],
            ['attempts','执行次数'],
            ['rscript','任务类型',[0=>'单例',1=>'多例']],
            ['status_text', '状态', 'radio', '0',[1=>'新任务',2=>'处理中',3=>'成功',4=>'失败']],
            ['create_at', '创建时间','datetime'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            // 排序处理
            $data = db('queue')
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' => 15,
                ])
                ->toArray();

            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['status_text'] = $val['status'];
            }
            return $data;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['delete'])
            ->addTopButtons([
                'delete',
                'start'=>[
                    'title'   => '开始监听',
                    'icon'    => 'fa fa-file-code-o',
                    'class'   => 'btn btn-info uk-ajax-get',
                    'url'    => (string)url('start'),
                ],
                'stop'=>[
                    'title'   => '停止监听',
                    'icon'    => 'fa fa-file-code-o',
                    'class'   => 'btn btn-info uk-ajax-get',
                    'url'    => (string)url('stop'),
                ],
                'test'=>[
                    'title'   => '队列测试',
                    'icon'    => 'fa fa-file-code-o',
                    'class'   => 'btn btn-info do-queue',
                    'url'    => (string)url('test'),
                ],
                ])
            ->fetch();
	}

    /**
     * WIN创建监听进程
     */
    public function start()
    {
        $message = nl2br($this->app->console->call('queue', ['start'])->fetch());
        if (stripos($message, 'daemons started successfully for pid')) {
            $this->success('任务监听主进程启动成功！');
        } elseif (stripos($message, 'daemons already exist for pid')) {
            $this->success('任务监听主进程已经存在！');
        } else {
            $this->error($message);
        }
    }

    public function stop()
    {
        $message = nl2br($this->app->console->call('queue', ['stop'])->fetch());
        if (stripos($message, 'sent end signal to process')) {
            $this->success('停止任务监听主进程成功！');
        } elseif (stripos($message, 'processes to stop')) {
            $this->success('没有找到需要停止的进程！');
        } else {
            $this->error($message);
        }
    }


    public function status(){
        $message = $this->app->console->call('queue', ['status'])->fetch();
        if (preg_match('/process.*?\d+.*?running/', $message, $attrs)) {
            $this->success('<span class="color-green">' . $message . '</span>');
        } else {
            $this->success('<span class="color-red">' . $message . '</span>');
        }
    }

    public function test()
    {
		$sql="select * from uk_question";
		$queue=QueueService::instance()->register('测试任务','test test',1,['sql'=>$sql],0);
		$ret=['code'=>1,'msg'=>'创建任务成功！','data'=>$queue->code];
		return json($ret);
    }


    /**
     * 重启任务
     * @auth true
     */
    public function redo()
    {
        $data = input('get.');
        $queue = QueueService::instance()->initialize($data['code'])->reset();
        $queue->progress(1, '>>> 任务重置成功 <<<', 0.00);
        $this->success('任务重置成功！','', $queue->code);
    }
}