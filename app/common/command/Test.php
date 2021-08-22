<?php

namespace app\common\command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
/**
 *测试
 * Class Database
 */
class Test extends Command
{
    public function configure()
    {
        $this->setName('test');

        $this->addArgument('action', Argument::OPTIONAL, 'test', 'test');
        $this->setDescription('更新项目数据');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return mixed
     */
    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        if (in_array('_'.$action, get_class_methods($this))) {
            return $this->{"_{$action}"}();
        }
        $this->output->error("Wrong operation, currently allow action");
    }


    protected function _test()
    {
        $data = [];
        $query = $this->queue->data['sql'];
        $ret=$this->app->db->query($query);
        $count=sizeof($ret);
        foreach ($ret as $key => $value) {
            $this->setQueueProgress('正在处理【'.$value['title'].'】',  sprintf("%.2f",(($key+1)/$count)*100));
            
        }
        $this->setQueueProgress("数据处理完成", 100);

        return "success";
    }
}