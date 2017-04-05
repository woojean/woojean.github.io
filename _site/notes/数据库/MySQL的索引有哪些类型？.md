# MySQL的索引有哪些类型？

索引是一种特殊的文件(InnoDB数据表上的索引是表空间的一个组成部分)，它们`包含着对数据表里所有记录的引用指针`。有了相应的索引之后，数据库会直接在索引中查找符合条件的选项。有以下索引类型：
（1）`普通索引`
这是最基本的索引，它没有任何限制，`MyIASM中默认的BTREE类型的索引`，也是大多数情况下用到的索引。
// 直接创建索引

```
CREATE INDEX index_name ON table(column(length))

// 修改表结构的方式添加索引
ALTER TABLE table_name ADD INDEX index_name ON (column(length))

// 创建表的时候同时创建索引
CREATE TABLE `table` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time` int(10) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
INDEX index_name (title(length))
)
// 删除索引
DROP INDEX index_name ON table
```

（2）`唯一索引`
索引列的值必须唯一，但允许有空值（注意和主键不同）。如果是组合索引，则列值的组合必须唯一，创建方法和普通索引类似。
```
// 创建唯一索引
CREATE UNIQUE INDEX indexName ON table(column(length))

// 修改表结构
ALTER TABLE table_name ADD UNIQUE indexName ON (column(length))

// 创建表的时候直接指定
CREATE TABLE `table` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time` int(10) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
UNIQUE indexName (title(length))
);
```

（3）`全文索引`
`仅可用于MyISAM表`。可以从CHAR、VARCHAR或TEXT列中作为CREATE TABLE语句的一部分被创建，或是随后使用ALTER TABLE或CREATE INDEX被添加。对于较大的数据集，将资料输入一个没有FULLTEXT索引的表中，然后创建索引，其速度比把资料输入现有FULLTEXT索引的速度更为快。不过切记对于大容量的数据表，生成全文索引是一个非常消耗时间非常消耗硬盘空间的做法。
```
// 创建表的适合添加全文索引
CREATE TABLE `table` (
`id` int(11) NOT NULL AUTO_INCREMENT ,
`title` char(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL ,
`time` int(10) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FULLTEXT (content)
);

// 修改表结构添加全文索引
ALTER TABLE article ADD FULLTEXT index_content(content)

// 直接创建索引
CREATE FULLTEXT INDEX index_content ON article(content)
```

（4）`单列索引`、`多列索引`
多个单列索引与单个多列索引的查询效果不同，因为执行查询时，MySQL只能使用一个索引，会从多个索引中选择一个限制最为严格的索引。

（5）`组合索引`
例如上表中针对title和time建立一个组合索引：
ALTER TABLE article ADD INDEX index_titme_time (title(50),time(10));
建立这样的组合索引，其实是`相当于分别建立了下面两组组合索引`：
–title,time
–title
为什么没有time这样的组合索引呢？这是因为MySQL`组合索引“最左前缀”`的结果。简单的理解就是只从最左面的开始组合。如下面的几个SQL所示：
```
// 使用到上面的索引
SELECT * FROM article WHREE title='测试' AND time=1234567890;		# 使用title,time索引
SELECT * FROM article WHREE utitle='测试';							# 使用title索引

// 不使用上面的索引
SELECT * FROM article WHREE time=1234567890;						
```
MySQL只对以下操作符才使用索引：<,<=,=,>,>=,between,in,以及某些时候的like(不以通配符%或_开头的情形)。而理论上每张表里面最多可创建16个索引。