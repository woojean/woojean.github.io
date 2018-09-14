---
layout: post
title:  "Mac下使用sshpass实现ssh登录快捷命令"
date: 2017-08-21 00:00:01
categories: 编程
tags: 运维
excerpt: ""
---

* content
{:toc}


```
http://sourceforge.net/projects/sshpass/files/sshpass/1.05/sshpass-1.05.tar.gz
./configure
sudo make&make install

alias ts='sshpass -p "WED&****$" ssh root@120.21.53.21'
```



