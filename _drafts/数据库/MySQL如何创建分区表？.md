# MySQL如何创建分区表？

分区是一种粗粒度，简易的索引策略，适用于大数据的过滤场景。`对于大数据(如10TB)而言，索引起到的作用相对小`，因为索引的空间与维护成本很高，另外如果不是索引覆盖查询，将导致回表，造成大量磁盘IO。
`分区表分为RANGE、LIST、HASH、KEY四种类型`,并且分区表的索引是可以局部针对分区表建立的。
用RANGE创建分区表：

```
CREATE TABLE sales (
  id INT AUTO_INCREMENT,
  amount DOUBLE NOT NULL,
  order_day DATETIME NOT NULL,
  PRIMARY KEY(id, order_day)
) ENGINE=Innodb PARTITION BY RANGE(YEAR(order_day)) (
  PARTITION p_2010 VALUES LESS THAN (2010),
  PARTITION p_2011 VALUES LESS THAN (2011),
  PARTITION p_2012 VALUES LESS THAN (2012),
  PARTITION p_catchall VALUES LESS THAN MAXVALUE);
```
如果这么做，则order_day必须包含在主键中，且会产生一个问题：当年份超过阈值，到了2013，2014时需要手动创建这些分区，更好的方法是使用HASH：
```
CREATE TABLE sales ( 
  id INT PRIMARY KEY AUTO_INCREMENT,
  amount DOUBLE NOT NULL,
  order_day DATETIME NOT NULL
) ENGINE=Innodb PARTITION BY HASH(id DIV 1000000);
```
这种分区表示每100W条数据建立一个分区，且没有阈值范围的影响。

`如果想为一个表创建分区，这个表最多只能有一个唯一索引`（主键也是唯一索引）。如果没有唯一索引，可指定任何一列为分区列；否则就只能指定唯一索引中的任何一列为分区列。查询时需用到分区的列，不然会遍历所有的分区，比不分区的查询效率还低，MySQL支持子分区。
在表建立后也可以新增、删除、合并分区。