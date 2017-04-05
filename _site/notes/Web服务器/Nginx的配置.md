# Nginx的配置

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
```
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
```