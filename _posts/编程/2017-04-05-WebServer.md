---
layout: post
title:  "Web Server"
date: 2017-04-05 00:00:03
categories: 编程
tags: Nginx Apache
excerpt: ""
---

* content
{:toc}


## CGI、CGI程序、Fast-CGI、php-fpm、Web Server等概念理解
* **CGI**：即公共网关接口(Common Gateway Interface)，<u>描述了客户端和服务器程序之间传输数据的一种协议</u>，就是规定要传哪些数据、以什么样的格式传递给后方处理这个请求的协议。总之，<u>CGI是一种标准，一种协议</u>。
* **CGI程序**：确保CGI协议的顺利执行，并且返回结果，用来沟通程序（如PHP、Python、 Java）和Web服务器（Apache2、Nginx），充当桥梁的作用。即<u>CGI程序是介于Web服务器与Web程序之间的用来保证CGI协议执行的程序</u>。比如php-cgi（安装php后会自动安装）。
* **Web Server**：内容的分发者，比如Nginx，通过配置Web Server知道如何处理不同类型的文件，比如请求.html文件，Web Server能够判断出是静态文件；请求.php文件，Web Server知道如何找到并执行对应的CGI程序（<u>比如php-cgi</u>）来处理，根据CGI协议把需要的数据传给CGI程序，对于php-cgi来说，会解析php.ini文件，初始化执行环境，然后找到index.php并编译执行。CGI程序的输出会被Web Server收集并加上合适的HTTP Header并返回给客户端。
* CGI执行的特点是每次<u>请求过来后再启动CGI程序</u>去处理请求，一次请求对应一个新的CGI进程。一些更有效的技术可以<u>让脚本解释器直接作为模块集成在 Web 服务器</u>（例如：Apache，mod_php）<u>中</u>，这样就能避免重复载入和初始化解释器（即无需再额外执行CGI程序）。不过这只是就那些需要解释器的高级语言（即解释型语言）而言的，使用诸如C一类的编译语言则可以避免这种额外负荷。
* **FastCGI程序**：如Apache的mod_fcgid，php-fpm等。<u>使用持续的进程来处理多个请求</u>。FastCGI会启动FastCGI进程管理器（简称master），解析配置文件，初始化执行环境，master再启动多个CGI程序（简称worker）在那里等候。当进来一个请求时，Web Server把环境变量和这个页面请求通过一个Unix Socket或者一个TCP connection传递给FastCGI进程。master的执行特点是请求过来，把请求传递给到空闲的worker，然后立即可以接受下一个请求，再传递。每个worker都一直在等候，接到从master传递过来的请求之后，立即执行并返回，但是<u>执行完毕后，不销毁，而且继续等待下个请求。</u>
* 修改php.ini文件后，php-fpm可以平滑重启（php-fpm reload），php-cgi进程无法平滑重启只能restart。




## Nginx fastcgi_index配置的作用

**语法：**
```
fastcgi_index file 
```
如果URI以斜线结尾，文件名将追加到URI后面，这个值将存储在Nginx内置变量$fastcgi_script_name中。

**例如：**
```
fastcgi_index  index.php;
fastcgi_param  SCRIPT_FILENAME  /home/www/scripts/php$fastcgi_script_name;
```
请求/page.php时，SCRIPT_FILENAME将被设置为/home/www/scripts/php/page.php，但是请求`/`则为/home/www/scripts/php/index.php。


## Nginx fastcgi_pass的不同配置
该配置用于指定FastCGI服务器监听端口与地址。
* 直接使用IP地址和端口号
```
fastcgi_pass localhost:9000;
```

* 使用Unix Socket
```
fastcgi_pass unix:/tmp/fastcgi.socket;
```

* 使用upstream
```
upstream backend  {  
  server   localhost:1234;
} 
fastcgi_pass backend;
```

## Nginx与php-fpm配合工作的流程
* （1）FastCGI进程管理器php-fpm自身初始化，启动主进程php-fpm和启动start_servers个CGI子进程。主进程php-fpm主要是管理fastcgi子进程，监听9000端口。fastcgi子进程等待来自Web Server的连接。
* （2）当客户端请求到达Nginx时，Nginx通过location指令，将所有以php为后缀的文件都交给127.0.0.1:9000来处理。
* （3）FastCGI进程管理器PHP-FPM选择并连接到一个子进程CGI解释器。Web server将CGI环境变量和标准输入发送到FastCGI子进程。
* （4）FastCGI子进程完成处理后将标准输出和错误信息从同一连接返回Web Server。当FastCGI子进程关闭连接时，请求便告处理完成。
* （5）FastCGI子进程接着等待并处理来自FastCGI进程管理器（运行在 WebServer中）的下一个连接。


## Nginx反向代理提高性能的理解
对于后端是动态服务来说，比如Java和PHP。这类服务器（如JBoss和PHP-FPM）的IO处理能力往往不高。Nginx可以把Request在读取完整之前buffer住，这样交给后端的就是一个完整的HTTP请求，从而提高后端的效率，而不是断断续续的传递。同样，Nginx也可以把response给buffer住，同样也是减轻后端的压力。

## 优化Apache的配置，去掉不必要的系统调用
Apache支持通过.htaccess文件来为htdocs下的各个目录进行局部的参数配置，但它有一定的副作用。当将httpd.conf的AllowOverride设置为All时，使用strace来跟踪Apache的一个子进程可以发现，在某次请求处理中的一系列系统调用中有很多open系统调用，目的在于检查被访问的文件路径中各级目录下是否存在.htaccess文件。

## Nginx的内存分配策略优于Apache
Nginx使用多线程来处理请求，使得多个线程之间可以共享内存资源。此外，使用分阶段的内存分配策略，按需分配，及时释放，使得内存使用量保持在很小的数量范围。
Nginx声称维持10000个非活跃HTTP持久链接只需要2.5M内存。


## Apache对于静态文件请求的处理方式
通常向Web服务器请求静态文件的过程是：磁盘文件的数据经过内核缓冲区到达用户内存空间，然后被送到网卡对应的内核缓冲区，接着被送进网卡并发送。数据从内核出去，没有经过任何变化，又回到了内核，因此浪费时间。
sendfile()系统调用可以将磁盘文件的特定部分直接送到代表客户端的socket描述符中，从而加快静态文件的请求速度，同时减少CPU和内存的开销。
Apache对于较小的静态文件选择使用内存映射来读取，对于较大的静态文件使用sendfile来传送文件。


## 服务器并发策略
从本质上讲所有到达Web服务器的请求都封装在IP包中，位于网卡的接收缓冲区内。Web服务器软件要做的事情就是不断地读取这些请求，进行处理，再将结果写到发送缓冲区。这个过程中涉及很多I/O操作和CPU计算，并发策略的目的就是让I/O操作和CPU计算尽量重叠进行，让CPU在I/O等待时不要空闲，同时在I/O调度上尽量花费最少的时间。
* 一个进程处理一个连接，非阻塞I/O
  fork()模式、prefork()模式；
  并发连接数有限，但是稳定性和兼容性较好。

* 一个线程处理一个连接，非阻塞I/O
  比如Apache的worker模型，这里的线程实际是轻量级进程，实际并不比prefork有太大优势。

* 一个进程处理多个连接，非阻塞I/O
  这种模式下，多路I/O就绪通知的性能成为关键。
  这种处理多个连接的进程称为work进程，通常数量可配，比如在Nginx中：worker_processes 2;

* 一个线程处理多个连接，异步I/O
  对于磁盘文件的操作，设置文件描述符为非阻塞没有任何意义：如果需要读取的数据不在磁盘缓冲区，磁盘便开始动用物理设备来读取数据，这时整个进程的其他工作必须等待。目前几乎没有Web服务器支持基于Linux AIO的工作方式。


## SSI（服务器端包含）技术
SSI（服务器端包含）技术实现各个局部页面的独立更新，比如Apache中的mod_include模块：
```
AddType text/html .shtml
AddOutputFilter INCLUDES .shtml
```
一旦网页支持SSI（按如上配置，即后缀为.shtml），那么每次请求的时候服务器必须要通读网页内容查找include标签，这需要大量的CPU开销。

SSI语法略。


## Web服务器缓存响应内容
一个URL在一段较长的时间内对应一个唯一的响应内容，主流的Web服务器软件都提供对URL映射内容的缓存，比如Apache的mod_cache，在URL映射开始时检查缓存，如果缓存有效就直接取出作为响应内容返回给浏览器。
Web服务器的缓存机制实质上是以URL为主键的key-value结构缓存。
```
LoadModule cache_module modules/mod_cache.so
LoadModule disk_cache_module modules/mod_disk_cache.so
CacheRoot /data/apache_cache
CacheEnable disk /
CacheDirLevels 5
CacheDirLength 3

CacheMaxExpire 3600
CacheIgnoreHeaders Set-Cookie
```
Apache将缓存的HTTP头信息和正文信息独立存储，这样为缓存过期检查提供了方便：只要检查HTTP头信息的文件即可。
Web服务器的缓存仍然基于HTTP的缓存协商进行控制。


## Web服务器缓存文件描述符
Apache提供了mod_file_cache模块来将已打开的文件描述符缓存在Web服务器内存中。需要指定希望缓存的文件列表：
```
CacheFile /data/www/htdocs/test.htm
```
Apache在启动时会打开这些文件，并持续到Apache关闭为止。
缓存文件描述符只适合小的静态文件，因为处理较大的静态文件所花费的主要时间在传送数据上，而不是打开文件。


## 反向代理缓存
目前更多使用的是NAT技术而不是代理服务器，代理服务器工作在应用层，需要能够支持应用层的协议，NAT工作在应用层以下，可以透明地转发应用层协议的数据。

反向代理的主要作用在于屏蔽后端服务器（安全性）和基于缓存的加速（性能）。

和Web服务器缓存、浏览器缓存一样，反向代理服务器缓存也可以基于HTTP/1.1，只要站点是面向HTTP缓存友好的内容，就可以直接放在代理服务器的后端达到反向代理缓存的目的。

通常会将Web服务器和应用服务器分离，即前端的Web服务器处理一些静态内容，同时又作为反向代理将动态内容的请求转发给后端的应用服务器处理，比如如下配置Nginx，当处理静态文件时，直接由Nginx返回（基于epoll，快），如果是动态内容，则交由启在80端口的Apache处理：
```
location ~ \.php$ {
  proxy_pass location:80;
}
```
反向代理只是Nginx的一个扩展模块，并且其缓存机制目前还不算完善，更专业的工具是Squid、Varnish等。

（Varnish实例，亮点是有脚本语言，可以实现逻辑控制，略）


## 缓存命中率和后端吞吐率的理想计算模型
假设反向代理服务器向后端服务器请求内容的次数为“活跃内容数”（缓存不命中），那么：
缓存命中率 = （ 活跃内容数/(实际吞吐率 * 平均缓存有效期) ） * 100%
同样的：
缓存命中率 = (1 - (后端吞吐率 / 实际吞吐率)） * 100%
（详细分析，略）


## ESI（Edge Side Includes）
反向代理服务器可以支持部分内容更新，但是前提是网页必须实现ESI（Edge Side Includes），ESI是W3C指定的标准，语法非常类似SSI，不同的是SSI在Web服务器上组装内容，而ESI在HTTP代理服务器上组装内容：
```
<HTML>
<BODY>
...
新闻内容
...
推荐新闻：<esi:include src="/recommand.php" />
</BODY>
</HTML>
```
（显然ajax更好，详略）


## 穿透代理
有些时候需要穿透代理，比如获取用户的实际IP，这一般是通过自定义一些服务器变量（反向代理服务器请求后端服务器时会设置）来实现，如Nginx：
```
location / {
  proxy_pass http://location:8001;
  proxy_set_header X-Real-IP $remote_addr;
}
```
这样，后端程序可以通过访问服务器变量$_SERVER['HTTP_X_REAL_IP']来获得用户的实际IP。

同样的，如果后端服务器想要透过反向代理来告诉浏览器一些额外信息（比如当存在多个后端服务器时），也可以通过在响应HTTP头信息中携带一定的自定义信息来实现：
```
header('X-Real-Server-IP:10.0.0.1');
```
这样，最终反向代理返回的HTTP头信息中有如下内容：
```
X-Real-Server-IP:10.0.0.1
```

## WebDAV
WebDAV是HTTP的扩展协议，它允许基于HTTP/1.1协议来对Web服务器进行远程文件操作，包括文件和目录的创建、修改，以及版本控制等（SVN的HTTP工作方式）。
目前主流的Web服务器软件都支持WebDAV扩展。

创建目录的请求：
```
MKCOL /files/2009/ HTTP/1.1
Host:www.xxx.com
```

创建成功的响应：
```
HTTP/1.1 201 Created
```


## Apache不适合用作通用Web服务器
Apache不适合用作通用Web服务器（既处理动态脚本也处理静态文件）：Apache对于静态文件的请求存在资源浪费，进程会复用，如果前一次处理的是动态语言脚本的请求，在请求结束后并不会释放所有的内存给操作系统，这样会造成一个占用内存很多的进程来为一个很小的请求服务的情况。同样的，这些被复用的进程也可能会保持大量MySQL连接，从而浪费MySQL资源。
总之，不要使用Apache来做静态内容服务，或者至少和动态服务使用不同的Apache实例。



## 配置通过Web页面实时查看php-fpm的状态
在nginx里面加一个location：
```
location ~ ^/status$ {
  include fastcgi_params;
  fastcgi_pass 127.0.0.1:9000;
  fastcgi_param SCRIPT_FILENAME $fastcgi_script_name;
}
```
然后在php-fpm.conf里面打开选项：
```
pm.status_path = /status
```
过http://域名/status就可以看到当前的php情况。


## 配置Nginx和Apache的Url重写使其支持Phalcon项目的路由
对于Phalcon项目，以下访问路径是等价的：
```
http://phalcon.w-blog.cn/phalcon/index/test
http://phalcon.w-blog.cn/phalcon/public/?_url=/index/test
```

**Nginx配置**
```
# 当URL中包含/phalcon/时进入到下面的url重写
location  /phalcon/ { 
  # 把/phalcon/后面的内容放到了public/index.php?_url=/后面
  rewrite ^/phalcon/(.*)$ /phalcon/public/index.php?_url=/$1;
}
```

**Apache配置**
* 在Phalcon目录下创建.htaccess文件加入如下语句，主要作用是指向到public
```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteRule  ^$ public/    [L]
    RewriteRule  ((?s).*) public/$1 [L]
</IfModule>
```

* 在public加入如下语句主要作用是定向赋值给_url
```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^((?s).*)$ index.php?_url=/$1 [QSA,L]
</IfModule>
```



























