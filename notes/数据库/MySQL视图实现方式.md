# MySQL视图实现方式

MySQL中视图有两种实现方式：合并算法、临时表算法。
如果视图中包含GROUP BY、DISTINCT、聚合函数、UNION、子查询等，只要无法在原表记录和视图记录中建立一一映射，都将使用临时表算法来实现视图（未来版本中可能改变）。

可以通过EXPLAIN查看视图的实现方式：
```
EXPLAIN SELECT * FROM <VIEW>;
```
如果结果中select_type为DERIVED，则是采用临时表算法实现的。

也可以在创建视图的时候指定算法：
```
CREATE ALGORITHM=TEMPTABLE VIEW v1 AS SELECT * FROM sakila.actor;
```

可更新视图：指可以通过更新这个视图来更新视图涉及的相关表（更新、删除、写入）。被更新的列必须来自同一个表中。

所有使用临时表算法实现的视图都无法被更新。