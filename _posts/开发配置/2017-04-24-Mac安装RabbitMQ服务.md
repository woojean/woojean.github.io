---
layout: post
title:  "Mac安装RabbitMQ服务"
date: 2017-04-24 00:00:01
categories: 开发配置
tags: RabbitMQ Mac
excerpt: "在Mac上安装RabbitMQ，用于本地开发、学习"
---

* content
{:toc}

# 安装
```
brew update
brew install rabbitmq
```

# 运行RabbitMQ服务
```
/usr/local/sbin/rabbitmq-server
```

# 开启网页版控制台
```
/usr/local/sbin/rabbitmq-plugins enable rabbitmq_management 
```
访问：
```
http:localhost:15672
```
初始用户及密码：guest guest



