# 如何进行MySQL数据备份与恢复操作？

（1）备份：使用`mysqldump`导出数据

```
mysqldump -u 用户名 -p 数据库名 [表名] > 导出的文件名
mysqldump -uroot -p test mytable > mytable.20140921.bak.sql
```

（2）恢复：导入备份数据
```
mysql -uroot -p test < mytable.20140921.bak.sql
```

（3）恢复：导入备份数据之后发送的写操作。先使用mysqlbinlog导出这部分写操作SQL(基于时间点或位置)
// 导出2014-09-21 09:59:59之后的binlog：
```
mysqlbinlog --database="test" --start-date="2014-09-21 09:59:59" /var/lib/mysql/mybinlog.000001 > binlog.data.sql

// 导出起始id为123456之后的binlog：
mysqlbinlog --database="test" --start-position="123456" /var/lib/mysql/mybinlog.000001 > binlog.data.sql

// 把要恢复的binlog导入db
mysql -uroot -p test < binlog.data.sql
```