# PHP配置中的open_basedir配置项的作用是什么？

open_basedir是PHP配置中为了`防御跨目录进行文件（目录）读写`的配置，所有PHP中有关文件读、写的函数都会经过open_basedir的检查。实际上是一些目录的集合，在定义了open_basedir以后，`php可以读写的文件、目录都将被限制在这些目录中`。在linux下，不同的目录由“:”分割，如“/var/www/:/tmp/”。
注意用open_basedir指定的限制实际上是前缀,而不是目录名。

Apache+PHP配置方法有三种：
方法一：在php.ini里配置
open_basedir = .:/tmp/
方法二：在Apache配置的VirtualHost里设置(httpd-vhosts.conf)
php_admin_value open_basedir .:/tmp/
方法三：在Apache配置的Direcotry里设置
php_admin_value open_basedir .:/tmp/
关于三个配置方法的解释：
a、方法二的优先级高于方法一，也就是说方法二会覆盖方法一；方法三的优先级高于方法二，也就是说方法三会覆盖方法二；
b、配置目录里加了“/tmp/”是因为php默认的临时文件（如上传的文件、session等）会放在该目录，所以一般需要添加该目录，否则部分功能将无法使用；
c、配置目录里加了“.”是指运行php文件的当前目录，这样做可以避免每个站点一个一个设置；
d、如果站点还使用了站点目录外的文件，需要单独在对应VirtualHost设置该目录；