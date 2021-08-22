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

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request