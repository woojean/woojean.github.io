# MySQL配置的工作原理

MySQL从命令行参数或者配置文件中获取配置信息。服务器会读取配置文件的内容，删除所有注释和换行，然后和命令行选项一起处理。

在不同的操作系统上MySQL配置文件的位置不同，可以使用如下方式查看当前使用的配置文件的路径：
```
$ which mysqld
/usr/local/bin/mysqld

$ /usr/local/bin/mysqld --verbose --help | grep -A 1 'Default options'
Default options are read from the following files in the given order:
/etc/my.cnf /etc/mysql/my.cnf /usr/local/etc/my.cnf ~/.my.cnf
```

MySQL配置文件中下划线和横线等价。

不同的配置项有不同的作用域（服务器、会话、对象）。除了在配置文件中设置变量，有很多变量也可以在服务器运行时修改（MySQL关闭时可能会丢失）。

如果在服务器运行时修改了变量的全局值，这个值对当前会话和其他任何已经存在的会话都不起作用。

不同配置项可能有不同的单位值。

在配置文件中不能使用表达式。

可以为配置项赋予默认值DEFAULT，这将使配置项使用上一级的配置。

常用变量：
key_buffer_size：为键缓冲区分配指定空间（使用时才会分配）；
table_cache_size：可以缓存的表的数量，当线程打开新表时会检查，如果已满，则删除不常使用的表；
thread_cache_size：缓存的线程数；
query_cache_size：用来缓存查询；
read_buffer_size；
read_rnd_buffer_size；
sort_buffer_size;

应该始终通过监控来确认生产环境中变量的修改（基准测试是不够的，不是真实的工作负载）。

不要根据比率来调优，比如如果键缓存的命中率应该高于某个百分比，如果命中率过低，则应该增加缓存的大小。这是非常错误的意见。缓存命中率跟缓存是否过大或过小没有关系。

没有适合所有场景的最佳配置文件。

## 配置文件示例
```
[mysqld]
datadir                  = /var/lib/mysql
socket                   = /var/lib/mysql/mysql.sock
pid_file                 = /var/lib/mysql/mysql.pid
user                     = mysql
port                     = 3306
default_storage_engine   = InnoDB
 # InnoDB
innodb_buffer_pool_size  = <value>  # 缓存行、自适应哈希索引、插入缓存、锁等
innodb_log_file_size     = <value>
innodb_file_per_table    = 1
innodb_flush_method      = 0_DIRECT
 
 # MyISAM
key_buffer_size          = <value>

 # Logging
log_error                = /var/lib/mysql/mysql-error.log
slow_query_log           = /var/lib/mysql/mysql-slow.log

 # Other
tmp_table_size           = 32M
max_heap_table_size      = 32M
query_cache_type         = 0
query_cache_size         = 0
max_connections          = <value>
thread_cache             = <value>
table_cache              = <value>
open_files_limit         = 65535

[client]
socket                   = /var/lib/mysql/mysql.sock
port                     = 3306
```
很大的缓冲池会有一些问题，例如预热和关闭（涉及脏数据写回）都会花费很长的时间。
（详略）