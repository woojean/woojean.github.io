# Nginx有哪些内置的全局变量？

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