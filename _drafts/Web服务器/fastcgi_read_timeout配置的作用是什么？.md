# fastcgi_read_timeout配置的作用是什么？

语法：fastcgi_read_timeout time 
默认值：fastcgi_read_timeout 60 
使用字段：http, server, location 
前端FastCGI服务器的响应超时时间，如果有一些直到它们运行完才有输出的长时间运行的FastCGI进程，或者在错误日志中出现前端服务器响应超时错误，可能需要调整这个值。