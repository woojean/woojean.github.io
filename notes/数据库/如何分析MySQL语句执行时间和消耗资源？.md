# 如何分析MySQL语句执行时间和消耗资源？

```
SET profiling=1; 					# 启动profiles，默认是没开启的
SELECT * FROM customers;			# 执行要分析的SQL语句

SHOW profiles;						# 查看SQL语句具体执行步骤及耗时

SHOW profile cpu,block io FOR QUERY 41;		#	查看ID为41的查询在各个环节的耗时和资源消耗
```