# fastcgi_param配置的作用是什么？

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