# fastcgi_pass配置的作用是什么？

语法：fastcgi_pass fastcgi-server 
默认值：none 
使用字段：http, server, location 
指定FastCGI服务器监听端口与地址。
可以是本机或者其它：
fastcgi_pass   localhost:9000;

使用Unix socket:
fastcgi_pass   unix:/tmp/fastcgi.socket;

同样可以使用一个upstream字段名称：
```
upstream backend  {  
server   localhost:1234;
} 
fastcgi_pass   backend;
```