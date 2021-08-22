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


namespace app\admin\model;
use app\common\library\helper\AuthHelper;
use think\facade\Request;
use think\facade\Session;
use think\Model;

class AdminLog extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function admin()
    {
        return $this->belongsTo('app\common\model\Users', 'uid');
    }

    // 管理员日志记录
    public static function record()
    {
        // 入库信息
        $adminId   = Session::get('admin_user_info.uid',0);
        $url       = Request::url();
        $title     = '';
        $content   = Request::except(['s','_pjax']); //s 变量为系统内置的变量，_pjax为js的变量，无记录的必要
        $ip        = Request::ip();
        $userAgent = Request::server('HTTP_USER_AGENT');

        // 标题处理
        $auth = AuthHelper::instance();
        $titleArr = $auth->getBreadCrumb();
        if (is_array($titleArr)) {
            foreach ($titleArr as $k => $v) {
                $title = '[' . $v['title'] . '] -> ' . $title;
            }
            $title = substr($title, 0, strlen($title) - 4);
        }

        // 内容处理(过长的内容和涉及密码的内容不进行记录)
        if ($content) {
            foreach ($content as $k => $v) {
                if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {
                    unset($content[$k]);
                }
            }
        }

        // 登录处理
        if (strpos($url, 'admin/index/login') !== false) {
            $title = '[登录成功]';
            $content = '';
        }

        // 插入数据
        if (!empty($title)) {
            // 查询管理员上一条数据
            $result = self::where('uid', '=', $adminId)->order('id', 'desc')->find();
            if ($result) {
                if ($result->url != $url) {
                    self::create([
                        'title'       => $title ? $title : '',
                        'content'     => !is_scalar($content) ? json_encode($content) : $content,
                        'url'         => $url,
                        'uid'    => $adminId,
                        'user_agent'   => $userAgent,
                        'ip'          => $ip
                    ]);
                }
            } else {
                self::create([
                    'title'       => $title ? $title : '',
                    'content'     => !is_scalar($content) ? json_encode($content) : $content,
                    'url'         => $url,
                    'uid'    => $adminId,
                    'user_agent'   => $userAgent,
                    'ip'          => $ip
                ]);
            }
        }
    }
}