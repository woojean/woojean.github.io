---
layout: post
title:  "基于Jenkins实现PHP项目的部署"
date: 2017-04-05 00:00:04
categories: 开发配置记录
tags: Jenkins PHP
excerpt: ""
---

* content
{:toc}

## 前置信息

1.操作系统为CentOS 6.8

2.项目代码基于GitLab维护，且有Submodule；

3.Web服务器、数据库、Redis以及各种PHP扩展的配置等项目正常运行所依赖的前置条件本文不讨论；



## 安装/检查JDK

```
sudo yum install java-1.8.0-openjdk
java -version
```



## 安装Jenkins

```
wget -O /etc/yum.repos.d/jenkins.repo http://pkg.jenkins-ci.org/redhat/jenkins.repo
rpm --import http://pkg.jenkins-ci.org/redhat/jenkins-ci.org.key
sudo yum install jenkins
```

如果不能更新源，也可以直接下载rpm包进行安装：

```
wget http://pkg.jenkins-ci.org/redhat-stable/jenkins-2.7.3-1.1.noarch.rpm
rpm -ivh jenkins-2.7.3-1.1.noarch.rpm
```



## 配置Jenkins的端口　

```
vi /etc/sysconfig/jenkins
JENKINS_PORT="8888"
```



## 启动Jenkins

```
service jenkins start
```

初始密码：

```
cat /var/lib/jenkins/secrets/initialAdminPassword
```

第一次访问，通过Web页面登录后，修改Administrator的密码。

rm /var/log/jenkins/jenkins.log
service jenkins restart


## 配置Git账户

```
git config --global user.name "wsnbackend"
git config --global user.email wsnbackend@gegolab.com
git config --list

root@iZuf6a6ydemt9fvgy22rcqZ ~]# ssh-keygen -t rsa -C "wsnbackend@gegolab.com"
Generating public/private rsa key pair.
Enter file in which to save the key (/root/.ssh/id_rsa): 【回车】
Created directory '/root/.ssh'.
Enter passphrase (empty for no passphrase): 【回车】
Enter same passphrase again: 【回车】
Your identification has been saved in /root/.ssh/id_rsa.
Your public key has been saved in /root/.ssh/id_rsa.pub.
The key fingerprint is:
...

cat ~/.ssh/id_rsa.pub
[需要将部署账户添加到项目]
[登录wsnbackend，配置公钥]

ssh -T git@gegolab.com
```



## 修改Jenkins为root权限运行

```
gpasswd -a root jenkins # 将jenkins用户添加到root组
vi /etc/default/jenkins
JENKINS_USER=root
JENKINS_GROUP=root

service jenkins restart
```



## 为jenkins用户添加sudo免密码

```
vi /etc/sudoers
jenkins ALL = NOPASSWD:ALL
```



# 添加新项目

## 源码管理

1.选择Git并添加项目路径

git@git.gegolab.com:backend/sms.git



2.添加认证信息

点击`Credentials`右侧`Add`按钮，添加一个新的Jenkins Credentials，在打开的对话框中选择kind为`SSH Username with private key`。

```
SSH Username with private key:
wsnbackend@gegolab.com
private key 直接输入：
cat ~/.ssh/id_rsa  # gitlab用户wsnbackend@gegolab.com的私钥
```



3.处理submodule

点击`Additional Behaviours` ，勾选`Recursively update submodules`





## Jenkin构建后指定Shell

在`构建`模块下点击`增加构建步骤`，选择`Excute Shell`，输入：

```
#mkdir /data/www/sms
sudo chown -R jenkins:jenkins /data/www/sms
\cp -rf /var/lib/jenkins/workspace/sms /data/www/
cd /data/www/sms
sudo composer install
sudo chmod -R 777 /data/www/sms
sudo chown -R www:www /data/www/sms
```

以上Shell将会在代码拉取成功后执行。
