# innodb_buffer_pool_size设置什么？

innodb_buffer_pool_size这个参数主要作用是缓存`innodb表的索引、数据、插入数据时的缓冲`。
默认值：128M
专用mysql服务器设置的大小： 操作系统内存的70%-80%最佳。
设置方法：
my.cnf文件
innodb_buffer_pool_size = 6G
此外，这个参数是非动态的，要修改这个值，需要重启mysqld服务。

如果因为内存不够，MySQL无法启动，就会在错误日志中出现如下报错：
InnoDB: mmap(137363456 bytes) failed; errno 12