# MySQL显式锁定方式

MySQL也支持LOCK TABLES和UNLOCK TABLES语句，这是在服务器层实现的，但它们不能代替事务处理，如果应用到事务，还是应该选择事务型存储引擎。
（建议：除了在事务中禁用了AUTOCOMMIT时可以使用LOCK TABLE之外，其他任何时候都不要显式地执行LOCK TABLES，不管使用的是什么存储引擎，因为LOCK TABLE和事务之间相互影响时，问题会变得非常复杂）

InnoDB也支持通过特定的语句进行显式锁定，这些语句不属于SQL规范，如：

```
SELECT ... LOCK IN SHARE MODE
SELECT ... FOR UPDATE
```

