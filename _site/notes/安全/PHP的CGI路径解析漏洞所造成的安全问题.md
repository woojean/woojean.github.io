# PHP CGI路径解析漏洞所造成的安全问题

当访问：

```
http://www.xxx.com/path/test.jpg/notexist.php
```
时，若notexist.php不存在，会将test.jpg当做PHP进行解析。
这个漏洞的原因与在fastcgi方式下PHP获取环境变量的方式有关。php.ini中cgi.fix_pathinfo开关默认打开，在映射URI时，两个环境变量很重要：PATH_INFO和SCRIPT_FILENAME。上例中PATH_INFO为notexist.php，当cgi.fix_pathinfo打开的情况下，在映射URI时将递归查询路径确认文件的合法性，因为notexist.php不存在，所以将往前递归查询路径。（这个功能原本是为了解决/info.php/test这种URL，使其能够正确地解析到info.php上），此时SCRIPT_FILENAME需要检查文件是否存在，最终递归查询后确认为/path/test.jpg，而PATH_INFO此时还是notexist.php，从而造成最终执行时test.jpg会被当做PHP进行解析。