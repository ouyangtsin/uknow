<?php

namespace app\common\library\helper;

use app\common\model\AuthRule;
use Exception;
use think\facade\Cache;
use think\db\exception\PDOException;
use think\facade\Config;
use think\facade\Db;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class PluginsHelper
{
    // 插件目录
    protected $addonsPath = '';

    // 插件安装需要复制的文件夹
    protected $copyDirs = [];

    private static $instance;

    // 构造方法
    public function __construct()
    {
        $this->addonsPath = app()->getRootPath() . 'plugins';
        $this->copyDirs = [
            'static',
        ];
    }

    public static function instance(): PluginsHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 获得本地插件列表
    public function localAddons(): array
    {
        $plugins = scandir($this->addonsPath);
        $list = [];
        foreach ($plugins as $name) {
            if ($name === '.' or $name === '..')
                continue;
            if (is_file($this->addonsPath . DIRECTORY_SEPARATOR . $name))
                continue;
            $addonDir = $this->addonsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
            if (!is_dir($addonDir))
                continue;
            $object = $this->getInstance($name);
            if ($object) {
                // 获取插件基础信息
                $info = $object->getInfo();

                // 增加右侧按钮组
                $str = '';
                if ($info['install'] == 1) {
                    // 已安装，增加配置按钮
                    $str .= '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="UK.operate.edit(\''.$name.'\')"><i class="fa fa-edit"></i> 配置</a> ';
                    $str .= '<a class="btn btn-danger btn-xs confirm" href="javascript:void(0)" onclick="pluginUninstall(\'' . $name . '\')"><i class="fa fa-edit"></i> 卸载</a> ';
                } else {
                    // 未安装，增加安装按钮
                    $str = '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="pluginInstall(\'' . $name . '\')"><i class="fa fa-edit"></i> 安装</a>';
                }

                $info['button'] = $str;

                $list[] = $info;
            }
        }
        return $list;
    }

    // 获取插件信息
    public function config(string $name)
    {
        $check = $this->check($name);
        if ($check !== true) {
            return [
                'code' => 0,
                'msg' => $check
            ];
        }
        return $this->getConfig($name);
    }

    // 保存插件信息
    public function configPost(array $data = []): array
    {
        $check = $this->check($data['id']);
        if ($check !== true) {
            return [
                'code' => 0,
                'msg' => $check
            ];
        }
        // 实例化插件
        $object = $this->getInstance($data['id']);
        if ($object) {
            // 获取插件配置信息
            $config = $this->getConfig($data['id']);
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
            $result = $this->setPluginConfig($data['id'], $config);
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
        } else {
            return [
                'code' => 0,
                'msg' => '插件实例化失败'
            ];
        }
    }

    // 启用插件或禁用插件
    public function state(string $name): array
    {
        $check = $this->check($name);
        if ($check !== true) {
            return [
                'code' => 0,
                'msg' => $check
            ];
        }
        // 实例化插件
        $object = $this->getInstance($name);
        // 获取插件基础信息
        $info = $object->getInfo();
        if (!$info) {
            return [
                'code' => 0,
                'msg'  => '未找到该插件的信息'
            ];
        } else {
            // 请先安装
            if ($info['install'] != 1) {
                return [
                    'code' => 0,
                    'msg'  => '请先安装该插件',
                ];
            } else {
                return $this->changeStatus($name);
            }
        }
    }

    // 安装插件
    public function install(string $name): array
    {
        // 实例化插件
        $object = $this->getInstance($name);
        // 获取插件基础信息
        $info = $object->getInfo();
        if (false !== $object->install()) {
            $info['status'] = 1;
            $info['install'] = 1;
            try {
                // 更新或创建插件的ini文件
                $result = $this->setPluginIni($name, $info);
                if ($result['code'] == 0) {
                    return [
                        'code' => 1,
                        'msg'  => $result['msg'],
                    ];
                }
                // 复制文件
                $this->copyDir($name);
                // 导入SQL
                $this->importSql($name);

                if( array_key_exists('menu', get_object_vars($object)))
                {
                    if($menu_config = $object->menu)
                    {
                        if(isset($menu_config['is_nav']) && $menu_config['is_nav']===1){
                            $pid = 0;
                        }else{
                            $pid = db('auth_rule')->where(['name'=>'plugin'])->value('id');
                        }

                        $menu[] = $menu_config['menu'];
                        //导入菜单
                        $this->addMenu($menu,$pid);
                    }
                }

            } catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '安装失败：' . $e->getMessage(),
                ];
            }
        } else {
            return [
                'code' => 0,
                'msg'  => '插件实例化失败',
            ];
        }
        return [
            'code' => 1,
            'msg'  => '插件安装成功',
        ];
    }

    // 卸载插件
    public function uninstall(string $name): array
    {
        // 实例化插件
        $object = $this->getInstance($name);
        // 获取插件基础信息
        $info = $object->getInfo();

        if (false !== $object->uninstall()) {
            $info['status'] = 0;
            $info['install'] = 0;
            // 更新或创建插件的ini文件
            $result = $this->setPluginIni($name, $info);
            //执行卸载sql
            $this->uninstallSql($name);
            //执行卸载菜单
            if( array_key_exists('menu', get_object_vars($object)))
            {
                $menu_config = $object->menu;
                if(!empty($menu_config))
                {
                    $menu[] = $menu_config['menu'];
                    $this->removeMenu($menu);
                }
            }
            //删除文件
            $this->removeDir($name);

            if ($result['code'] == 0) {
                return [
                    'code' => 0,
                    'msg'  => $result['msg'],
                ];
            } else {
                return [
                    'code' => 1,
                    'msg'  => '插件卸载成功',
                ];
            }
        } else {
            return [
                'code' => 0,
                'msg'  => '插件实例化失败',
            ];
        }
    }

    // 启用/禁用插件
    public function changeStatus(string $name): array
    {
        // 实例化插件
        $object = $this->getInstance($name);
        // 获取插件基础信息
        $info = $object->getInfo();

        if (false !== $object->install()) {
            $info['status'] = $info['status'] == 1 ? 0 : 1;
            try {
                // 更新或创建插件的ini文件
                $result = $this->setPluginIni($name, $info);
                if ($result['code'] == 0) {
                    return [
                        'code' => 1,
                        'msg'  => $result['msg'],
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '状态变动失败：' . $e->getMessage(),
                ];
            }
        } else {
            return [
                'code' => 0,
                'msg'  => '插件实例化失败',
            ];
        }
        return [
            'code' => 1,
            'msg'  => '状态变动成功',
        ];
    }

    // 判断插件配置文件是否进行了分组
    public function checkConfigGroup($config): bool
    {
        // 获取第一个元素
        $arrayShift = array_shift($config);
        if (array_key_exists('title', $arrayShift) && array_key_exists('type', $arrayShift)) {
            // 未开启分组
            return false;
        } else {
            // 开启分组
            return true;
        }
    }

    // 验证插件是否完整
    private function check(string $name)
    {
        if (!is_dir($this->addonsPath . DIRECTORY_SEPARATOR . $name)) {
            return '未发现该插件,请先下载并放入到plugins目录中';
        }
        return true;
    }

    // 获取插件实例
    private function getInstance(string $file)
    {
        $class = "\\plugins\\{$file}\\Plugin";
        if (class_exists($class)) {
            return app($class);
        }
        return false;
    }

    /**
     * 获取完整配置列表[config.php]
     * @param string $name
     * @return mixed
     */
    public function getConfig(string $name)
    {
        $file = app()->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.php';
        if (file_exists($file)) {
            return include $file;
        } else {
            return false;
        }
    }

    /**
     * 解析模板配置为键值对
     * @param string $name
     * @param string $configName
     * @return array|false
     */
    public function getPluginsConfigs(string $name,$configName = '')
    {
        $config = Cache::get('cache_plugins_config_'.$name, []);

        if ($config) {
            return $configName ? $config[$configName] : $config;
        }

        if(!$config = $this->getConfig($name)) return false;

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
                        $v['value'] = json_decode($v['option'],true);
                    }
                    $newConfig[$k]=$v['value'];
                }
            }
        }else{
            foreach ($config as $key=>$val)
            {
                if (in_array($val['type'], ['select','checkbox'])) {
                    $val['value'] = explode(',', $val['value']);
                }

                if ($val['type'] == 'array') {
                    $v['value'] = json_decode($val['option'],true);
                }

                $newConfig[$key]=$val['value'];
            }
        }

        Cache::set( 'cache_plugins_config_'.$name,$newConfig);
        return $configName ? $newConfig[$configName] : $newConfig;
    }

    /**
     * 更新插件的配置文件
     * @param string $name 插件名
     * @param array $array
     * @return mixed
     */
    private function setPluginConfig(string $name, array $array = []): array
    {
        Cache::set( 'cache_plugins_config_'.$name,null);
        $file = $this->addonsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.php';
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
     * 更新插件的ini文件
     * @param string $name 插件名
     * @param array $array
     * @return mixed
     */
    private function setPluginIni(string $name, array $array = []): array
    {
        $file = $this->addonsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'info.ini';
        if (!$this->checkFileWritable($file)) {
            return [
                'code' => 0,
                'msg' => '文件没有写入权限',
            ];
        }
        // 拼接要写入的数据
        $str = '';
        foreach ($array as $k => $v) {
            $str .= $k . " = " . $v . "\n";
        }
        if ($handle = fopen($file, 'w')) {
            fwrite($handle, $str);
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

    // 导入SQL
    private function importSql(string $name): bool
    {
        $sqlFile = $this->addonsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'install.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*')
                    continue;

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
        return true;
    }

    //卸载SQL
    private function uninstallSql(string $name): bool
    {
        $sqlFile = $this->addonsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'uninstall.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*')
                    continue;

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
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    // 安装时复制资源文件
    private function copyDir(string $name)
    {
        $addonDir = $this->addonsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        foreach ($this->copyDirs as $k => $dir)
        {
            if (is_dir($addonDir . $dir))
            {
                FileHelper::copyDir($addonDir . $dir, app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'static'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$name);
            }
        }
    }

    // 卸载时删除文件
    private function removeDir(string $name)
    {
        $dest = app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'static'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$name;
        FileHelper::delDir($dest);
    }

    // 生成表单信息
    public function makeAddColumns($config): array
    {
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
        $columns = [];
        foreach ($config as $k => $field) {
            // 初始化
            $field['name'] = isset($field['name']) ? $field['name'] : $field['title'];
            $field['field'] = $k;
            $field['tips'] = $field['tips'] ? $field['tips'] : '';
            $field['required'] = isset($field['required']) ? $field['required'] : 0;
            $field['group'] = isset($field['group']) ? $field['group'] : '';
            if (!isset($field['setup'])) {
                $field['setup'] = [
                    'default' => $field['value'] ? $field['value'] : '',
                    'extra_attr' => isset($field['extra_attr']) ? $field['extra_attr'] : '',
                    'extra_class' => isset($field['extra_class']) ? $field['extra_class'] : '',
                    'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
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
}