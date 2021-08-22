<?php

namespace app\common\library\helper;

use app\admin\model\Module;
use app\common\model\AuthRule;
use Exception;
use think\facade\Cache;
use think\db\exception\PDOException;
use think\facade\Config;
use think\facade\Db;

/**
 * 模块管理
 * Class ModuleHelper
 * @package app\common\library\helper
 */
class ModuleHelper
{
    // 模块目录
    public $modulePath='';

    // 模块安装需要复制的文件夹
    public $copyDirs = [];
    private static $instance;
    public $error;

    // 构造方法
    public function __construct()
    {
        $this->modulePath = app()->getRootPath() . 'app';
        $this->copyDirs = [
            'theme',
            'plugins'
        ];
    }

    public static function instance(): ModuleHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 获得本地模块列表
    public function getModuleList($status=0): array
    {
        $deny_module = config('uk.modules');
        $map                = [];
        $map['status']      = $status;
        $map['system']      = 0;

        $module_list = db('module')->where($map)
            ->order('sort,id')->column('id,title,author,intro,icon,default,system,identifier,config,name,version,status');
        $list = [];

        if(!$status)
        {
            $allModule  = db('module')->order('sort,id')->column('id,name', 'name');
            $modules = scandir($this->modulePath);
            foreach ($modules as $name) {
                if ($name === '.' || $name === '..') {
                    continue;
                }

                // 排除系统模块和已存在数据库的模块
                if (in_array($name, $deny_module) || array_key_exists($name, $allModule))
                {
                    continue;
                }

                $moduleDir = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
                if (!is_dir($moduleDir)) {
                    continue;
                }

                if (file_exists($moduleDir.'info.php'))
                {
                    // 获取模块基础信息
                    $info = include $moduleDir.'info.php';
                    $sql                = [];
                    $sql['name']        = $info['name'];
                    $sql['identifier']  = $info['identifier'];
                    $sql['theme']       = $info['theme'];
                    $sql['title']       = $info['title'];
                    $sql['intro']       = $info['intro'];
                    $sql['author']      = $info['author'];
                    $sql['icon']        = '/'.substr($info['icon'], 1);
                    $sql['version']     = $info['version'];
                    $sql['url']         = $info['author_url'];
                    $sql['config']      = '';
                    $sql['status']      = 0;
                    $sql['default']     = 0;
                    $sql['system']      = 0;
                    $sql['app_keys']    = '';
                    $db = Module::create($sql);
                    $sql['id'] = $db->id;
                    $module_list = array_merge($module_list, [$sql]);
                }
            }
        }

        foreach ($module_list as $key=> $val)
        {
            // 增加右侧按钮组
            $str = '';
            if(!in_array($val['name'],['ask']))
            {
                if ($val['status'] == 1) {
                    // 已安装，增加配置按钮
                    $str .= '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="UK.operate.edit(\''.$val['name'].'\')"><i class="fa fa-edit"></i> 配置</a> ';
                    $str .= '<a class="btn btn-primary btn-xs uk-ajax-open" href="javascript:void(0)" data-title="['.$val['title'].'] 主题设置" data-url="'.url('admin/module.Theme/index',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 主题</a> ';
                    $str .= '<a class="btn btn-danger btn-xs uk-ajax-get" data-confirm="是否卸载该模块？" data-url="'.url('uninstall',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 卸载</a> ';
                    $str .= '<a class="btn btn-danger btn-xs uk-ajax-get" data-confirm="是否禁用该模块？" data-url="'.url('status',['name'=>$val['name'],'status'=>2]).'"><i class="fa fa-edit"></i> 禁用</a> ';
                }else if($val['status'] == 2){
                    $str .= '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="UK.operate.edit(\''.$val['name'].'\')"><i class="fa fa-edit"></i> 配置</a> ';
                    $str .= '<a class="btn btn-primary btn-xs uk-ajax-open" href="javascript:void(0)" data-title="['.$val['title'].'] 主题设置" data-url="'.url('admin/module.Theme/index',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 主题</a> ';
                    $str .= '<a class="btn btn-danger btn-xs uk-ajax-get" data-confirm="是否卸载该模块？"  data-url="'.url('uninstall',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 卸载</a> ';
                    $str .= '<a class="btn btn-danger btn-xs uk-ajax-get" data-confirm="是否启用该模块？"  data-url="'.url('status',['name'=>$val['name'],'status'=>1]).'"><i class="fa fa-edit"></i>启用</a> ';
                } else {
                    // 未安装，增加安装按钮
                    $str = '<a class="btn btn-primary btn-xs uk-ajax-get" data-url="'.url('install',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 安装</a>';
                }
            }else{
                $str .= '<a class="btn btn-primary btn-xs uk-ajax-open" href="javascript:void(0)" data-title="['.$val['title'].'] 主题设置" data-url="'.url('admin/module.Theme/index',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 主题</a> ';
            }
            $val['button'] = $str;
            $list[$key] = $val;
        }
        return $list;
    }

    // 获取模块信息
    public function config(string $name)
    {
        return $this->getConfig($name,true);
    }

    // 保存模块信息
    public function configPost(array $data = []): array
    {
        $check = $this->check($data['name']);
        if ($check !== true) {
            return [
                'code' => 0,
                'msg' => $this->getError()
            ];
        }
        // 获取模块配置信息
        $config = $this->getConfig($data['name']);

        // 判断是否分组
        $group = $this->checkConfigGroup($config);

        if ($data) {
            if ($group) {
                // 开启分组
                foreach ($config as $k => $v) {
                    foreach ($v as $kk => $vv) {
                        if (isset($data[$kk])) {
                            $value = is_array($data[$kk]) ? implode(',', $data[$kk]) : ($data[$kk] ?? $vv['value']);
                            $config[$k][$kk]['value'] = $value;
                        }
                    }
                }
            } else {
                // 未开启分组
                foreach ($config as $k => $v) {
                    if (isset($data[$k])) {
                        $value = is_array($data[$k]) ? implode(',', $data[$k]) : ($data[$k] ?? $v['value']);
                        $config[$k]['value'] = $value;
                    }
                }
            }
        }
        // 更新配置文件
        $result = $this->setPluginConfig($data['name'], $config);
        if ($result['code'] == 1) {
            return [
                'code' => 1,
                'msg'  => '保存成功!'
            ];
        } else {
            return [
                'code' => 0,
                'msg'  => $result['msg']
            ];
        }
    }

    // 安装模块
    public function install(string $name): array
    {
        $db_info = db('module')->where('name', $name)->find();
        if (!$db_info) {
            $this->setError('模块不存在');
            return false;
        }

        if ($db_info['status'] > 0) {
            $this->setError('请勿重复安装此模块');
            return false;
        }

        $moduleDir = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        if (!file_exists($moduleDir.'info.php')) {
            $this->setError('模块配置文件不存在[info.php]');
            return false;
        }

        $info = include $moduleDir.'info.php';

        try {
            // 导入SQL
            $this->importSql($name);

            // 复制文件
            $this->copyDir($name);

            // 导入菜单
            if (file_exists($moduleDir.'menu.php') ) {
                $menu_config = include $moduleDir.'menu.php';
                // 如果不是数组且不为空就当JSON数据转换
                if (!is_array($menu_config) && !empty($menu_config)) {
                    $menu_config = json_decode($menu_config, 1);
                }

                if(!empty($menu_config))
                {
                    if(isset($menu_config['is_nav']) && $menu_config['is_nav']===1){
                        $pid = 0;
                    }else{
                        $pid = $menu_config['pid'] ?? db('auth_rule')->where(['name'=>'module'])->value('id');
                    }
                    $menu[] = $menu_config['menu'];
                    //导入菜单
                    $this->addMenu($menu,$pid);
                }
            }

            // 导入模块配置
            if (isset($info['config']) && !empty($info['config'])) {
                db('module')->where('name', $name)->update(['config'=>json_encode($info['config'], JSON_UNESCAPED_UNICODE)]);
            }
            //更新模块状态
            db('module')->where('name', $name)->update(['status'=>1]);
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '安装失败：' . $e->getMessage(),
            ];
        }

        return [
            'code' => 1,
            'msg'  => '模块安装成功',
        ];
    }

    // 卸载模块
    public function uninstall(string $name): array
    {
        $db_info = db('module')->where('name', $name)->find();
        if (!$db_info) {
            $this->setError('模块不存在');
            return false;
        }

        if (!$db_info['status']) {
            $this->setError('模块未安装');
            return false;
        }

        $moduleDir = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;

        if (!file_exists($moduleDir.'info.php')) {
            $this->setError('模块配置文件不存在[info.php]');
            return false;
        }

        try {
            //执行卸载sql
            $this->uninstallSql($name);

            //执行卸载菜单
            if (file_exists($moduleDir . 'menu.php')) {
                $menu_config = include $moduleDir . 'menu.php';
                if (!empty($menu_config)) {
                    $menu[] = $menu_config['menu'];
                    $this->removeMenu($menu);
                }
            }
            //删除文件
            $this->removeDir($name);

            //更新模块状态
            db('module')->where('name', $name)->update(['status'=>0]);

        }catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '模块卸载失败：' . $e->getMessage(),
            ];
        }
        return [
            'code' => 1,
            'msg'  => '模块卸载成功',
        ];
    }

    // 启用/禁用模块
    public function changeStatus(string $name,$status=0): array
    {
        $db_info = db('module')->where('name', $name)->find();
        if (!$db_info) {
            $this->setError('模块不存在');
            return false;
        }

        if (!$db_info['status']) {
            $this->setError('模块未安装');
            return false;
        }

        try {
            $result = db('module')->where('name', $name)->update(['status'=>$status]);
            if ($result) {
                return [
                    'code' => 1,
                    'msg'  => '状态变动成功',
                ];
            }
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '状态变动失败：' . $e->getMessage(),
            ];
        }
        return [
            'code' => 1,
            'msg'  => '状态变动成功',
        ];
    }

    // 判断模块配置文件是否进行了分组
    public function checkConfigGroup($config): bool
    {
        if(!$config) {
            return false;
        }
        // 获取第一个元素
        //$arrayShift = array_shift($config);
        if (array_key_exists('title', $config) && array_key_exists('type', $config)) {
            // 未开启分组
            return false;
        } else {
            // 开启分组
            return true;
        }
    }

    // 验证模块是否完整
    private function check(string $name): bool
    {
        if (!is_dir($this->modulePath . DIRECTORY_SEPARATOR . $name)) {
            $this->setError('未发现该模块,请先下载并放入到app目录中');
            return false;
        }
        return true;
    }

    /**
     * 获取配置列表
     * @param string $name
     * @param bool $update
     * @return mixed
     */
    public function getConfig(string $name='', bool $update=false)
    {
        $result = Cache::get('module_config');
        if (!$result || $update) {
            $rows = db('module')->where('status', 1)->column('name,config', 'name');
            $result = [];
            foreach ($rows as $k => $r) {
                if (!$r['config']) {
                    continue;
                }
                $config = json_decode($r['config'], true);
                if (!is_array($config)) {
                    continue;
                }
                $result[$r['name']] = $config;
            }

            Cache::tag('uk_module')->set('module_config', $result);
        }
        return $name ? $result[$name] : $result;
    }

    /**
     * 解析配置为键值对
     * @param string $name
     * @param string $configName
     * @param int $update
     * @return array|false
     */
    public function getModuleConfigs(string $name, string $configName = '', int $update=0,$extend_name=false)
    {
        $config = Cache::get('cache_modules_config_'.$name, []);
        if ($config && !$update) {
            return $configName ? $config[$configName] : $config;
        }
        if(!$config = $this->getConfig($name,true)) {
            return false;
        }
        $newConfig = [];
        if($this->checkConfigGroup($config))
        {
            foreach ($config as $key=>$val)
            {
                foreach ($val as $k=>$v)
                {
                    if (in_array($v['type'], ['select','checkbox'])) {
                        $v['value'] = explode(',', $v['value']);
                    }
                    if ($v['type'] == 'array') {
                        $v['value'] = json_decode($v['options'],true);
                    }
                    if($extend_name)
                    {
                        $newConfig[$name.'_'.$k]= $v['value'];
                    }else{
                        $newConfig[$k]= $v['value'];
                    }
                }
            }
        }else{
            foreach ($config as $key=>$val)
            {
                if (in_array($val['type'], ['select','checkbox'])) {
                    $val['value'] = explode(',', $val['value']);
                }

                if ($val['type'] == 'array') {
                    $v['value'] = json_decode($val['options'],true);
                }

                if($extend_name)
                {
                    $newConfig[$name.'_'.$key]= $val['value'];
                }else{
                    $newConfig[$key]= $val['value'];
                }
            }
        }

        Cache::set( 'cache_modules_config_'.$name,$newConfig);
        return $configName ? $newConfig[$configName] : $newConfig;
    }

    public function getAllModuleConfigs()
    {
        $rows = db('module')->where('status', 1)->column('name,config', 'name');
        $result = [];
        foreach ($rows as $k => $r) {
            if (!$r['config']) {
                continue;
            }
            $config = $this->getModuleConfigs($r['name'],'',1,1);
            if (!is_array($config)) {
                continue;
            }
            $result = array_merge($result,$config);
        }
        return $result;
    }

    /**
     * 更新模块的配置文件
     * @param string $name 模块名
     * @param array $array
     * @return mixed
     */
    private function setModuleConfig(string $name, array $array = []): array
    {
        Cache::set( 'cache_modules_config_'.$name,null);
        $file = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.php';
        if (!$this->checkFileWritable($file)) {
            return [
                'code' => 0,
                'msg' => '文件没有写入权限',
            ];
        }
        if ($handle = fopen($file, 'w')) {
            fwrite($handle, "<?php\n\n" . "return " . var_export($array, TRUE) . ";\n");
            fclose($handle);
        } else {
            return [
                'code' => 0,
                'msg'  => '文件没有写入权限',
            ];
        }
        return [
            'code' => 1,
            'msg' => '文件写入完毕',
        ];
    }

    /**
     * 判断文件或目录是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    private function checkFileWritable(string $file): bool
    {
        if (is_dir($file)) {
            // 判断目录是否可写
            return is_writable($file);
        } elseif (file_exists($file)) {
            // 文件存在则判断文件是否可写
            return is_writable($file);
        } else {
            // 文件不存在则判断当前目录是否可写
            $file = pathinfo($file, PATHINFO_DIRNAME);
            return is_writable($file);
        }
    }

    /**
     * 导入SQL
     * @param string $name
     * @param string $fileName
     */
    private function importSql(string $name,string $fileName='')
    {
        $fileName = $fileName!=='' ? $fileName :'install.sql';
        $sqlFile = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                    continue;
                }

                $tempLine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    // 不区分大小写替换前缀
                    $tempLine = str_ireplace('uk_', Config::get('database.connections.mysql.prefix'), $tempLine);
                    // 忽略数据库中已经存在的数据
                    $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                    try {
                        Db::execute($tempLine);
                    } catch (\PDOException $e) {
                        //$e->getMessage();
                    }
                    $tempLine = '';
                }
            }
        }
    }

    //卸载SQL
    private function uninstallSql(string $name): bool
    {
        $sqlFile = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'uninstall.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                    continue;
                }

                $tempLine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    // 不区分大小写替换前缀
                    $tempLine = str_ireplace('__PREFIX__', Config::get('database.connections.mysql.prefix'), $tempLine);
                    // 忽略数据库中已经存在的数据
                    $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                    try {
                        Db::execute($tempLine);
                    } catch (\PDOException $e) {
                        //$e->getMessage();
                    }
                    $tempLine = '';
                }
            }
        }
        return true;
    }

    // 导入菜单
    private function addMenu($menu=[],$pid=0)
    {
        foreach ($menu as $k=>$v)
        {
            $hasChild = isset($v['menu_list']) && $v['menu_list'];
            try {
                $v['pid'] = $pid ;
                $v['name'] = trim($v['name'],'/');
                if(AuthRule::where('name',$v['name'])->find()){
                    continue;
                }
                $menu = AuthRule::create($v);
                if ($hasChild) {
                    $this->addMenu($v['menu_list'], $menu['id']);
                }
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    //删除菜单
    private function removeMenu($menu)
    {
        foreach ($menu as $k=>$v){
            $hasChild = isset($v['menu_list']) && $v['menu_list'];
            try {
                $menu_rule = AuthRule::where('name',$v['name'])->find();
                if($menu_rule){
                    $menu_rule->delete();
                    if ($hasChild) {
                        $this->removeMenu($v['menu_list']);
                    }
                }
            } catch (\PDOException $e) {
                $this->setError($e->getMessage());
                return false;
            }
        }
    }

    // 安装时复制资源文件
    private function copyDir(string $name)
    {
        $moduleDir = $this->modulePath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        if (is_dir($moduleDir . 'theme'))
        {
            FileHelper::copyDir($moduleDir . 'theme', app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'templates'.DIRECTORY_SEPARATOR.$name);
        }

        if (is_dir($moduleDir . 'plugins'))
        {
            FileHelper::copyDir($moduleDir . 'plugins', app()->getRootPath() . 'plugins'.DIRECTORY_SEPARATOR.$name);
        }
    }

    // 卸载时删除文件
    private function removeDir(string $name)
    {
        //删除模块主题
        $templates_dest = app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'templates'.DIRECTORY_SEPARATOR.$name;
        if (is_dir($templates_dest))
        {
            FileHelper::delDir($templates_dest);
        }

        //删除模块插件
        $plugins_dest = app()->getRootPath() . 'plugins'.DIRECTORY_SEPARATOR.$name;
        if (is_dir($plugins_dest))
        {
            FileHelper::delDir($plugins_dest);
        }
    }

    // 生成表单信息
    public function makeAddColumns($config): array
    {
        if(!$config)
        {
            return false;
        }
        // 判断是否开启了分组
        if (!$this->checkConfigGroup($config)) {
            // 未开启分组
            return $this->makeAddColumnsArr($config);
        } else {
            $columns = [];
            // 开启分组
            foreach ($config as $k => $v) {
                $columns[$k] = $this->makeAddColumnsArr($v);
            }
            return $columns;
        }
    }

    // 生成表单返回数组
    public function makeAddColumnsArr(array $config): array
    {
        if(!$config)
        {
            return  false;
        }
        $columns = [];
        foreach ($config as $k => $field) {
            // 初始化
            $field['name'] = $field['name'] ?? $field['title'];
            $field['field'] = $k;
            $field['tips'] = $field['tips'] ?: '';
            $field['required'] = $field['required'] ?? 0;
            $field['group'] = $field['group'] ?? '';
            if (!isset($field['setup'])) {
                $field['setup'] = [
                    'default' => $field['value'] ?: '',
                    'extra_attr' => $field['extra_attr'] ?? '',
                    'extra_class' => $field['extra_class'] ?? '',
                    'placeholder' => $field['placeholder'] ?? '',
                ];
            }

            if ($field['type'] == 'text') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['setup']['default'],    // 默认值
                    $field['group'],               // 标签组，可以在文本框前后添加按钮或者文字
                    $field['setup']['extra_attr'], // 额外属性
                    $field['setup']['extra_class'],// 额外CSS
                    $field['setup']['placeholder'],// 占位符
                    $field['required'],            // 是否必填
                ];
            }
            elseif ($field['type'] == 'textarea' || $field['type'] == 'password') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['extra_attr'],        // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['setup']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'radio' || $field['type'] == 'checkbox') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['options'],             // 选项（数组）
                    $field['setup']['default'],    // 默认值
                    $field['setup']['extra_attr'], // 额外属性 extra_attr
                    '',                            // 额外CSS extra_class
                    $field['required'],            // 是否必填
                ];
            }
            elseif ($field['type'] == 'select' || $field['type'] == 'select2' ) {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['options'],                    // 选项（数组）
                    $field['setup']['default'],           // 默认值
                    $field['setup']['extra_attr'],        // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['setup']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'number') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    '',                                   // 最小值
                    '',                                   // 最大值
                    $field['setup']['step'],              // 步进值
                    $field['setup']['extra_attr'],        // 额外属性
                    $field['setup']['extra_class'],       // 额外CSS
                    $field['setup']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'hidden') {
                $columns[] = [
                    $field['type'],                      // 类型
                    $field['field'],                     // 字段名称
                    $field['setup']['default'] ?? '',    // 默认值
                    $field['setup']['extra_attr'] ?? '', // 额外属性 extra_attr
                ];
            }
            elseif ($field['type'] == 'date' || $field['type'] == 'time' || $field['type'] == 'datetime') {
                // 使用每个字段设定的格式
                if ($field['type'] == 'time') {
                    $field['setup']['format'] = str_replace("HH", "h", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("mm", "i", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("ss", "s", $field['setup']['format']);
                    $format = $field['setup']['format'] ?? 'H:i:s';
                } else {
                    $field['setup']['format'] = str_replace("yyyy", "Y", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("mm", "m", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("dd", "d", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("hh", "h", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("ii", "i", $field['setup']['format']);
                    $field['setup']['format'] = str_replace("ss", "s", $field['setup']['format']);
                    $format = $field['setup']['format'] ?? 'Y-m-d H:i:s';
                }
                $field['setup']['default'] = $field['setup']['default'] > 0 ? date($format, $field['setup']['default']) : '';
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['setup']['default'],    // 默认值
                    $field['setup']['format'],     // 日期格式
                    $field['setup']['extra_attr'], // 额外属性 extra_attr
                    '',                            // 额外CSS extra_class
                    $field['setup']['placeholder'],// 占位符
                    $field['required'],            // 是否必填
                ];
            }
            elseif ($field['type'] == 'daterange') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['format'],            // 日期格式
                    $field['setup']['extra_attr'] ?? '',  // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'tag') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['extra_attr'] ?? '',  // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'image' || $field['type'] == 'images' || $field['type'] == 'file' || $field['type'] == 'files') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['size'],              // 限制大小（单位kb）
                    $field['setup']['ext'],               // 文件后缀
                    $field['setup']['extra_attr'] ?? '',  // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['setup']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'editor') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['heidht'] ?? 0,       // 高度
                    $field['setup']['extra_attr'] ?? '',  // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'color') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['extra_attr'] ?? '',  // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['setup']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
        }
        return $columns;
    }

    //设置错误信息
    public function setError($error)
    {
        $this->error = $error;
    }

    //获取错误信息
    public function getError()
    {
        return $this->error;
    }
}