<?php

namespace app\common\library\helper;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;

class ThemeHelper
{
    // 模板目录
    protected $templatesPath = '';
    private static $instance;
    protected  $moduleName;

    // 构造方法
    public function __construct($moduleName)
    {
        $this->moduleName = $moduleName;
        $this->templatesPath = public_path() . 'templates'.DIRECTORY_SEPARATOR.$moduleName;
    }

    public static function instance($moduleName): ThemeHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($moduleName);
        }
        return self::$instance;
    }

    // 获得本地主题列表
    public function localTemplates(): array
    {
        $templates = scandir($this->templatesPath);
        $list = [];
        foreach ($templates as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $templatesDir = $this->templatesPath . DIRECTORY_SEPARATOR;
            if (!is_dir($templatesDir)) {
                continue;
            }
            // 获取模板基础信息
            $info = $this->getTemplateInfo($name);
            // 增加右侧按钮组
            $str = '';
            if ($info['install'] == 1) {
                // 已安装，增加配置按钮
                $str .= '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="UK.operate.edit(\''.$name.'\')"><i class="fa fa-edit"></i> 配置</a> ';
                $str .= '<a class="btn btn-danger btn-xs confirm" href="javascript:void(0)" onclick="templateUninstall(\'' . $name . '\')"><i class="fa fa-edit"></i> 卸载</a> ';
            } else {
                // 未安装，增加安装按钮
                $str = '<a class="btn btn-primary btn-xs" href="javascript:void(0)" onclick="templateInstall(\'' . $name . '\')"><i class="fa fa-edit"></i> 安装</a>';
            }
            $info['button'] = $str;
            $list[] = $info;
        }
        return $list;
    }

    /**
     * 获取模板信息
     * @param $templateName
     * @return array|mixed
     */
    public function getTemplateInfo($templateName)
    {
        $info = Config::get("templates_{$this->moduleName}_{$templateName}_info", []);
        if ($info) {
            return $info;
        }
        // 文件配置
        $info_file = $this->templatesPath.DS.$templateName.DS.'info.ini';
        if (is_file($info_file)) {
            $_info = parse_ini_file($info_file, true, INI_SCANNER_TYPED) ?: [];
            $info = array_merge($info, $_info);
        }
        Config::set($info, "templates_{$this->moduleName}_{$templateName}_info");

        return isset($info) ? $info : [];
    }

    // 获取模板配置信息
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

    // 保存模板信息
    public function configPost(array $data = []): array
    {
        $check = $this->check($data['id']);
        if ($check !== true) {
            return [
                'code' => 0,
                'msg' => $check
            ];
        }
        // 获取模板配置信息
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
        $result = $this->setThemeConfig($data['id'], $config);
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

    // 启用模板或禁用模板
    public function state(string $name): array
    {
        $check = $this->check($name);
        if ($check !== true) {
            return [
                'code' => 0,
                'msg' => $check
            ];
        }
        // 获取模板基础信息
        $info = $this->getTemplateInfo($name);
        if (!$info) {
            return [
                'code' => 0,
                'msg'  => '未找到该模板的信息'
            ];
        } else {
            // 请先安装
            if ($info['install'] != 1) {
                return [
                    'code' => 0,
                    'msg'  => '请先安装该模板',
                ];
            } else {
                return $this->changeStatus($name);
            }
        }
    }

    // 安装模板
    public function install(string $name): array
    {
        // 获取模板基础信息
        $info = $this->getTemplateInfo($name);
        $info['install'] = 1;
        try {
            // 更新或创建模板的ini文件
            $result = $this->setThemeIni($name, $info);
            if ($result['code'] == 0) {
                return [
                    'code' => 1,
                    'msg'  => $result['msg'],
                ];
            }
            // 导入SQL
            $this->importSql($name);
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '安装失败：' . $e->getMessage(),
            ];
        }
        return [
            'code' => 1,
            'msg'  => '模板安装成功',
        ];
    }

    // 卸载模板
    public function uninstall(string $name): array
    {
        $info = $this->getTemplateInfo($name);
        $info['status'] = 0;
        $info['install'] = 0;
        // 更新或创建模板的ini文件
        $result = $this->setThemeIni($name, $info);
        //执行卸载sql
        $this->uninstallSql($name);

        if ($result['code'] == 0) {
            return [
                'code' => 0,
                'msg'  => $result['msg'],
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '模板卸载成功',
            ];
        }
    }

    // 启用/禁用模板
    public function changeStatus(string $name): array
    {
        $info = $this->getTemplateInfo($name);
        $info['status'] = $info['status']==1 ? 0 : 1;

        //设置为默认模板
        if($info['status'])
        {
            $templates = scandir($this->templatesPath);
            foreach ($templates as $template) {
                if ($template === '.' or $template === '..')
                    continue;

                if (!is_dir($templatesDir = $this->templatesPath . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR))
                    continue;

                if ($template!=$name)
                {
                    $template_info = $this->getTemplateInfo($template);
                    $template_info['status'] = 0;
                    $this->setThemeIni($template,$template_info);
                }
            }
            Config::set(['default_theme'=>$name],$this->moduleName.'_theme_default');
        }else{
            //取消设置默认模板
            $templates = scandir($this->templatesPath);
            $tmp=array();
            foreach ($templates as $template) {
                if ($template === '.' or $template === '..')
                    continue;

                if (!is_dir($templatesDir = $this->templatesPath . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR))
                    continue;
                $tmp[] = $name;
            }

            if(count($tmp)==1)
            {
                Config::set(['default_theme'=>$tmp[0]],$this->moduleName.'_theme_default');
                return [
                    'code' => 0,
                    'msg'  => '当前仅有一个模板不可取消为默认模板,否则页面将无法显示',
                ];
            }
        }

        try {
            // 更新或创建模板的ini文件
            $result = $this->setThemeIni($name, $info);
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

        return [
            'code' => 1,
            'msg'  => '状态变动成功',
        ];
    }

    // 判断模板配置文件是否进行了分组
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

    // 验证模板是否完整
    private function check(string $name)
    {
        if (!is_dir($this->templatesPath . DIRECTORY_SEPARATOR . $name)) {
            return '未发现该模板,请先下载并放入到templates目录中';
        }
        return true;
    }

    /**
     * 获取完整配置列表[config.php]
     * @param string $name
     * @return mixed
     */
    public function getConfig(string $name)
    {
        $file = $this->templatesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.php';
        if (file_exists($file)) {
            return include $file;
        } else {
            return false;
        }
    }

    /**
     * 解析模板配置为键值对
     * @param string $name
     * @return array|false
     */
    public function getThemeConfigs(string $name)
    {
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

        return $newConfig;
    }

    /**
     * 获取默认模板
     * @return mixed|string
     */
    public function getDefaultTheme(): string
    {
        if($defaultTheme = Config::get($this->moduleName.'_theme_default'))
        {
            return $defaultTheme['default_theme']?:'default';
        }

        $templates = scandir($this->templatesPath);
        $defaultTheme = '';
        foreach ($templates as $name) {
            if ($name === '.' or $name === '..')
                continue;
            if (!is_dir($this->templatesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR))
                continue;
            // 获取模板基础信息
            $info = $this->getTemplateInfo($name);
            if($info['status'])
            {
                $defaultTheme = $name;
            }
        }
        Config::set(['default_theme'=>$defaultTheme],$this->moduleName.'_theme_default');
        return $defaultTheme?:'default';
    }

    /**
     * 更新模板的配置文件
     * @param string $name 模板名
     * @param array $array
     * @return mixed
     */
    private function setThemeConfig(string $name, array $array = []): array
    {
        $file = $this->templatesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.php';
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
     * 更新模板的ini文件
     * @param string $name 模板名
     * @param array $array
     * @return mixed
     */
    private function setThemeIni(string $name, array $array = []): array
    {
        $file = $this->templatesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'info.ini';
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
        $sqlFile = $this->templatesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'install.sql';
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

    //卸载SQL
    private function uninstallSql(string $name): bool
    {
        $sqlFile = $this->templatesPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'uninstall.sql';
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

    // 生成表单信息
    public function makeAddColumns($config)
    {
        // 判断是否开启了分组
        if ($this->checkConfigGroup($config) === false) {
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
            $field['name'] = $field['name'] ?? $field['title'];
            $field['field'] = $k;
            $field['tips'] = $field['tips'] ?? '';
            $field['required'] = $field['required'] ?? 0;
            $field['group'] = $field['group'] ?? '';
            if (!isset($field['setup'])) {
                $field['setup'] = [
                    'default' => $field['value'] ?? '',
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
            elseif ($field['type'] == 'image' || $field['type'] == 'images') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['size'] ?? get_setting('upload_image_size'),              // 限制大小（单位kb）
                    $field['setup']['ext'] ?? get_setting('upload_image_ext'),               // 文件后缀
                    $field['setup']['extra_attr'] ?? '',  // 额外属性
                    $field['setup']['extra_class'] ?? '', // 额外CSS
                    $field['setup']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'file' || $field['type'] == 'files') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['setup']['default'],           // 默认值
                    $field['setup']['size'] ?? get_setting('upload_file_size'),              // 限制大小（单位kb）
                    $field['setup']['ext'] ?? get_setting('upload_file_ext'),               // 文件后缀
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