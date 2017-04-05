# 如何使用Redis的事务功能？

redis 127.0.0.1:6379> MULTI					# 开始一个事务
OK
redis 127.0.0.1:6379> SET book-name "Mastering C++ in 21 days"		
QUEUED										# 事务入队
redis 127.0.0.1:6379> GET book-name
QUEUED										# 事务入队
redis 127.0.0.1:6379> SADD tag "C++" "Programming" "Mastering Series"
QUEUED										# 事务入队
redis 127.0.0.1:6379> SMEMBERS tag
QUEUED										# 事务入队
redis 127.0.0.1:6379> EXEC					# 执行所有事务
1) OK
2) "Mastering C++ in 21 days"
3) (integer) 3
4) 1) "Mastering Series"
   2) "C++"
   3) "Programming"

常用命令：
（1）DISCARD 								# 取消事务，放弃执行事务块内的所有命令
（2）EXEC 									# 执行所有事务块内的命令
（3）MULTI 									# 标记一个事务块的开始
（4）UNWATCH 								# 取消WATCH命令对所有key的监视
（5）WATCH key [key ...] 					# 监视一个（或多个）key ，如果在事务执行之前这个（或这些） key被其他命令所改动，那么事务将被打断