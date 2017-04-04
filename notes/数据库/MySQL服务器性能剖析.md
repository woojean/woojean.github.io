# MySQL服务器性能剖析

性能：完成某件任务所需要的时间度量，性能即响应时间。
性能剖析（profiling）：测量服务器的时间花费在哪里。
性能剖析的步骤：测量任务所花费的时间，然后对结果进行统计和排序，将重要的任务排到前面。

## 对应用程序进行性能剖析
使应用的可测量，性能剖析的代码会导致服务器变慢，可以采用`随机采样`：
```
<?php
$profiling_enabled = rand(0,100) > 99;
...
```

## 剖析MySQL查询
在MySQL中可以通过设置long_query_time为0来捕获所有的查询，目前查询的相应时间单位已经精确到微秒级。

在MySQL中，慢查询日志是开销最低、精度最高的测量查询时间的工具。对CPU的开销很少，但是可能消耗大量的磁盘空间。

也可以通过tcpdump抓取TCP包，然后使用pt-query-digest --type=tcpdump选项来解析并分析查询。

（使用pt-query-digest解析慢查询日志，略）

剖析单条查询：
```
SET profiling = 1;
SELECT * ...
SHOW PROFILES;               # 所有查询的统计
SHOW PROFILE FOR QUERY 1;   # 单条查询的详情，给出查询执行的每个步骤及花费的时间
```
也可以不使用SHOW PROFILE命令，而是直接查询INFOMATION_SCHEMA中对应的表，这样可以自定义输出数据的格式（按特定字段排序等）。

可以使用SHOW STATUS命令返回查询计数器（但无法给出消耗了多少时间）。最有用的计数器是句柄计数器、临时文件、表计数器等。
```
FLUSH STATUS;
SELECT * FROM ...
SHOW STATUS WHERE Variable_name LIKE 'Handler%' OR Variable_name LIKE 'Created';
```
SHOW STATUS本身也会创建一个临时表，而且也会通过句柄操作访问此临时表，也会影响到SHOW STATUS结果中对应的数字。

`Performance Schema`：5.5中新增，目前还在快速开发中