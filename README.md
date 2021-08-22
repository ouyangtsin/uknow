# UKnowing知识问答系统

#### 介绍
UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储

#### 软件架构
thinkphp6

#### 使用说明

官方交流社区[https://ask.uknowing.com](https://ask.uknowing.com)

UKnowing的环境要求如下：

PHP >= 7.1.0
可用的 www 服务器，如 Apache、IIS、nginx, 推荐使用性能高效的 nginx
MySQL 5.7 以上, 服务器需要支持 MySQLi 或 PDO_MySQ
安装Composer
如果还没有安装 Composer，在 Linux 和 Mac OS X 中可以运行如下命令：

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
在 Windows 中，你需要下载并运行 Composer-Setup.exe。
如果遇到任何问题或者想更深入地学习 Composer，请参考Composer 文档（英文文档，中文文档）。

由于众所周知的原因，国外的网站连接速度很慢。因此安装的时间可能会比较长，我们建议使用国内镜像（阿里云）。

打开命令行窗口（windows用户）或控制台（Linux、Mac 用户）并执行如下命令：

composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
安装程序
Gitee下载程序源代码 https://gitee.com/uknowing/uknow

1. 上传 解压后 目录中的文件到服务器，根目录执行 composer update更新composer包

2. 设置目录属性（windows 服务器可忽略这一步）

以下这些目录需要可读写权限

./

./runtime

./public/uploads

./config

./install/lock/

3. 执行安装脚本

直接在浏览器里面访问 http://您的域名/install.php；

4. 参照页面提示，进行安装，直至安装完毕


URL重写
可以通过URL重写隐藏应用的入口文件index.php（也可以是其它的入口文件，但URL重写通常只能设置一个入口文件）,下面是相关服务器的配置参考：

[ Apache ]
httpd.conf配置文件中加载了mod_rewrite.so模块
AllowOverride None 将None改为 All
把下面的内容保存为.htaccess文件放到应用入口文件的同级目录下
<IfModule mod_rewrite.c>
  Options +FollowSymlinks -Multiviews
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]
</IfModule>
[ IIS ]
如果你的服务器环境支持ISAPI_Rewrite的话，可以配置httpd.ini文件，添加下面的内容：

RewriteRule (.*)$ /index\.php\?s=$1 [I]
在IIS的高版本下面可以配置web.Config，在中间添加rewrite节点：

<rewrite>
 <rules>
 <rule name="OrgPage" stopProcessing="true">
 <match url="^(.*)$" />
 <conditions logicalGrouping="MatchAll">
 <add input="{HTTP_HOST}" pattern="^(.*)$" />
 <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
 <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
 </conditions>
 <action type="Rewrite" url="index.php/{R:1}" />
 </rule>
 </rules>
 </rewrite>
[ Nginx ]
在Nginx低版本中，是不支持PATHINFO的，但是可以通过在Nginx.conf中配置转发规则实现：

location / { // …..省略部分代码
   if (!-e $request_filename) {
   		rewrite  ^(.*)$  /index.php?s=/$1  last;
    }
}

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request