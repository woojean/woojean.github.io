# cgi.fix_pathinfo配置项的作用是什么？

如果webserver为nginx，则须在PHP的配置文件php.ini中配置cgi.fix_pathinfo = 0，防止nginx文件解析漏洞。
在cgi.fix_pathinfo = 1的情况下，假设有如下的 URL：http://xxx.net/foo.jpg，当访问 http://xxx.net/foo.jpg/a.php 时，foo.jpg 将会被执行，如果 foo.jpg 是一个普通文件，那么 foo.jpg 的内容会被直接显示出来，但是如果把一段 php 代码保存为 foo.jpg，那么问题就来了，这段代码就会被直接执行。