<?php

namespace app\common\command;

use app\common\service\ProcessService;
use app\common\service\QueueService;
use think\console\Command as ThinkCommand;
use think\console\Input;
use think\console\Output;

/**
 * 自定义指令基类
 * Class Command
 * @package think\admin
 */
abstract class Command extends ThinkCommand
{
    /**
     * 任务控制服务
     * @var QueueService
     */
    protected $queue;

    /**
     * 进程控制服务
     * @var ProcessService
     */
    protected $process;

    /**
     * 初始化指令变量
     * @param Input $input
     * @param Output $output
     * @return static
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function initialize(Input $input, Output $output): Command
    {
        $this->queue = QueueService::instance();
        $this->process = ProcessService::instance();
        if (defined('WorkQueueCode')) {
            if (!$this->queue instanceof QueueService) {
                $this->queue = QueueService::instance();
            }
            if ($this->queue->code !== WorkQueueCode) {
                $this->queue->initialize(WorkQueueCode);
            }
        }
        return $this;
    }

    /**
     * 设置进度消息并继续执行
     * @param null|string $message 进度消息
     * @param null|integer $progress 进度数值
     * @return static
     */
    protected function setQueueProgress(?string $message = null, $progress = null)
    {
        if (defined('WorkQueueCode')) {
            $this->queue->progress(2, $message, $progress);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }

    /**
     * 设置失败消息并结束进程
     * @param string $message 消息内容
     * @return static
     * @throws Exception
     */
    protected function setQueueError(string $message): Command
    {
        if (defined('WorkQueueCode')) {
            $this->queue->error($message);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }

    /**
     * 设置成功消息并结束进程
     * @param string $message 消息内容
     * @return static
     * @throws Exception
     */
    protected function setQueueSuccess(string $message): Command
    {
        if (defined('WorkQueueCode')) {
            $this->queue->success($message);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }

}