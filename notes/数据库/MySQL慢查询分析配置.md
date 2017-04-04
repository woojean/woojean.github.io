# MySQL慢查询分析配置

```
long_query_time = 1
log-slow-queries = /data/var/mysql_slow.log
```

log-queries-not-using-indexes选项用于控制记录所有没有使用索引的查询。

使用mysqlsla分析慢查询日志，略。



