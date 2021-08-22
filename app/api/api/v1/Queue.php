<?php

namespace app\api\api\v1;
use app\common\controller\Controller;
use app\common\service\QueueService;

/**
 * 任务队列接口
 * Class Queue
 * @package app\api\controller\api
 */
class Queue extends Controller
{
    /**
     * 任务进度查询
     */
    public function progress()
    {
        $input=input('post.');
        $queue = QueueService::instance()->initialize($input['code']);
        $this->success('获取任务进度成功！','', $queue->progress());
    }
}