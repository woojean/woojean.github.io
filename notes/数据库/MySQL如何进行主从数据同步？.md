# MySQL如何进行主从数据同步？

复制机制（Replication）
master通过复制机制，将master的写操作通过binlog传到slave生成中继日志(relaylog)，slave再将中继日志redo，使得主库和从库的数据保持同步

复制相关的3个Mysql线程（属于slave主动请求拉取的模式）
（1）slave上的I/O线程：向master请求数据
（2）master上的Binlog Dump线程：读取binlog事件并把数据发送给slave的I/O线程
（3）slave上的SQL线程：读取中继日志并执行，更新数据库

相关监控命令
show processlist		# 查看MySQL进程信息，包括3个同步线程的当前状态
show master status		# 查看master配置及当前复制信息
show slave status		# 查看slave配置及当前复制信息