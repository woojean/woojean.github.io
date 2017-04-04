# 什么是Nginx重写？与Location有什么区别？Location的匹配规则是什么样的？

rewrite功能就是，使用nginx提供的全局变量或自己设置的变量，结合正则表达式和标志位实现url重写以及重定向。rewrite只能放在server{},location{},if{}中，并且只能对域名后边的除去传递的参数外的字符串起作用。如，http://seanlook.com/a/we/index.php?id=1&u=str 只对/a/we/index.php重写。
如果想对域名或参数字符串起作用，可以使用全局变量匹配，也可以使用proxy_pass反向代理。

Rewrite标志位：
last 			相当于Apache的[L]标记，表示完成rewrite
break			停止执行当前虚拟主机的后续rewrite指令集
redirect		返回302临时重定向，地址栏会显示跳转后的地址
permanent		返回301永久重定向，地址栏会显示跳转后的地址

Rewrite实例：
```
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
```
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
*    代表任意字符

 . 	匹配除换行符以外的任意字符
 ?	重复0次或1次
 +  重复1次或更多次
  *重复0次或更多次
  \d匹配数字
  {n}重复n次
  {n,}重复n次或更多次
  [c]匹配单个字符c
  [a-z]匹配a-z小写字母的任意一个
  \转义字符

  -f和!-f判断是否存在文件
  -d和!-d判断是否存在目录
  -e和!-e判断是否存在文件或目录
  -x和!-x判断文件是否可执行


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