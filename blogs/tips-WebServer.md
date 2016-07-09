
## 安装Nginx依赖哪些条件？
（1）编译环境gcc g++ 开发库之类的需要提前装好
（2）安装PCRE库，为了重写（rewrite）：PCRE(Perl Compatible Regular Expressions)是一个Perl库，包括perl兼容的正则表达式库。
（3）安装zlib库，为了gzip压缩。
（4）安装ssl

./configure --sbin-path=/usr/local/nginx/nginx 
--conf-path=/usr/local/nginx/nginx.conf 
--pid-path=/usr/local/nginx/nginx.pid 
--with-http_ssl_module
--with-pcre=/usr/local/src/pcre-8.34 			
--with-zlib=/usr/local/src/zlib-1.2.8 			
--with-openssl=/usr/local/src/openssl-1.0.1c


## Nginx的配置
nginx配置文件主要分为六个区域：
main
控制子进程的所属用户/用户组、派生子进程数、错误日志位置/级别、pid位置、子进程优先级、进程对应cpu、进程能够打开的文件描述符数目等
events
控制nginx处理连接的方式
（3）http
（4）sever
（5）location
（6）upstream

实例：

user www-data;    								# 运行用户
worker_processes  1;							# 启动进程数,通常设置成和cpu的数量相等
error_log  /var/log/nginx/error.log;			# 全局错误日志
pid        /var/run/nginx.pid;					# PID文件

// 工作模式及连接数上限
events {
use   epoll;             					# 使用epoll多路复用模式
worker_connections  1024;					# 单个后台worker process进程的最大并发链接数
    # multi_accept on; 
}

// 设定http服务器，利用它的反向代理功能提供负载均衡支持
http {
    # 设定mime类型,类型由mime.type文件定义
    include       /etc/nginx/mime.types;
default_type  application/octet-stream; 	# 1 octet = 8 bit

    # 设定访问日志
    access_log    /var/log/nginx/access.log;

    # sendfile指令指定nginx是否调用sendfile函数（zero copy方式）来输出文件，对于普通应用，必须设为on,如果用来进行下载等应用磁盘IO重负载应用，可设置为off，以平衡磁盘与网络I/O处理速度，降低系统的uptime.
    sendfile        on;
    #tcp_nopush     on;					# 在一个数据包里发送所有头文件，而不一个接一个的发送

    
    keepalive_timeout  65;				# 连接超时时间
    tcp_nodelay        on;				# 作用于socket参数TCP_NODELAY，禁用nagle算法，也即不缓存数据
    
    # 开启gzip压缩
    gzip  on;
    gzip_disable "MSIE [1-6]\.(?!.*SV1)";

    # 设定请求缓冲
    client_header_buffer_size    1k;
    large_client_header_buffers  44k;

    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;

    # 设定负载均衡的服务器列表
    upstream mysvr {
    	# weigth参数表示权值，权值越高被分配到的几率越大
    	# 本机上的Squid开启3128端口
    	server 192.168.8.1:3128 	weight=5;
    	server 192.168.8.2:80  		weight=1;
    	server 192.168.8.3:80  		weight=6;
    }

   server {
        listen	80;						# 侦听80端口
        server_name  www.xx.com;		# 定义使用www.xx.com访问
        access_log  logs/www.xx.com.access.log  main;		# 设定本虚拟主机的访问日志

    # 默认请求
    location / {
		root   /root;      						# 定义服务器的默认网站根目录位置
		index index.php index.html index.htm;   # 定义首页索引文件的名称
fastcgi_pass  localhost:9000;				
    	fastcgi_param  SCRIPT_FILENAME  $document_root/$fastcgi_script_name; 
		include /etc/nginx/fastcgi_params;
	}

    # 定义错误提示页面
    error_page   500 502 503 504 /50x.html;  
        location = /50x.html {
        root   /root;
    }

    # 静态文件，nginx自己处理
    location ~ ^/(images|javascript|js|css|flash|media|static)/ {
        root /var/www/virtual/htdocs;
        # 过期时间30天
        expires 30d;
}

    # PHP脚本请求全部转发到FastCGI处理，使用FastCGI默认配置
    location ~ \.php$ {
        root /root;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /home/www/www$fastcgi_script_name;
        include fastcgi_params;
}

    # 设定查看Nginx状态的地址
    location /NginxStatus {
        stub_status 			on;
        access_log              on;
        auth_basic              "NginxStatus";
        auth_basic_user_file  	conf/htpasswd;
}

    # 禁止访问 .htxxx 文件
    location ~ /\.ht {
        deny all;
    }
    }
}

## Nginx有哪些内置的全局变量？
$args						请求中的参数;
$content_length				HTTP请求信息里的"Content-Length";
$content_type				请求信息里的"Content-Type";
$document_root				针对当前请求的根路径设置值;
$document_uri				与$uri相同;
$host						http请求的域名
$http_user_agent			客户端agent信息;
$http_cookie				客户端cookie信息;
$limit_rate					对连接速率的限制;
$request_body_file			客户端请求主体信息的临时文件名;
$request_method				请求的方法，比如"GET"、"POST"等;
$remote_addr				客户端地址;
$remote_port				客户端端口号;
$remote_user				客户端用户名，认证用;
$request_filename			当前请求的文件路径名;
$request_body_file			客户端请求主体的临时文件名;
$request_uri				包含请求参数的原始URI，不包含主机名，如："/foo/bar.php?arg=baz";
$query_string				与$args相同;
$scheme						所用的协议，比如http或者是https;
$server_addr				服务器地址，如果没有用listen指明服务器地址，使用这个变量将发起一次系统调用以取得地址(造成资源浪费);
$server_name				请求到达的服务器名;
$server_port				请求到达的服务器端口号;
$uri						不带请求参数的当前URI，$uri不包含主机名，如"/foo/bar.html";

$fastcgi_script_name		这个变量等于一个以斜线结尾的请求URI加上fastcgi_index给定的参数。可以用这个变量代替SCRIPT_FILENAME 和PATH_TRANSLATED，以确定php脚本的名称。
如请求"/info/": 
 	fastcgi_index		index.php;  
fastcgi_param  		SCRIPT_FILENAME  	/home/www/scripts/php$fastcgi_script_name;
SCRIPT_FILENAME等于"/home/www/scripts/php/info/index.php"

## fastcgi_index配置的作用是什么？
语法：fastcgi_index file 
默认值：none 
使用字段：http, server, location 
如果URI以斜线结尾，文件名将追加到URI后面，这个值将存储在变量$fastcgi_script_name中。
例如：
fastcgi_index  index.php;
fastcgi_param  SCRIPT_FILENAME  /home/www/scripts/php$fastcgi_script_name;
请求"/page.php"的参数SCRIPT_FILENAME将被设置为"/home/www/scripts/php/page.php"，但是请求"/"则为"/home/www/scripts/php/index.php"。

## fastcgi_param配置的作用是什么？
语法：fastcgi_param parameter value 
默认值：none 
使用字段：http, server, location 
指定一些传递到FastCGI服务器的参数。可以使用字符串，变量，或者其组合，这里的设置不会继承到其他的字段，设置在当前字段会清除掉任何之前的定义。
下面是一个PHP需要使用的最少参数：
  	fastcgi_param  SCRIPT_FILENAME  	/home/www/scripts/php$fastcgi_script_name;  
fastcgi_param  QUERY_STRING     	$query_string;
PHP使用SCRIPT_FILENAME参数决定需要执行哪个脚本，QUERY_STRING包含请求中的某些参数。

如果要处理POST请求，则需要另外增加三个参数：
  	fastcgi_param  REQUEST_METHOD   $request_method;  
fastcgi_param  CONTENT_TYPE     $content_type;  
fastcgi_param  CONTENT_LENGTH   $content_length;

如果PHP在编译时带有--enable-force-cgi-redirect，则必须传递值为200的REDIRECT_STATUS参数：
fastcgi_param  REDIRECT_STATUS  200;

## fastcgi_pass配置的作用是什么？
语法：fastcgi_pass fastcgi-server 
默认值：none 
使用字段：http, server, location 
指定FastCGI服务器监听端口与地址。
可以是本机或者其它：
fastcgi_pass   localhost:9000;

使用Unix socket:
fastcgi_pass   unix:/tmp/fastcgi.socket;

同样可以使用一个upstream字段名称：
upstream backend  {  
server   localhost:1234;
} 
fastcgi_pass   backend;

## fastcgi_read_timeout配置的作用是什么？
语法：fastcgi_read_timeout time 
默认值：fastcgi_read_timeout 60 
使用字段：http, server, location 
前端FastCGI服务器的响应超时时间，如果有一些直到它们运行完才有输出的长时间运行的FastCGI进程，或者在错误日志中出现前端服务器响应超时错误，可能需要调整这个值。


## fastcgi_param的内容是什么？
即为fastcgi模块设置一些服务器环境变量：
fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;			#脚本文件请求的路径
fastcgi_param  QUERY_STRING       $query_string; 					#请求的参数;如?app=123
fastcgi_param  REQUEST_METHOD     $request_method; 					#请求的动作(GET,POST)
fastcgi_param  CONTENT_TYPE       $content_type; 					#请求头中的Content-Type字段
fastcgi_param  CONTENT_LENGTH     $content_length; 					#请求头中的Content-length字段

fastcgi_param  SCRIPT_NAME        $fastcgi_script_name; 			#脚本名称 
fastcgi_param  REQUEST_URI        $request_uri; 					#请求的地址不带参数
fastcgi_param  DOCUMENT_URI       $document_uri; 					#与$uri相同。 
fastcgi_param  DOCUMENT_ROOT      $document_root; #网站的根目录。在server配置中root指令中指定的值 
fastcgi_param  SERVER_PROTOCOL    $server_protocol; 	#请求使用的协议，通常是HTTP/1.0或HTTP/1.1 

fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;							#cgi 版本
fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;				#nginx 版本号，可修改、隐藏

fastcgi_param  REMOTE_ADDR        $remote_addr; 					#客户端IP
fastcgi_param  REMOTE_PORT        $remote_port; 					#客户端端口
fastcgi_param  SERVER_ADDR        $server_addr; 					#服务器IP地址
fastcgi_param  SERVER_PORT        $server_port; 					#服务器端口
fastcgi_param  SERVER_NAME        $server_name; 	#服务器名，域名在server配置中指定的server_name

//fastcgi_param  PATH_INFO           $path_info;						#可自定义变量

// PHP only, required if PHP was built with --enable-force-cgi-redirect
// fastcgi_param  REDIRECT_STATUS    200;

在php可打印出上面的服务环境变量：
如：echo $_SERVER['REMOTE_ADDR']


## 如何使用Nginx实现负载均衡和反向代理？
设定http服务器，利用它的反向代理功能提供负载均衡支持
http {
     #设定mime类型,类型由mime.type文件定义
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;

    #设定日志格式
    access_log    /var/log/nginx/access.log;

    #其他配置，略

    #设定负载均衡的服务器列表
    upstream mysvr {
    	#weigth参数表示权值，权值越高被分配到的几率越大
    	server 192.168.8.1x:3128 weight=5;#本机上的Squid开启3128端口
    	server 192.168.8.2x:80  weight=1;
    	server 192.168.8.3x:80  weight=6;
    }

   	upstream mysvr2 {
    	#weigth参数表示权值，权值越高被分配到的几率越大
    	server 192.168.8.x:80  weight=1;
    	server 192.168.8.x:80  weight=6;
    }

   #第一个虚拟服务器
   server {
    	#侦听192.168.8.x的80端口
        listen       80;
        server_name  192.168.8.x;

      	#对aspx后缀的进行负载均衡请求
    	location ~ .*\.aspx$ {
         	root   /root;      						#定义服务器的默认网站根目录位置
          	index index.php index.html index.htm;   #定义首页索引文件的名称
          	proxy_pass  http://mysvr ;				#请求转向mysvr定义的服务器列表

          	# 反向代理的配置
          	proxy_redirect off;

          	#后端的Web服务器可以通过X-Forwarded-For获取用户真实IP
          	proxy_set_header Host $host;
          	proxy_set_header X-Real-IP $remote_addr;
          	proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
          	client_max_body_size 10m;    		#允许客户端请求的最大单文件字节数
          	client_body_buffer_size 128k;  		#缓冲区代理缓冲用户端请求的最大字节数
          	proxy_connect_timeout 90;  			#nginx跟后端服务器连接超时时间(代理连接超时)
          	proxy_send_timeout 90;        		#后端服务器数据回传时间(代理发送超时)
          	proxy_read_timeout 90;         		#连接成功后，后端服务器响应时间(代理接收超时)
          	proxy_buffer_size 4k;             #设置代理服务器（nginx）保存用户头信息的缓冲区大小
          	proxy_buffers 4 32k;         #proxy_buffers缓冲区，网页平均在32k以下的话，这样设置
          	proxy_busy_buffers_size 64k;    #高负荷下缓冲大小（proxy_buffers*2）
          	proxy_temp_file_write_size 64k;  #设定缓存文件夹大小，大于这个值，将从upstream服务器传
       }
	}
}


## 什么是Nginx重写？与Location有什么区别？Location的匹配规则是什么样的？
rewrite功能就是，使用nginx提供的全局变量或自己设置的变量，结合正则表达式和标志位实现url重写以及重定向。rewrite只能放在server{},location{},if{}中，并且只能对域名后边的除去传递的参数外的字符串起作用。如，http://seanlook.com/a/we/index.php?id=1&u=str 只对/a/we/index.php重写。
如果想对域名或参数字符串起作用，可以使用全局变量匹配，也可以使用proxy_pass反向代理。

Rewrite标志位：
last 			相当于Apache的[L]标记，表示完成rewrite
break			停止执行当前虚拟主机的后续rewrite指令集
redirect		返回302临时重定向，地址栏会显示跳转后的地址
permanent		返回301永久重定向，地址栏会显示跳转后的地址

Rewrite实例：
// 应用于Server
server {
listen 80;
server_name start.igrow.cn;
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

rewrite和location：
rewrite和location都能实现跳转，主要区别在于rewrite是在同一域名内更改获取资源的路径，而location是对一类路径做控制访问或反向代理，可以proxy_pass到其他机器。很多情况下rewrite也会写在location里，它们的执行顺序是：
（1）执行server块的rewrite指令
（2）执行location匹配
（3）执行选定的location中的rewrite指令
如果其中某步URI被重写，则重新循环执行1-3，直到找到真实存在的文件；循环超过10次，则返回500 Internal Server Error错误。

正则匹配会覆盖普通匹配，location的执行逻辑跟location的编辑顺序无关。
语法格式：location [=|~|~*|^~|@] /uri/ { … } 

=		表示精确匹配
~ 		区分大小写匹配
~* 		不区分大小写匹配
!~		区分大小写不匹配
!~* 	不区分大小写不匹配
^ 		以什么开头的匹配
$ 		以什么结尾的匹配
^~ 		表示uri以某个常规字符串开头，不是正则匹配，优先级高于正则
/ 		通用匹配,如果没有其它匹配,任何请求都会匹配到
* 		代表任意字符

. 		匹配除换行符以外的任意字符
?		重复0次或1次
+		重复1次或更多次
*		重复0次或更多次
\d		匹配数字
{n}		重复n次
{n,}	重复n次或更多次
[c]		匹配单个字符c
[a-z]	匹配a-z小写字母的任意一个
\		转义字符

-f和!-f		判断是否存在文件
-d和!-d		判断是否存在目录
-e和!-e		判断是否存在文件或目录
-x和!-x		判断文件是否可执行


例：
实际使用中一般至少有三个匹配规则定义，如下：
/* 
直接匹配网站根，通过域名访问网站首页比较频繁，使用这个会加速处理，官网如是说。
 这里是直接转发给后端应用服务器了，也可以是一个静态首页
 第一个必选规则
*/
location = / {
    proxy_pass http://tomcat:8080/index
}

/*
 第二个必选规则是处理静态文件请求，这是nginx作为http服务器的强项
 有两种配置模式，目录匹配或后缀匹配,任选其一或搭配使用
*/
location ^~ /static/ {
    root /webroot/static/;
}
location ~* \.(gif|jpg|jpeg|png|css|js|ico)$ {
    root /webroot/res/;
}

/*
 第三个规则就是通用规则，用来转发动态请求到后端应用服务器
 非静态文件请求就默认是动态请求，自己根据实际把握
 毕竟目前的一些框架的流行，带.php,.jsp后缀的情况很少了
*/
location / {
    proxy_pass http://tomcat:8080/
}

## 为什么Nginx反向代理能够提高性能？
对于后端是动态服务来说，比如Java和PHP。这类服务器（如JBoss和PHP-FPM）的IO处理能力往往不高。Nginx有个好处是它会把Request在读取完整之前buffer住，这样交给后端的就是一个完整的HTTP请求，从而提高后端的效率，而不是断断续续的传递（互联网上连接速度一般比较慢）。同样，Nginx也可以把response给buffer住，同样也是减轻后端的压力。


## Nginx的模块及工作原理是怎样的？是如何有FastCGI配合的？
Nginx由内核和模块组成，内核的设计非常简洁，仅仅通过查找配置文件将客户端请求映射到一个location block（location是Nginx配置中的一个指令，用于URL匹配），而在这个location中所配置的每个指令将会启动不同的模块去完成相应的工作。

Nginx的模块直接被编译进Nginx，因此属于静态编译方式。启动Nginx后，Nginx的模块被自动加载，不像Apache首先将模块编译为一个so文件，然后在配置文件中指定是否进行加载。在解析配置文件时，Nginx的每个模块都有可能去处理某个请求，但是同一个处理请求只能由一个模块来完成。 

在工作方式上，Nginx分为单工作进程和多工作进程两种模式。在单工作进程模式下，除主进程外，还有一个工作进程，工作进程是单线程的；在多工作进程模式下，每个工作进程包含多个线程。Nginx默认为单工作进程模式。

Nginx不支持对外部程序的直接调用或者解析，所有的外部程序（包括PHP）必须通过FastCGI接口来调用。FastCGI接口在Linux下是socket（这个socket可以是文件socket，也可以是ip socket）。

为了调用CGI程序，还需要一个FastCGI的wrapper（wrapper可以理解为用于启动另一个程序的程序），这个wrapper绑定在某个固定socket上，如端口或者文件socket。当Nginx将CGI请求发送给这个socket的时候，通过FastCGI接口，wrapper接收到请求，然后Fork(派生）出一个新的线程，这个线程调用解释器或者外部程序处理脚本并读取返回数据；接着，wrapper再将返回的数据通过FastCGI接口，沿着固定的socket传递给Nginx；最后，Nginx将返回的数据（html页面或者图片）发送给客户端。这就是Nginx+FastCGI的整个运作过程

FastCGI接口方式在脚本解析服务器上启动一个或者多个守护进程对动态脚本进行解析，这些进程就是FastCGI进程管理器，或者称为FastCGI引擎，如PHP-FPM。因此HTTPServer完全解放出来，可以更好地进行响应和并发处理。
其实，Nginx就是一个反向代理服务器。Nginx通过反向代理功能将动态请求转向后端php-fpm（wrapper），从而实现对PHP的解析支持，这就是Nginx实现PHP动态解析的原理。

其整体工作流程：
1) FastCGI进程管理器php-fpm自身初始化，启动主进程php-fpm和启动start_servers个CGI 子进程。
主进程php-fpm主要是管理fastcgi子进程，监听9000端口。
fastcgi子进程等待来自Web Server的连接。

当客户端请求到达Web Server Nginx时，Nginx通过location指令，将所有以php为后缀的文件都交给127.0.0.1:9000来处理，即Nginx通过location指令，将所有以php为后缀的文件都交给127.0.0.1:9000来处理。

FastCGI进程管理器PHP-FPM选择并连接到一个子进程CGI解释器。Web server将CGI环境变量和标准输入发送到FastCGI子进程。

FastCGI子进程完成处理后将标准输出和错误信息从同一连接返回Web Server。当FastCGI子进程关闭连接时，请求便告处理完成。

5) FastCGI子进程接着等待并处理来自FastCGI进程管理器（运行在 WebServer中）的下一个连接。


## Nginx支持的IO模型有哪些？
Nginx支持如下处理连接的方法（I/O复用方法），这些方法可以通过use指令指定：
（1）select 如果当前平台没有更有效的方法，它是编译时默认的方法。可以使用配置参数–with-select_module 和 –without-select_module来启用或禁用这个模块。
（2）poll 如果当前平台没有更有效的方法，它是编译时默认的方法。可以使用配置参数–with-poll_module和–without-poll_module来启用或禁用这个模块。
（3）kqueue 高效的方法，使用于FreeBSD 4.1+、 OpenBSD 2.9+、NetBSD 2.0和MacOS X.。使用双处理器的MacOS X系统使用kqueue可能会造成内核崩溃。
（4）epoll 高效的方法，使用于Linux内核2.6版本及以后的系统。在某些发行版本中，如SuSE 8.2, 有让2.4版本的内核支持epoll的补丁。
（5）rtsig 可执行的实时信号，使用于Linux内核版本2.2.19以后的系统。默认情况下整个系统中不能出现大于1024个POSIX实时(排队)信号。这种情况对于高负载的服务器来说是低效的；所以有必要通过调节内核参数 /proc/sys/kernel/rtsig-max来增加队列的大小。可是从Linux内核版本2.6.6-mm2开始， 这个参数就不再使用了，并且对于每个进程有一个独立的信号队列，这个队列的大小可以用 RLIMIT_SIGPENDING 参数调节。当这个队列过于拥塞，nginx就放弃它并且开始使用poll方法来处理连接直到恢复正常。
（6）/dev/poll 高效的方法，使用于 Solaris 7 11/99+, HP/UX 11.22+ (eventport), IRIX 6.5.15+ 和 Tru64 UNIX 5.1A+.
（7）eventport 高效的方法，使用于 Solaris 10。
在linux下面，只有epoll是高效的方法。
