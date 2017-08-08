---
layout: post
title:  "Nginx rewrite与Location的比较"
date: 2017-08-08 00:00:03
categories: 开发配置
tags: Nginx
excerpt: ""
---

* content
{:toc}


**rewrite**
使用Nginx提供的全局变量或自己设置的变量，结合正则表达式和标志位**实现url重写以及重定向**。rewrite只能放在server{},location{},if{}中，并且**只能对域名后边的除去传递的参数外的字符串起作用**。如，`http://demo.com/a/we/index.php?id=1&u=str` 只对`/a/we/index.php`重写。
如果想对域名或参数字符串起作用，可以使用全局变量匹配，也可以使用proxy_pass反向代理。

**Rewrite标志位**
* last  相当于Apache的[L]标记，表示完成rewrite
* break  停止执行当前虚拟主机的后续rewrite指令集
* redirect 返回302临时重定向，地址栏会显示跳转后的地址
* permanent 返回301永久重定向，地址栏会显示跳转后的地址

**Rewrite实例**
```
// 应用于Server
server {
  listen 80;
  server_name demo.com;
  index index.html index.php;
  root html;
  if ($http_host !~ “^star\.igrow\.cn$&quot {
    rewrite ^(.*) http://star.igrow.cn$1 redirect;
  }
}

// 防盗链
location ~* \.(gif|jpg|swf)$ {
  valid_referers none blocked start.igrow.cn sta.igrow.cn;
  if ($invalid_referer) {
    rewrite ^/ http://$host/logo.png;
  }
}

// 根据文件类型设置过期时间
location ~* \.(js|css|jpg|jpeg|gif|png|swf)$ {
  if (-f $request_filename) {
    expires 1h;
    break;
  }
}

// 禁止访问某个目录
location ~* \.(txt|doc)${
root /data/www/wwwroot/linuxtone/test;
deny all;
}
```

**rewrite和location的比较**
rewrite和location都能实现跳转，主要区别在于**rewrite是在同一域名内更改获取资源的路径，而location是对一类路径做控制访问或反向代理，可以proxy_pass到其他机器**。很多情况下rewrite也会写在location里，它们的执行顺序是：

* （1）执行server块的rewrite指令
* （2）执行location匹配
* （3）执行选定的location中的rewrite指令

如果其中某步URI被重写，则重新循环执行（1）~（3），直到找到真实存在的文件；循环超过10次，则返回500 Internal Server Error错误。

正则匹配会覆盖普通匹配，**location的执行逻辑跟location的编辑顺序无关**。

**location语法格式**：

```
location [=|~|~*|^~|@] /uri/ { … }
```

* = 表示精确匹配
* ~  区分大小写匹配
* ~* 不区分大小写匹配
* !~ 区分大小写不匹配
* !~* 不区分大小写不匹配
* ^ 以什么开头的匹配
* $ 以什么结尾的匹配
* ^~ 表示uri以某个常规字符串开头，不是正则匹配，优先级高于正则
* / 通用匹配,如果没有其它匹配,任何请求都会匹配到
* 代表任意字符
* . 匹配除换行符以外的任意字符
* ? 重复0次或1次
* \+ 重复1次或更多次
* \* 重复0次或更多次
* \d 匹配数字
* {n}重复n次
* {n,}重复n次或更多次
* [c]匹配单个字符c
* [a-z]匹配a-z小写字母的任意一个
* \转义字符
* -f和!-f判断是否存在文件
* -d和!-d判断是否存在目录
* -e和!-e判断是否存在文件或目录
* -x和!-x判断文件是否可执行

**实际使用中一般至少有三个匹配规则定义：**
* 直接匹配网站根，通过域名访问网站首页比较频繁，使用这个会加速处理。这里是直接转发给后端应用服务器了，也可以是一个静态首页。
```
location = / {
  proxy_pass http://tomcat:8080/index
}
```

* 处理静态文件的请求，这是nginx作为http服务器的强项。有两种配置模式，目录匹配或后缀匹配,任选其一或搭配使用。
```
location ^~ /static/ {
  root /webroot/static/;
}

location ~* \.(gif|jpg|jpeg|png|css|js|ico)$ {  
  root /webroot/res/;
}
```
* 通用规则，用来转发动态请求到后端应用服务器非静态文件请求就默认是动态请求
```
location / {
  proxy_pass http://tomcat:8080/
}
```

### 如何使用Nginx实现负载均衡和反向代理？
设定http服务器，利用它的反向代理功能提供负载均衡支持

```
http {
  # 设定mime类型,类型由mime.type文件定义
  include       /etc/nginx/mime.types;
  default_type  application/octet-stream;

  # 设定日志格式
  access_log    /var/log/nginx/access.log;

  # 其他配置，略

  # 设定负载均衡的服务器列表
  upstream mysvr {
    # weigth参数表示权值，权值越高被分配到的几率越大
    # 本机上的Squid开启3128端口
    server 192.168.8.1x:3128 weight=5;
      server 192.168.8.2x:80 weight=1;
      server 192.168.8.3x:80 weight=6;
    }

  upstream mysvr2 {
    # weigth参数表示权值，权值越高被分配到的几率越大
    server 192.168.8.x:80  weight=1;
    server 192.168.8.x:80  weight=6;
  }

  # 第一个虚拟服务器
  server {
     # 侦听192.168.8.x的80端口
    listen 80;
    server_name  192.168.8.x;

    # 对aspx后缀的进行负载均衡请求
    # 定义服务器的默认网站根目录位置
    location ~ .*\.aspx$ {
      # 定义服务器的默认网站根目录位置
      root   /root;

      # 定义首页索引文件的名称
      index index.php index.html index.htm;   

      # 请求转向mysvr定义的服务器列表
      proxy_pass  http://mysvr ;

      # 反向代理的配置
      proxy_redirect off;

      # 后端的Web服务器可以通过X-Forwarded-For获取用户真实IP
      proxy_set_header Host $host;
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

      # 允许客户端请求的最大单文件字节数
      client_max_body_size 10m;

      # 缓冲区代理缓冲用户端请求的最大字节数
      client_body_buffer_size 128k;

      # nginx跟后端服务器连接超时时间(代理连接超时)
      proxy_connect_timeout 90;

      # 后端服务器数据回传时间(代理发送超时)
      proxy_send_timeout 90;

      # 连接成功后，后端服务器响应时间(代理接收超时)
      proxy_read_timeout 90;

      # 设置代理服务器（nginx）保存用户头信息的缓冲区大小
      proxy_buffer_size 4k;    

      # proxy_buffers缓冲区，网页平均在32k以下的话，这样设置
      proxy_buffers 4 32k;    

      # 高负荷下缓冲大小（proxy_buffers*2）
      proxy_busy_buffers_size 64k;    

      # 设定缓存文件夹大小，大于这个值，将从upstream服务器传
      proxy_temp_file_write_size 64k;  
    }
  }
}
```
