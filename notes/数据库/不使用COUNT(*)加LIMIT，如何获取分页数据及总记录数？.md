# 不使用COUNT(*)加LIMIT，如何获取分页数据及总记录数？

在很多分页的程序中都这样写:

```
SELECT COUNT(*) from ‘table’ WHERE ......;  	# 查出符合条件的记录总数
SELECT * FROM ‘table’ WHERE ...... limit M,N; 	# 查询当页要显示的数据
```
这样的语句可以改成:
```
SELECT SQL_CALC_FOUND_ROWS * FROM ‘table’ WHERE ......  limit M, N;
SELECT FOUND_ROWS();
```
这样只要执行一次较耗时的复杂查询可以同时得到与不带limit同样的记录条数。
第二个SELECT返回一个数字，指示了在没有LIMIT子句的情况下，第一个SELECT返回了多少行。