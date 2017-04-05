# MySQL中有哪几种事务隔离级别？

1）`READ UNCOMMITTED`	# 可读取其他事务未提交的数据（脏读）
2）`READ COMMITTED`		# 只能读取已提交的数据，但是不可重复读（避免脏读）
3）`REPEATABLE READ`		# 可重复读
// 用户A查询完之后，用户B将无法更新用户A所查询到的数据集中的任何数据（但是可以更新、插入和删除用户A查询到的数据集之外的数据），直到用户A事务结束才可以进行更新，这样就有效的防止了用户在同一个事务中读取到不一致的数据。
4）`SERIALIZABLE`			# 事务串行化，必须等待当前事务执行完，其他事务才可以执行写操作，`有多个事务同时设置SERIALIZABLE时会产生死锁`：
ERROR 1213 (40001): Deadlock found when trying to get lock; try restarting transaction
这是四个隔离级别中限制最大的级别。因为并发级别较低，所以应只在必要时才使用该选项。

使用事务时设置级别：
START TRANSACTION
SET [SESSION | GLOBAL] TRANSACTION ISOLATION LEVEL {READ UNCOMMITTED | READ COMMITTED | REPEATABLE READ | SERIALIZABLE}
COMMIT
ROLLBACK