---
layout: post
title:  "在Macbook上搭建Thrift+Swoole实现的RPC执行环境"
date: 2017-04-05 00:00:04
categories: 开发配置
tags: Thrift Swoole
excerpt: ""
---

* content
{:toc}

## 使用brew安装thrift

```
brew install thrift
ln -s /usr/local/Cellar/thrift/0.10.0/bin/thrift /usr/local/bin/thrift
```



## 使用brew安装thrift的PHP扩展

```
brew install homebrew/php/php56-thrift
extension=/usr/local/Cellar/php56-thrift/0.9.2/thrift_protocol.so
/Applications/MAMP/bin/php/php5.6.30/bin/php -m | grep thrift
```



## 新建thrift接口（IDL）文件

/Users/wujian/learn/demo/thrift-php/hello.thrift

```
service HelloWorld
{
    string sayHello()
}
```



## 生成文件thrift存根文件

```
cd /Users/wujian/learn/demo/thrift-php
thrift -gen php:server hello.thrift
```



## 引入thrift的PHP库

composer require apache/thrift



## 编写Client

略



## 编写Server

略



## 测试

```
php -S localhost:9090
/Applications/MAMP/bin/php/php5.6.30/bin/php server.php
/Applications/MAMP/bin/php/php5.6.30/bin/php client.php --http
```



# 安装swoole

version: 1.9.6

编译安装，略。