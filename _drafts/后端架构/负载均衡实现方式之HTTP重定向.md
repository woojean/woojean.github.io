# 负载均衡实现方式之HTTP重定向

## HTTP重定向
```
HTTP/1.1 302 Found
...
Location: http://xxx.com/xxxx
```

基于HTTP重定向的负载均衡实现比较简单，通过Web程序即可实现：
```
<?php
  $domains = [
    'www1.xxx.com',
    'www2.xxx.com',
    'www3.xxx.com',
  ];
  
  $index = substr(microtime(),5,3) % count($domains);
  $domain = $domains[$index];

  header("Location:http://$domain");
```

注意，这里采用随机方式，而没有采用轮询（RR，Round Robin）的方式，因为HTTP协议是无状态的，如果要实现轮询，需要额外的工作，比如维持一个互斥的计数变量（任何时候只有一个请求可以修改），对性能不利。

Apache的mod_rewrite模块可以支持RR重定向：
```
<VirtualHost *:80>
  DocumentRoot /data/www/highperfweb/htdocs
  ServerName www.xxx.com
  RewriteEngine on
  RewriteMap    lb prg:/data/www/lb.pl
  RewriteRule   ^/(.+)$ $(lb:$l)
</VirtualHost>
```
/data/www/lb.pl是一个脚本，实现了轮询均衡负载的逻辑。

顺序调度的性能总是比不上随机调度的性能，好处在于可以实现绝对的均衡。

注意，实际生产环境中次数的均衡不一定等于负载的均衡，比如不同用户的访问深度是不一样的。