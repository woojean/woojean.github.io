# mysql与mysqli有什么区别？

PHP-MySQL（mysql）是PHP操作MySQL数据库最原始的Extension，PHP-MySQLi（mysqli）的i代表 Improvement ，提供了相对进阶的功能，也增加了安全性。
mysql是非持继连接函数而`mysqli是永远连接函数`。也就是说mysql每次链接都会打开一个连接的进程而mysqli多次运行mysqli将使用同一连接进程,从而减少了服务器的开销。
如果使用new mysqli('localhost', usenamer', 'password', 'databasename')总是报错，Fatal error: Class 'mysqli' not found in ...那么要检查一下mysqli是不是开启的。`mysqli类不是默认开启的`，win下要改php.ini,去掉php_mysqli.dll前的;,linux下要把mysqli编译进去。
当然，如果mysql也需要永久连接的话，就可以使用mysql_pconnect()这个函数。