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

namespace app\admin\backend;

use app\common\controller\Backend;
use app\common\model\Users;
use think\App;
use think\facade\Cache;
use think\facade\Request;

class Index extends Backend
{
	protected $noNeedLogin = ['login'];
	protected $noNeedRight = ['login','logout','index'];

    /**
     * 后台主页
     */
    public function index()
    {
        return $this->fetch();
    }

    public function login()
    {
    	if(!$this->user_id)
	    {
		    if($this->request->isPost())
		    {
			    if(!$this->request->checkToken())
			    {
				    $this->error('请不要重复提交');
			    }

			    $user_name = $this->request->post('user_name');
			    $password = $this->request->post('password');

			    if(!$user_info = Users::getLogin($user_name,$password))
			    {
				    $this->error(Users::getError());
			    }
			    session('admin_user_info',$user_info);
			    $this->success('登录成功','index');
		    }
	    }else{
			$this->redirect('index');
	    }
	    $this->view->engine()->layout(false);
    	if($uid = session('login_uid'))
        {
            $this->assign('user_info',Users::getUserInfo($uid));
        }else{
            $this->redirect('/');
        }
    	return $this->fetch();
    }

    public function logout()
    {
	    session('admin_user_info',null);
	    $this->success('退出成功','login');
    }

    public function clear()
    {
        $path = root_path() . 'runtime';
        if ($this->_deleteDir($path)) {
            $result['msg'] = '清除缓存成功!';
            $result['error'] = 0;
        } else {
            $result['msg'] = '清除缓存失败!';
            $result['error'] = 1;
        }
        $result['url'] = (string)url('login');
        return json($result);
    }

    private function _deleteDir($R): bool
    {
        Cache::clear();
        $handle = opendir($R);
        while (($item = readdir($handle)) !== false) {
            // log目录不可以删除
            if ($item != '.' && $item != '..' && $item != 'log') {
                if (is_dir($R . DIRECTORY_SEPARATOR . $item)) {
                    $this->_deleteDir($R . DIRECTORY_SEPARATOR . $item);
                } else {
                    if ($item != '.gitignore') {
                        if (!unlink($R . DIRECTORY_SEPARATOR . $item)) {
                            return false;
                        }
                    }
                }
            }
        }
        closedir($handle);
        return true;
    }

    /**
     * select 2 ajax分页获取数据
     * @param  int $id  字段id
     * @param  string $keyWord 搜索词
     * @param  string $rows    显示数量
     * @param  string $value   默认值
     * @return mixed
     */
    public function select2(int $id, string $keyWord = '', string $rows = '10', string $value = '')
    {
        // 字段信息
        $field = db('module_field')->find($id);
        if (is_null($field) || empty($field['relation_model']) || empty($field['relation_field'])) {
            return [];
        }
        // 获取主键
        $pk = db('module')->where('model_name', $field['relation_model'])->value('pk') ?? 'id';
        // 默认值
        if ($value) {
            $valueText = db($field['relation_model'])->where($pk, $value)->value($field['relation_field']);
            if ($valueText) {
                return [
                    'key' => $value,
                    'value' => $valueText
                ];
            }
        }
        // 搜索条件
        $where = [];
        if ($keyWord) {
            $where[] = [$field['relation_field'], 'LIKE', '%' . $keyWord . '%'];
        }

        $list = db($field['relation_model'])->field($pk . ',' . $field['relation_field'])
            ->where($where)
            ->order($pk . ' desc')
            ->paginate([
                'query' => Request::get(),
                'list_rows' => $rows,
            ]);
        foreach ($list as $k => $v) {
            $v['text'] = $v[$field['relation_field']];
        }
        return $list;
    }

    /**
     * 图标
     * @return mixed
     */
    public function icons()
    {
        return $this->view->fetch('global/icons');
    }
}
