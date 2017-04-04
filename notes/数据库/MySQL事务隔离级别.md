# MySQL事务隔离级别

ACID：atomicity(原子性)、consistency(一致性)、isolation(隔离性)、durability(持久性)。

隔离级别：SQL标准定义了4种隔离级别，每一种级别都规定了在一个事务中所做的修改，哪些在事务内和事务间是可见的，哪些是不可见的。
1. `READ UNCOMMITED` 
   事务中的修改，即使没有提交，对其他事务也是可见的。（因此会产生脏读）
2. `READ COMMITTED`
   一个事务只能看见已经提交的事务所做的修改。
3. `REPEATABLE READ`
   这是MySQL默认的事务隔离级别，保证在同一个事务中多次读取同样记录的结果是一样的。理论上该级别无法避免幻读的问题，InnoDB通过多版本并发控制解决了幻读的问题。
4. `SERIALIZABLE`
   强制事务串行执行，会在读取的每一行数据上都加锁，可能导致大量的超时和锁争用问题。

可以在配置文件中设置整个数据库的隔离级别，也可以只改变当前会话的隔离级别：
SET SESSION TRANSACTION LEVEL READ COMMITTED;