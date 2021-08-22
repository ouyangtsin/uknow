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
ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use think\facade\Db;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/topthink/framework/src/helper.php';

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__) . DS);
define('INSTALL_PATH', ROOT_PATH .'install' . DS);
define('CONFIG_PATH', ROOT_PATH . 'config' . DS);

if (is_file(INSTALL_PATH . 'lock' . DS . 'install.lock'))
{
    echo '
		<html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        </head>
        <body>
        	你已经安装过该系统，如果想重新安装，请先删除站点install\lock目录下的 install.lock 文件，然后再安装。
        </body>
        </html>';
    exit;
}

if (phpversion() <= '7.1.0') {
    die('本系统需要PHP版本 >= 7.1.0 环境，当前PHP版本为：' . phpversion());
}
$currentHost = ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/';
$check_extension = check_extension([
    //'fileinfo'=>'获取文件的MIME信息',
    'PDO'=>'必须拓展,否则无法正常安装系统',
    'openssl'=>'用于支持SSL传输协议的软件包'
]);

$file_write_enable = file_write_enable(array(
    ROOT_PATH . 'config' . DS,
    ROOT_PATH . 'runtime' . DS,
    ROOT_PATH . 'public' . DS
));

//检查拓展函数
function check_extension($extensions=[]){
    if(empty($extensions))
    {
        return false;
    }
    $return = array();
    foreach($extensions as $key=>$val){
        $extension_loaded_enable = extension_loaded($key) ? 1 : 0;
        $return[$key]=array(
            'extension_name' =>$key,
            'extension_loaded_enable' =>$extension_loaded_enable,
            'remark'=>$val,
            'class'=>$extension_loaded_enable ? '' : 'uk-text-danger',
        );
    }
    return $return;
}

//检查目录是否可读写
function file_write_enable($file_write_enable): array
{
    $return =array();
    foreach ($file_write_enable as $val)
    {
        $return[$val]=array(
            'dir'=>$val,
            'enable'=>isReadWrite($val) ? 1 : 0,
            'error'=>!isReadWrite($val)? '读写权限不足' :'正常'
        );
    }
    return  $return;
}

function isReadWrite($file): bool
{
    if (DIRECTORY_SEPARATOR === '\\') {
        return true;
    }
    if (DIRECTORY_SEPARATOR === '/' && @ ini_get("safe_mode") === false) {
        return is_writable($file);
    }
    if (!is_file($file) || ($fp = @fopen($file, "r+")) === false) {
        return false;
    }
    fclose($fp);
    return true;
}
// POST请求
if (isAjax()) {
    $post = $_POST;
    switch($post['step'])
    {
        case 2:
            $errorInfo = null;

            if (is_file(INSTALL_PATH . 'lock' . DS . 'install.lock'))
            {
                $errorInfo = '已安装系统，如需重新安装请删除文件：/install/lock/install.lock';
            } elseif (!isReadWrite(ROOT_PATH . 'config' . DS)) {
                $errorInfo = ROOT_PATH . 'config' . DS . '：读写权限不足';
            } elseif (!isReadWrite(ROOT_PATH . 'runtime' . DS)) {
                $errorInfo = ROOT_PATH . 'runtime' . DS . '：读写权限不足';
            } elseif (!isReadWrite(ROOT_PATH . 'public' . DS)) {
                $errorInfo = ROOT_PATH . 'public' . DS . '：读写权限不足';
            } elseif (!checkPhpVersion('7.2.0')) {
                $errorInfo = 'PHP版本不能小于7.2.0';
            } elseif (!extension_loaded("PDO")) {
                $errorInfo = '当前未开启PDO，无法进行安装';
            }
            if (!empty($errorInfo)) {
                $data = [
                    'code' => 0,
                    'msg'  => $errorInfo,
                ];
                die(json_encode($data));
            }
            $data = [
                'code' => 1,
                'msg'  => '检测正常',
                'url'  => 'install.php?step=3',
            ];
            die(json_encode($data));
            break;

        case 3:
            $cover = $post['cover'] == 1 ? true : false;
            $database = $post['database'];
            $hostname = $post['hostname'];
            $hostport = $post['hostport'];
            $dbUsername = $post['db_username'];
            $dbPassword = $post['db_password'];
            $prefix = $post['prefix'];
            $adminUrl = $post['admin_url'] ?? 'admin.php';
            $username = $post['username'];
            $password = $post['password'];
            // 参数验证
            $validateError = null;

            // 判断是否有特殊字符
            $check = preg_match('/[0-9a-zA-Z]+$/', $adminUrl, $matches);
            if (!$check) {
                $validateError = '后台地址不能含有特殊字符, 只能包含字母或数字。';
                $data = [
                    'code' => 0,
                    'msg'  => $validateError,
                ];
                die(json_encode($data));
            }

            if (strlen($adminUrl) < 2) {
                $validateError = '后台的地址不能小于2位数';
            } elseif (strlen($password) < 5) {
                $validateError = '管理员密码不能小于5位数';
            } elseif (strlen($username) < 4) {
                $validateError = '管理员账号不能小于4位数';
            }

            if (!empty($validateError)) {
                $data = [
                    'code' => 0,
                    'msg'  => $validateError,
                ];
                die(json_encode($data));
            }

            // DB类初始化
            $config = [
                'type'     => 'mysql',
                'hostname' => $hostname,
                'username' => $dbUsername,
                'password' => $dbPassword,
                'hostport' => $hostport,
                'charset'  => 'utf8',
                'prefix'   => $prefix,
                'debug'    => true,
            ];

            Db::setConfig([
                'default'     => 'mysql',
                'connections' => [
                    'mysql'   => $config,
                    'install' => array_merge($config, ['database' => $database]),
                ],
            ]);

            // 检测数据库连接
            if (!checkConnect()) {
                $data = [
                    'code' => 0,
                    'msg'  => '数据库连接失败',
                ];
                die(json_encode($data));
            }
            // 检测数据库是否存在
            if (!$cover && checkDatabase($database)) {
                $data = [
                    'code' => 0,
                    'msg'  => '数据库已存在，请选择覆盖安装或者修改数据库名',
                ];
                die(json_encode($data));
            }
            // 创建数据库
            createDatabase($database);
            // 导入sql语句等等
            $install = install($username, $password, array_merge($config, ['database' => $database]), $adminUrl);
            if ($install !== true) {
                $data = [
                    'code' => 0,
                    'msg'  => '系统安装失败：' . $install,
                ];
                die(json_encode($data));
            }
            $data = [
                'code' => 1,
                'msg'  => '系统安装成功，正在跳转登录页面',
                'url'  => $currentHost.$adminUrl,
            ];
            die(json_encode($data));
            break;
        case 4:

            break;
    }
}

function isAjax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function isPost()
{
    return ($_SERVER['REQUEST_METHOD'] === 'POST' && checkurlHash($GLOBALS['verify'])
        && (empty($_SERVER['HTTP_REFERER']) || preg_replace("~https?:\/\/([^\:\/]+).*~i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("~([^\:]+).*~", "\\1", $_SERVER['HTTP_HOST']))) ? 1 : 0;
}

function checkPhpVersion($version)
{
    $php_version = explode('-', PHP_VERSION);
    return strnatcasecmp($php_version[0], $version) >= 0;
}

function checkConnect()
{
    try {
        Db::query("select version()");
    } catch (\Exception $e) {
        return false;
    }
    return true;
}

function checkDatabase($database)
{
    $check = Db::query("SELECT * FROM information_schema.schemata WHERE schema_name='{$database}'");
    if (empty($check)) {
        return false;
    }
    return true;
}

function createDatabase($database)
{
    try {
        Db::execute("CREATE DATABASE IF NOT EXISTS `{$database}` DEFAULT CHARACTER SET utf8");
    } catch (\Exception $e) {
        return false;
    }
    return true;
}

function parseSql($sql = '', $to, $from)
{
    [$pure_sql, $comment] = [[], false];
    $sql = explode("\n", trim(str_replace(["\r\n", "\r"], "\n", $sql)));
    foreach ($sql as $key => $line) {
        if ($line == '') {
            continue;
        }
        if (preg_match("/^(#|--)/", $line)) {
            continue;
        }
        if (preg_match("/^\/\*(.*?)\*\//", $line)) {
            continue;
        }
        if (substr($line, 0, 2) === '/*') {
            $comment = true;
            continue;
        }
        if (substr($line, -2) === '*/') {
            $comment = false;
            continue;
        }
        if ($comment) {
            continue;
        }
        if ($from != '') {
            $line = str_replace('`' . $from, '`' . $to, $line);
        }
        if ($line === 'BEGIN;' || $line === 'COMMIT;') {
            continue;
        }
        $pure_sql[] = $line;
    }
    $pure_sql = implode("\n", $pure_sql);
    return explode(";\n", $pure_sql);
}

function install($username, $password, $config, $adminUrl)
{
    $sqlPath = file_get_contents(INSTALL_PATH . 'sql' . DS . 'install.sql');
    $sqlArray = parseSql($sqlPath, $config['prefix'], 'uk_');
    Db::startTrans();
    try {
        foreach ($sqlArray as $vo) {
            Db::connect('install')->execute($vo);
        }

        $uid = Db::connect('install')
            ->name('users')
            ->insertGetId([
                'nick_name'=>$username,
                'user_name'    => $username,
                'avatar'    => '/static/common/image/default-avatar.svg',
                'password'    => password($password),
                'create_time' => time(),
                'status'=>1
            ]);
        if($uid)
        {
            Db::connect('install')
                ->name('auth_group_access')
                ->insertGetId([
                    'uid'=>$uid,
                    'group_id'=>1,
                    'score_group_id'=>1,
                    'power_group_id'=>1,
                    'create_time'=>time()
                ]);

            /*Db::connect('install')
                ->name('config')
                ->where('name','upload_dir')
                ->update(['value'=>public_path().'uploads']);*/
        }
        // 处理安装文件
        !is_dir(INSTALL_PATH) && !mkdir($concurrentDirectory = INSTALL_PATH) && !is_dir($concurrentDirectory);
        !is_dir(INSTALL_PATH . 'lock' . DS) && !mkdir($concurrentDirectory = INSTALL_PATH . 'lock' . DS) && !is_dir($concurrentDirectory);
        @file_put_contents(INSTALL_PATH . 'lock' . DS . 'install.lock', date('Y-m-d H:i:s'));
        @file_put_contents(CONFIG_PATH . 'app.php', getAppConfig($adminUrl));
        @file_put_contents(CONFIG_PATH . 'database.php', getDatabaseConfig($config));
        Db::commit();
    } catch (\Exception $e) {
        Db::rollback();
        return $e->getMessage();
    }
    return true;
}

function password($value)
{
    return password_hash($value, 1);
}

function getAppConfig($admin)
{
    $config = <<<EOT
<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------
use think\\facade\Env;
return [
    // 应用地址
    'app_host'         => Env::get('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 是否启用事件
    'with_event'       => true,
    // 开启应用快速访问
    'app_express'      => true,
    // 默认应用
    'default_app' => 'ask',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 应用映射（自动多应用模式有效）
    'app_map'          => [   
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ['common'],
    // 异常页面的模板文件
	'exception_tmpl' => Env::get('app_debug') ? app()->getThinkPath() . 'tpl/think_exception.tpl' : app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'think_exception.tpl',
	// 跳转页面的成功模板文件
	'dispatch_success_tmpl' => app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
	// 跳转页面的失败模板文件
	'dispatch_error_tmpl' => app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
	// 错误显示信息,非调试模式有效
	'error_message' => '页面错误！请稍后再试～',
	// 显示错误信息
	'show_error_msg' => true,
];
EOT;
    return $config;
}

function getDatabaseConfig($data)
{
    $config = <<<EOT
<?php
use think\\facade\Env;
return [
    // 默认使用的数据库连接配置
    'default'         => 'mysql',
    // 自定义时间查询规则
    'time_query_rule' => [],
    // 自动写入时间戳字段
    // true为自动识别类型 false关闭
    // 字符串则明确指定时间字段类型 支持 int timestamp datetime date
    'auto_timestamp'  => true,
    // 时间字段取出后的默认时间格式
    'datetime_format' => false,
    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'              => 'mysql',
            // 服务器地址
            'hostname'          => '{$data['hostname']}',
            // 数据库名
            'database'          => '{$data['database']}',
            // 用户名
            'username'          => '{$data['username']}',
            // 密码
            'password'          => '{$data['password']}',
            // 端口
            'hostport'          => '{$data['hostport']}',
            // 数据库连接参数
            'params'            => [],
            // 数据库编码默认采用utf8
            'charset'           => Env::get('database.charset', 'utf8'),
            // 数据库表前缀
            'prefix'            => '{$data['prefix']}',
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 是否严格检查字段是否存在
            'fields_strict'     => true,
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 监听SQL
            'trigger_sql'       => true,
            // 开启字段缓存
            'fields_cache'      => false,
            // 字段缓存路径
            'schema_cache_path' => app()->getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR,
        ],
        // 更多的数据库配置信息
    ],
];
EOT;
    return $config;
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>安装程序 - UKnowing问答系统</title>
    <link rel="stylesheet" href="/static/common/css/framework.css?v=<?php echo time();?>>" media="all">
    <link href="/static/common/css/init.css?v=<?php echo time();?>" rel="stylesheet" type="text/css" />
    <link href="/static/libs/layer/theme/default/layer.css?v=<?php echo time();?>" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/static/common/js/jquery.js?v=<?php echo time();?>"></script>
    <script type="text/javascript" src="/static/libs/layer/layer.js?v=<?php echo time();?>"></script>
    <style>
        table{border:1px solid #eee}
        table th{font-size: 1.1rem !important;font-weight: bold !important;}
    </style>
</head>
<body style="background: #f9f9f9">
    <div class="uk-overflow-hidden">
        <div style="width: 800px;margin:0 auto 50px;border-radius: 10px" class="uk-overflow-auto">
            <div class="uk-background-default uk-padding-small">
                <img src="/static/common/image/logo.png" alt="UKnowing" style="height: 50px;">
                <h2 style="margin: 15px 0;color: #999;">一款基于TP6开发的社交化知识付费问答系统</h2>
            </div>
            <div class="uk-background-default">
                <nav class="responsive-tab style-5">
                    <ul>
                        <li <?php if(!$_GET['step'] || $_GET['step']==1) {?>class="uk-active"<?php } ?>><a href="install.php"> 许可协议 </a></li>
                        <li <?php if($_GET['step']==2) {?>class="uk-active"<?php } ?>><a href="javascript:;"> 环境检测 </a></li>
                        <li <?php if($_GET['step']==3) {?>class="uk-active"<?php } ?>><a href="javascript:;"> 参数配置 </a></li>
                        <li <?php if($_GET['step']==4) {?>class="uk-active"<?php } ?>><a href="javascript:;"> 安装完成 </a></li>
                    </ul>
                </nav>
            </div>
            <div class="uk-padding-small uk-background-default" style="margin-top: 5px">
                <?php if(!$_GET['step'] || $_GET['step']==1) {?>
                    <div class="step1">
                        <div class="uk-form-group"><h3>阅读许可协议</h3></div>
                        <div class="uk-form-group uk-overflow-auto" style="height: 400px;border:1px solid #eee;padding: 10px 15px">
                            <p>版权所有UKnowing社交化知识付费问答系统保留所有权利。 </p>
                            <p>感谢您选UKnowing社交化知识付费问答系统管理系统（以下简称UKnowing），UKnowing是目前国内最强大、最稳定的中小型门户网站建设解决方案之一，基于 PHP + MySQL   的技术开发</p>
                            <p>UKnowing的官方网址是： <a href="https://www.uknowing.com" target="_blank">UKnowing</a> 交流论坛：<a href="https://ask.uknowing.com" target="_blank"></a></p>
                            <p>为了使你正确并合法的使用本软件，请你在使用前务必阅读清楚下面的协议条款：</p>
                            <strong>一、本授权协议适用且仅适用于 版权所有UKnowing社交化知识付费问答系统保留所有权利版本，官方对本授权协议的最终解释权。</strong>
                            <strong>二、协议许可的权利 </strong>
                            <p>1、您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途，而不必支付软件版权授权费用。 </p>
                            <p>2、您可以在协议规定的约束和限制范围内修改，UKnowing源代码或界面风格以适应您的网站要求。 </p>
                            <p>3、您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。 </p>
                            <p>4、获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。 </p>
                            <strong>二、协议规定的约束和限制 </strong>
                            <p>1、未获商业授权之前，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目的或实现盈利的网站）。购买商业授权请登陆   <a href="https://www.uknowing.com" target="_blank">UKnowing</a> 了解最新说明。</p>
                            <p>2、未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</p>
                            <p>3、不管你的网站是否整体使用 ，还是部份栏目使用，在你使用了UKnowing的网站主页上必须加上UKnowing的官方网址(<a href="https://www.uknowing.com" target="_blank">UKnowing</a>)的链接。</p>
                            <p>4、未经官方许可，禁止在UKnowing的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</p>
                            <p>5、如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。 </p>
                            <strong>三、有限担保和免责声明 </strong>
                            <p>1、本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。 </p>
                            <p>2、用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。 </p>
                            <p>3、电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装   UKnowing即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</p>
                            <p>4、如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</p>
                            <p><b>协议发布时间：</b> 2020年12月18日</p>
                            <p><b>版本最新更新：</b> 2020年12月18日 </p>
                        </div>
                        <div class="uk-form-group uk-overflow-hidden">
                            <div class="uk-float-left">
                                <label><input name="accept" type="checkbox"  value="1" class="check_boxId uk-checkbox" /> <strong>我已经阅读并同意此协议</strong></label>
                            </div>
                            <div class="uk-float-right">
                                <input name="继续" type="button" class="uk-button-primary uk-button uk-button-small step1-btn" value="继续"  />
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if($_GET['step']==2) {?>
                    <div class="step2">
                        <div class="uk-form-group"><h3>服务器信息</h3></div>
                        <table class="uk-table uk-table-divider uk-table-striped" >
                            <thead>
                            <tr>
                                <th>参数</th>
                                <th>值</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>服务器域名</td>
                                <td style=" color:#999;"><?php echo $currentHost;?></td>
                            </tr>
                            <tr>
                                <td>服务器操作系统</td>
                                <td style=" color:#999;"><?php echo php_uname() ;?></td>
                            </tr>
                            <tr>
                                <td>服务器翻译引擎</td>
                                <td style="color:#999;"><?php echo $_SERVER['SERVER_SOFTWARE'] ;?></td>
                            </tr>
                            <tr>
                                <td>PHP版本</td>
                                <td style="color:#999;"><?php echo PHP_VERSION ;?></td>
                            </tr>
                            <tr>
                                <td>系统安装目录</td>
                                <td style=" color:#999;"><?php echo ROOT_PATH ;?></td>
                            </tr>
                            </tbody>
                        </table>

                        <div class="uk-form-group"><h3>系统环境检测</h3></div>
                        <table class="uk-table uk-table-divider uk-table-striped">
                            <thead>
                            <tr>
                                <th>需要开启的拓展或函数</th>
                                <th>开启状态</th>
                                <th>开启建议</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($check_extension as $key=>$val){?>
                            <tr class="<?php echo $val['class'];?>">
                                <td><?php echo $val['extension_name'];?></td>
                                <td><?php echo $val['extension_loaded_enable'] ? '<img src="/static/common/image/success.svg" width="16" height="16">':'<img src="/static/common/image/error.svg" width="16" height="16">';?></td>
                                <td>(<?php echo $val['remark'];?>)</td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <div class="uk-form-group"><h3>目录权限检测</h3></div>
                        <table class="uk-table uk-table-divider uk-table-striped">
                            <thead>
                            <tr>
                                <th>目录名</th>
                                <th>读写权限</th>
                                <th>提示信息</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($file_write_enable as $key=>$val){?>
                            <tr>
                                <td><?php echo $val['dir'];?></td>
                                <td><?php echo $val['enable'] ? '<img src="/static/common/image/success.svg" width="16" height="16">':'<img src="/static/common/image/error.svg" width="16" height="16">';?></td>
                                <td><?php echo $val['error'];?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                        <form action="" method="post">
                            <div class="uk-form-group uk-overflow-hidden">
                                <input type="hidden" name="step" value="2">
                                <div class="uk-float-left">
                                    <a href="javascript:history.back();" class="uk-button-danger uk-button">后退</a>
                                </div>
                                <div class="uk-float-right">
                                    <input name="继续" type="button" class="uk-button-primary uk-button ajax-form step2-btn" value="继续"  />
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } ?>

                <?php if($_GET['step']==3) {?>
                    <div class="step3 box">
                        <form action="install.php?step=3" method="post">
                            <div class="uk-form-group">
                                <label class="uk-form-label">数据库地址</label>
                                <input class="uk-input" name="hostname"  placeholder="请输入数据库地址" value="127.0.0.1">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">数据库端口</label>
                                <input class="uk-input" name="hostport"  placeholder="请输入数据库端口" value="3306">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">数据库名称</label>
                                <input class="uk-input" name="database"  placeholder="请输入数据库名称" value="uknowing">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">数据表前缀</label>
                                <input class="uk-input" name="prefix"  placeholder="请输入数据表前缀" value="uk_">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">数据库账号</label>
                                <input class="uk-input" name="db_username" placeholder="请输入数据库账号" value="root">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">数据库密码</label>
                                <input type="password" class="uk-input" name="db_password" placeholder="请输入数据库密码">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">覆盖数据库</label>
                                <div class="uk-input-block" style="text-align: left">
                                    <input type="radio" name="cover" value="1">覆盖
                                    <input type="radio" name="cover" value="0" checked>不覆盖
                                </div>
                            </div>
                            <!--<div class="uk-form-group">
                                <label class="uk-form-label">后台的地址</label>
                                <input class="uk-input" id="admin_url" name="admin_url" placeholder="为了后台安全，不建议将后台路径设置为admin" value="admin">
                                <span class="tips">后台登录地址： <?php /*echo $currentHost; */?><span id="admin_name">admin</span></span>
                            </div>-->
                            <div class="uk-form-group">
                                <label class="uk-form-label">管理员账号</label>
                                <input class="uk-input" name="username" placeholder="请输入管理员账号" value="admin">
                            </div>
                            <div class="uk-form-group">
                                <label class="uk-form-label">管理员密码</label>
                                <input type="password" class="uk-input" name="password" placeholder="请输入管理员密码">
                            </div>
                            <div class="uk-form-group">
                                <input type="hidden" name="step" value="3">
                                <input name="继续" type="button" class="uk-button-primary uk-button ajax-form step2-btn" value="确定安装"  />
                            </div>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(e) {
            $(".step1-btn").click(function(){
                if($(".check_boxId").is(":checked")){
                    window.location.href="install.php?step=2";
                }else
                {
                    layer.msg("请同意安装协议");
                }
            });
        });

        $(document).on('click', '.ajax-form', function (e) {
            const that = this;
            const form = $($(that).parents('form')[0]);
            var loading = layer.msg('正在安装...', {
                icon: 16,
                shade: 0.2,
                time: false
            });
            $.ajax({
                url:form.attr('action'),
                dataType: 'json',
                type:'post',
                data:form.serialize(),
                success: function (result)
                {
                    layer.closeAll();
                    const msg = result.msg ? result.msg : '操作成功';
                    if(result.code> 0)
                    {
                        window.location.href = result.url;
                    }else{
                        layer.msg(msg,{},function (){
                            window.location.reload();
                        });
                    }
                },
                error:  function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        layer.msg('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        });
    </script>
</body>
</html>