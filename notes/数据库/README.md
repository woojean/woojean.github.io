# EXPLAIN

EXPLAIN命令用于查看查询优化器选择的查询计划。被标记了EXPLAIN的查询会返回关于执行计划中每一步的信息，而不是执行它。(实际上，如果查询在from子句中包括子查询，那么MySQL实际上会执行子查询)
EXPLAIN只是一个近似结果。

## EXPLAIN的结果
`id` 
标识SELECT所属的行，如果在语句中没有子查询或者联合查询，那么只会有一行（因为只有1个SELECT），且id值为1.

`select_type` 
1.SIMPLE 意味着该查询不包含子查询和UNION，如果查询有任何复杂的子部分，则最外层部分标记为PRIMARY（id为1的查询）。
2.SUBQUERY 包含在SELECT列表中的子查询（即不是位于FROM子句中的查询）；
3.DERIVED 包含在FROM子句中的子查询；
4.UNION 在UNION查询中的第二个和随后的SELECT被标记为UNION；
5.UNION RESULT 用来从UNION的匿名临时表检索结果的SELECT；

`table` 
表示正在访问哪个表（包括匿名临时表，比如derived1），可以在这一列从上往下观察MySQL的关联优化器为查询选择的关联顺序。

`type` 
访问类型，决定如何查找表中的行，从最差到最优排列如下：
1.ALL 全表扫描；
2.index 按索引次序全表扫描，主要优点是避免了排序，缺点是当随机访问时开销非常大；如果在Extra列中有Using index，说明MySQL正在使用覆盖索引，即只扫描索引的数据。
3.range 有限制的索引扫描（带有between或where >等条件的查询）；
注意，当使用IN()、OR()来查询时，虽然也显示范围扫描，但是其实是相当不同的访问类型，在性能上有重要的差异。
4.ref 索引查找，返回所有匹配某个值的行，这个值可能是一个常数或者来自多表查询前一个表里的结果值；
5.eq_ref 也是索引查找，且MySQL知道最多只返回一条符合条件的记录（使用主键或者唯一性索引查询），MySQL对于这类访问优化的非常好；
6.const、system 当MySQL能对查询的某部分进行优化并将其转换为一个常量时，它就会使用这些访问类型；
7.NULL 意味着MySQL能在优化阶段分解查询语句，在执行阶段甚至用不着再访问表或者索引；

`possible_keys`
显示查询可以使用哪些索引。

`key` 
显示MySQL决定采用哪个索引来优化对表的访问。

`key_len`
显示MySQL在索引里使用的字节数。

`ref`
显示之前的表在key列记录的索引中查找值所用的列或常量。

`rows`
MySQL估计为了找到所需的行而要读取的行数。

`filtered`
在使用EXPLAIN EXTENED时才会出现，查询结果记录数占总记录数的百分比。

`Extra`
1.Using index：表示将使用覆盖索引；
2.Using where：意味着MySQL服务器将在存储引擎检索后再进行过滤；
3.Using temporary：意味着MySQL在对查询结果排序时会使用一个临时表；
4.Using filesort：意味着MySQL会对结果使用一个外部索引排序，而不是按索引次序从表里读取行；
5.Range checked for each record ...：意味着没有好用的索引；