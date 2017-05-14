---
layout: post
title:  "CentOS部署最新Node前端执行环境"
date: 2017-04-05 00:00:04
categories: 开发配置记录
tags: CentOS Node
excerpt: ""
---

* content
{:toc}

# CentOS部署最新Node前端执行环境

## 前置信息

1.操作系统为CentOS 6.8

2.我们的前端项目执行需要安装node、npm、webpack、bower等等

3.本文的node`最新版本`为7.7.3，其他版本安装过程类似



## 安装node

node的编译安装依赖巨多,甚至依赖python版本,所以直接使用二进制文件.

```
cd /data/extend/
wget https://nodejs.org/dist/v7.7.3/node-v7.7.3-linux-x64.tar.gz
tar -xvf node-v7.7.3-linux-x64.tar.gz

ln -s /data/extend/node-v7.7.3-linux-x64/bin/node /usr/bin/node
ln -s /data/extend/node-v7.7.3-linux-x64/bin/npm /usr/bin/npm
npm config set registry https://registry.npm.taobao.org  
```

输出：

```
[root@iZuf6cbroi7rj1zjydjruoZ extend]# node -v
v7.7.3
[root@iZuf6cbroi7rj1zjydjruoZ extend]# npm -v
4.1.2
```



## 安装webpack

```
npm install webpack -g
ln -s /data/extend/node-v7.7.3-linux-x64/lib/node_modules/webpack/bin/webpack.js /usr/bin/webpack
```

输出

```
[root@iZuf6cbroi7rj1zjydjruoZ extend]# webpack -v
webpack 2.2.1
...
```



## 安装bower

```
npm install -g bower
ln -s /data/extend/node-v7.7.3-linux-x64/lib/node_modules/bower/bin/bower /usr/bin/bower
```

输出

```
[root@iZuf6cbroi7rj1zjydjruoZ deploy]# bower -v
1.8.0
```