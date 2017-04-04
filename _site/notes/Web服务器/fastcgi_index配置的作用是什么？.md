# fastcgi_index配置的作用是什么？

语法：fastcgi_index file 
默认值：none 
使用字段：http, server, location 
如果URI以斜线结尾，文件名将追加到URI后面，这个值将存储在变量$fastcgi_script_name中。
例如：
fastcgi_index  index.php;
fastcgi_param  SCRIPT_FILENAME  /home/www/scripts/php$fastcgi_script_name;
请求"/page.php"的参数SCRIPT_FILENAME将被设置为"/home/www/scripts/php/page.php"，但是请求"/"则为"/home/www/scripts/php/index.php"。