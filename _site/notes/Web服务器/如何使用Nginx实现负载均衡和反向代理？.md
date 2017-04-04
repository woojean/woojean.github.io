# 如何使用Nginx实现负载均衡和反向代理？

设定http服务器，利用它的反向代理功能提供负载均衡支持

```
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
```