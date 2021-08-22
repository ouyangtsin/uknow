<?php


namespace app\common\service;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Exception;

class QueueService extends Service
{

    /**
     * 当前任务编号
     * @var string
     */
    public $code = '';

    /**
     * 当前任务标题
     * @var string
     */
    public $title = '';

    /**
     * 当前任务参数
     * @var array
     */
    public $data = [];

    /**
     * 当前任务数据
     * @var array
     */
    public $record = [];

    /**
     * 数据初始化
     * @param integer $code
     * @return static
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException|Exception
     */
    public function initialize($code = 0): QueueService
    {
        if (!empty($code)) {
            $this->code = $code;
            $this->record = $this->app->db->name('Queue')->where(['code' => $this->code])->find();
            if (empty($this->record)) {
                $this->app->log->error("Queue initialize failed, Queue {$code} not found.");
                throw new Exception("Queue initialize failed, Queue {$code} not found.");
            }
            [$this->code, $this->title] = [$this->record['code'], $this->record['title']];
            $this->data = json_decode($this->record['exec_data'], true) ?: [];
        }
        return $this;
    }

    /**
     * 重发异步任务
     * @param integer $wait 等待时间
     * @return $this
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function reset($wait = 0): QueueService
    {
        if (empty($this->record)) {
            $this->app->log->error("Queue reset failed, Queue {$this->code} data cannot be empty!");
            throw new Exception("Queue reset failed, Queue {$this->code} data cannot be empty!");
        }
        $this->app->db->name('Queue')->where(['code' => $this->code])->strict(false)->failException(true)->update([
            'exec_pid' => 0, 'exec_time' => time() + $wait, 'status' => 1,
        ]);
        return $this->initialize($this->code);
    }

    /**
     * 添加定时清理任务
     * @return $this
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException|Exception
     */
    public function addCleanQueue(): QueueService
    {
        return $this->register('定时清理系统任务数据', "queue clean", 0, [], 0, 3600);
    }

    /**
     * 注册异步处理任务
     * @param string $title 任务名称
     * @param string $command 执行脚本
     * @param integer $later 延时时间
     * @param array $data 任务附加数据
     * @param integer $rscript 任务类型(0单例,1多例)
     * @param integer $loops 循环等待时间
     * @return $this
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function register(string $title, string $command, $later = 0, $data = [], $rscript = 0, $loops = 0): self
    {
        $map = [['title', '=', $title], ['status', 'in', ['1', '2']]];
        if (empty($rscript) && ($queue = $this->app->db->name('queue')->where($map)->find())) {
            // $this->code=$queue['code'];
            // $this->record=$queue;
            // $this->reset();
            throw new Exception(lang('think_library_queue_exist'));
        }

        $this->code = uniqueDate(16, 'Q');
        $this->app->db->name('queue')->strict(false)->failException(true)->insert([
            'code'       => $this->code,
            'title'      => $title,
            'command'    => $command,
            'attempts'   => '0',
            'rscript'    => (int)(bool)$rscript,
            'exec_data'  => json_encode($data, JSON_UNESCAPED_UNICODE),
            'exec_time'  => $later > 0 ? time() + $later : time(),
            'enter_time' => '0',
            'outer_time' => '0',
            'loops_time' => $loops,
        ]);
        $this->progress(1, '>>> 任务创建成功 <<<', 0.00);
        return $this->initialize($this->code);
    }

    /**
     * 设置任务进度信息
     * @param null|integer $status 任务状态
     * @param null|string $message 进度消息
     * @param null|float $progress 进度数值
     * @return array
     */
    public function progress(?int $status = null, ?string $message = null, $progress = null): array
    {
        $key = "queue_{$this->code}_progress";

        if (is_numeric($status) && intval($status) === 3) {
            if (!is_numeric($progress)) $progress = '100.00';
            if (is_null($message)) $message = '>>> 任务已经完成 <<<';
        }
        if (is_numeric($status) && intval($status) === 4) {
            if (!is_numeric($progress)) $progress = '0.00';
            if (is_null($message)) $message = '>>> 任务执行失败 <<<';
        }

        try {
            $data = $this->app->cache->get($key, [
                'code' => $this->code, 'status' => $status, 'message' => $message, 'progress' => $progress, 'history' => [],
            ]);
        } catch (\Exception|\Error $exception) {
            return $this->progress($status, $message, $progress);
        }
        if (is_numeric($status)) $data['status'] = intval($status);
        if (is_numeric($progress)) $progress = str_pad(sprintf("%.2f", $progress), 5, '0', STR_PAD_LEFT);
        if (is_string($message) && is_null($progress)) {
            $data['message'] = $message;
            $data['history'][] = ['message' => $message, 'progress' => $data['progress'], 'datetime' => date('Y-m-d H:i:s')];
        } elseif (is_null($message) && is_numeric($progress)) {
            $data['progress'] = $progress;
            $data['history'][] = ['message' => $data['message'], 'progress' => $progress, 'datetime' => date('Y-m-d H:i:s')];
        } elseif (is_string($message) && is_numeric($progress)) {
            $data['message'] = $message;
            $data['progress'] = $progress;
            $data['history'][] = ['message' => $message, 'progress' => $progress, 'datetime' => date('Y-m-d H:i:s')];
        }
        if (is_string($message) || is_numeric($progress)) {
            if (count($data['history']) > 10) {
                $data['history'] = array_slice($data['history'], -10);
            }
            $this->app->cache->set($key, $data, 86400);
        }

        return $data;
    }

    /**
     * 更新任务进度
     * @param integer $total 记录总和
     * @param integer $used 当前记录
     * @param string $message 文字描述
     */
    public function message(int $total, int $used, string $message = ''): void
    {
        $total = $total < 1 ? 1 : $total;
        $prefix = str_pad("{$used}", strlen("{$total}"), '0', STR_PAD_LEFT);
        $message = "[{$prefix}/{$total}] {$message}";
        if (defined('WorkQueueCode')) {
            $this->progress(2, $message, sprintf("%.2f", $used / $total * 100));
        } else {
            echo $message . PHP_EOL;
        }
    }

    /**
     * 任务执行成功
     * @param string $message 消息内容
     * @throws Exception
     */
    public function success(string $message)
    {
        throw new Exception($message, $this->code);
    }

    /**
     * 任务执行失败
     * @param string $message 消息内容
     * @throws Exception
     */
    public function error(string $message): void
    {
        throw new Exception($message, $this->code);
    }

    /**
     * 执行任务处理
     * @param array $data 任务参数
     * @return mixed
     */
    public function execute(array $data = [])
    {
    }

}