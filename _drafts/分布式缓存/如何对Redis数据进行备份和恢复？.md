# 如何对Redis数据进行备份和恢复？

创建当前数据库的备份，将在redis安装目录中创建dump.rdb文件
redis 127.0.0.1:6379> SAVE 					
OK

恢复数据：将备份文件 (dump.rdb) 移动到redis安装目录并启动服务即可
redis 127.0.0.1:6379> CONFIG GET dir		# 获取redis目录
1) "dir"
2) "/usr/local/redis/bin"

在后台备份数据
127.0.0.1:6379> BGSAVE
Background saving started