---
layout: post
title:  "Odoo Start On Mac"
date: 2020-05-07 00:00:00
categories: 编程
tags: Python
excerpt: ""
---

* content
{:toc}


# 更新Python为3.5以上版本
-----------------------------------------------------------------
## Python Mac 最新版本安装包
* 官方：
```
https://www.python.org/downloads/mac-osx/
```

* 3.7网盘：
```
https://pan.baidu.com/s/1hsPN-ACPs7bdGyGZxp7lmQ
p3j0
```

## 更新.bash_profile
```
vi ~/.bash_profile
alias python='/Library/Frameworks/Python.framework/Versions/3.7/bin/python3.7'
alias pip=pip3
source ~/.bash_profile
```

## 查看版本
```
python --version
```


# Mac安装PostgreSQL
-----------------------------------------------------------------
## 下载Mac安装包并安装
```
https://www.enterprisedb.com/downloads/postgres-postgresql-downloads
```
默认端口：5432
locale：zh_CN.UTF-8

## 启动PostgreSQL
```
/Library/PostgreSQL/12/scripts/runpsql.sh ;exit
```

使用pgAdmin管理。

## 创建用户
odoo禁止使用PG的默认用户“postgres”来访问，所以需要创建专门用于odoo使用的账户：
```
CREATE USER odoo WITH PASSWORD '888888';
```

创建属于新用户的数据库：
```
CREATE DATABASE odoo_db OWNER odoo;
```



# 通过源码部署odoo
-----------------------------------------------------------------
## Clone源码
```
git clone https://github.com/odoo/odoo.git
```

## 安装
[官方文档](https://www.odoo.com/documentation/13.0/setup/install.html#mac-os)

```
pip install setuptools wheel
pip install -r requirements.txt
```



# 配置文件
-----------------------------------------------------------------
```
mkdir config
vi config/odoo.conf
```



# 启动odoo
-----------------------------------------------------------------
## 指定启动文件启动
```
cd ~/odoo
./odoo-bin --config=config/odoo.conf -i base
```

初始账户名和密码都是admin

之后启动只需要：
```
./odoo-bin --config=config/odoo.conf
```









## 报错
* pip版本过旧
```
Could not find a version that satisfies the requirement gevent==1.3.7 (from -r requirements.txt (line 8)) (from versions: )
No matching distribution found for gevent==1.3.7 (from -r requirements.txt (line 8))
You are using pip version 10.0.1, however version 20.1 is available.
You should consider upgrading via the 'pip install --upgrade pip' command.
```
更新pip：
```
python -m pip install --upgrade pip
```

查看版本号：
```
pip3 --version
```

* pip设置源
换成阿里云源：
```
mkdir ~/.pip
vi ～/.pip/pip.conf
```
输入：
```
[global]
index-url=http://mirrors.aliyun.com/pypi/simple/
[install]
trusted-host=mirrors.aliyun.com
```

* 没有odoo-bin文件
```
cp setup/odoo odoo-bin
chmod -R 777 odoo-bin
```

* pgAdmin无法启动
```
ps -ef | grep pgAdmin
kill -9 xxx
```













