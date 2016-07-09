## MySQL有哪些数据类型？
串数据类型
CHAR 1~255个字符的定长串，长度必须在创建时指定，否则MySQL假定为CHAR(1)
ENUM 接收最多64K个串组成的一个预定义集合的某个串
LONGTEXT 与TEXT相同，但最大长度为4GB
MEDIUMTEXT 与TEXT相同，但最大长度为16K
SET 接受最多64个串组成的一个预定义集合的零个或多个串
TEXT 最大长度为64K的变长文本
TINYTEXT 与TEXT相同，但最大长度为255字节
VARCHAR 长度可变，最多不超过255字节。如果在创建时指定为VARCHAR(n)，则可存储0~n个字符的变长串

数值数据类型
BIT 					位字段，1~64位
BIGINT 				整数值，8个字节，-2^63到2^63-1的整型数据
BOOLEAN (BOOL) 	布尔值，为0或1
DECIMAL (DEC) 		精度可变的浮点数
DOUBLE 			双精度浮点数
FLOAT 				单精读浮点数
INT (INTEGER) 		整数值，4个字节，-2^31到2^31–1的整型数据
MEDIUMINT 		整数值，3个字节
REAL 				4字节的浮点值
SMALLINT 			整数值，2个字节
TINYINT 			整数值，1个字节

日期和时间数据类型
DATE 				表示1000-01-01~9999-12-31的日期，格式为YYYY-MM-DD
DATETIME 			DATE和TIME的组合
TIMESTAMP 		功能和DATETIME相同，但范围较小
TIME 				格式为HH:MM:SS
YEAR 				1970~2069年

二进制数据类型
BLOB 				最大长度为64K
MEDIUMBLOB 		最大长度为16M
LONGBLOB 		最大长度为4GB
TINYBLOB 			最大长度为255字节


## 一条MySQL语句的执行顺序是什么样的？
MySQL的语句一共分为11步，如下图所标注的那样，最先执行的总是FROM操作，最后执行的是LIMIT操作。其中每一个操作都会产生一张虚拟的表，这个虚拟的表作为一个处理的输入，只是这些虚拟的表对用户来说是透明的，但是只有最后一个虚拟的表才会被作为结果返回。如果没有在语句中指定某一个子句，那么将会跳过相应的步骤。
1.	FORM: 对FROM的左边的表和右边的表计算笛卡尔积。产生虚表VT1
2.	ON: 对虚表VT1进行ON筛选，只有那些符合<join-condition>的行才会被记录在虚表VT2中。
3.	JOIN： 如果指定了OUTER JOIN（比如left join、 right join），那么保留表中未匹配的行就会作为外部行添加到虚拟表VT2中，产生虚拟表VT3, rug from子句中包含两个以上的表的话，那么就会对上一个join连接产生的结果VT3和下一个表重复执行步骤1~3这三个步骤，一直到处理完所有的表为止。
4.	WHERE： 对虚拟表VT3进行WHERE条件过滤。只有符合<where-condition>的记录才会被插入到虚拟表VT4中。
5.	GROUP BY: 根据group by子句中的列，对VT4中的记录进行分组操作，产生VT5.
6.	CUBE | ROLLUP: 对表VT5进行cube或者rollup操作，产生表VT6.
7.	HAVING： 对虚拟表VT6应用having过滤，只有符合<having-condition>的记录才会被 插入到虚拟表VT7中。
8.	SELECT： 执行select操作，选择指定的列，插入到虚拟表VT8中。
9.	DISTINCT： 对VT8中的记录进行去重。产生虚拟表VT9.
10.	ORDER BY: 将虚拟表VT9中的记录按照<order_by_list>进行排序操作，产生虚拟表VT10.
11.	LIMIT：取出指定行的记录，产生虚拟表VT11, 并将结果返回。

## LIMIT语句起始行号如何计算？有无替代方法？
返回不多于5行（小于等于）
SELECT prod_name
FROM products
LIMIT 5;

返回从第6行开始的5行（行号从0开始）
SELECT prod_name
FROM products
LIMIT 5,5;

返回从第6行开始的5行（LIMIT的一种替代语法）
SELECT prod_name
FROM products
LIMIT 5 OFFSET 5;

## 使用NULL值进行条件判断存在什么问题？
空值检查
SELECT prod_name
FROM products
WHERE prod_price IS NULL;
无法通过过滤条件“选择出不具有特定值的行”来返回具有NULL值的行，因为“未知”具有特殊的含义，数据库不知道它们是否匹配，所以在匹配过滤或不匹配过滤时，不返回它们。即NULL值既非等于，也非不等于。


## 使用%进行模糊查询有什么限制？
搜索以指定字符串开始、以指定字符串结束的记录
SELECT prod_name
FROM products
WHERE prod_name LIKE 's%e';
%表示任何字符出现任意次数（0、1或多个），是否区分大小写取决于MySQL的配置。
尾空格可能会干扰通配符匹配，例如“anvil ”（最后有一个空格），则LIKE “%anvil”将不会匹配，因为在最后有一个多余的字符。
通配符不能匹配NULL值，即使使用‘%’也不行。

注意：把通配符置于搜索模式的开始处，搜索起来是最慢的。


## 冷备份和热备份有什么不同？各自的优缺点是什么？
冷备份
发生在数据库已经正常关闭的情况下，当正常关闭时会提供给我们一个完整的数据库。冷备份是将关键性文件拷贝到另外位置的一种说法。对于备份数据库信息而言，冷备份是最快和最安全的方法。
优点： 
1．是非常快速的备份方法（只需拷贝文件） 
2．容易归档（简单拷贝即可） 
3．容易恢复到某个时间点上（只需将文件再拷贝回去） 
4．能与归档方法相结合，做数据库“最新状态”的恢复。 
5．低度维护，高度安全。 

缺点： 
1．单独使用时，只能提供到“某一时间点上”的恢复。 
2．在实施备份的全过程中，数据库必须要作备份而不能作其它工作。也就是说，在冷备份过程中，数据库必须是关闭状态。 
3．若磁盘空间有限，只能拷贝到磁带等其它外部存储设备上，速度会很慢。 
4．不能按表或按用户恢复。 
值得注意的是冷备份必须在数据库关闭的情况下进行，当数据库处于打开状态时，执行数据库文件系统备份是无效的 。而且在恢复后一定要把数据库文件的属组和属主改为mysql。

热备份
在数据库运行的情况下，备份数据库操作的sql语句，当数据库发生问题时，可以重新执行一遍备份的sql语句。

优点： 
1．可在表空间或数据文件级备份，备份时间短。 
2．备份时数据库仍可使用。 
3．可达到秒级恢复（恢复到某一时间点上）。 
4．可对几乎所有数据库实体作恢复。 
5．恢复是快速的，在大多数情况下在数据库仍工作时恢复。 

缺点： 
1．不能出错，否则后果严重。 
2．若热备份不成功，所得结果不可用于时间点的恢复。 
3．因难于维护，所以要特别仔细小心，不允许“以失败而告终”。

MySQL原生支持多机热备。


## 如何理解数据库设计的三个范式？
通俗地理解三个范式，对于数据库设计大有好处。在数据库设计中，为了更好地应用三个范式，就必须通俗地理解 三个范式(通俗地理解是够用的理解，并不是最科学最准确的理解)： 
第一范式：1NF是对属性的原子性约束，要求属性具有原子性，不可再分解； 
第二范式：2NF是对记录的惟一性约束，要求记录有惟一标识，即实体的惟一性； 
第三范式：3NF是对字段冗余性的约束，即任何字段不能由其他字段派生出来，它要求字段没有冗余。 

没有冗余的数据库设计可以做到。但是，没有冗余的数据库未必是最好的数据库，有时为了提高运行效率，就必须降低范式标准，适当保留冗余数据。具体做法是：在概念数据模型设计时遵守第三范式，降低范式标准的工作放到物理数据模型设计时考虑。降低范式就是增加字段，允许冗余。

范式是符合某一种级别的关系模式的集合。关系数据库中的关系必须满足一定的要求，即满足不同的范式。目前关系数据库有六种范式。一般说来，数据库只需满足第三范式（3NF）就行了。
第一范式（1NF）：指数据库表的每一列都是不可分割的基本数据项，同一列中不能有多个值，即实体中的某个属性不能有多个值或者不能有重复的属性。例如，不能将员工信息都放在一列中显示，也不能将其中的两列或多列在一列中显示；员工信息表的每一行只表示一个员工的信息，一个员工的信息在表中只出现一次。简而言之，第一范式就是无重复的列。
第二范式（2NF）：第二范式（2NF）要求数据库表中的每个实例或行必须可以被惟一地区分。为实现区分通常需要为表加上一个列，以存储各个实例的惟一标识。简而言之，第二范式就是非主属性的部分依赖于主关键字。
第三范式（3NF）：第三范式（3NF）要求一个数据库表中不包含已在其它表中已包含的非主关键字信息。例如，存在一个部门信息表，其中每个部门有部门编号（dept_id）、部门名称、部门简介等信息。那么在员工信息表中列出部门编号后就不能再将部门名称、部门简介等与部门有关的信息再加入员工信息表中。如果不存在部门信息表，则根据第三范式（3NF）也应该构建它，否则就会有大量的数据冗余。简而言之，第三范式就是属性不依赖于其它非主属性。


## 有哪些MySQL性能优化的技巧？
（1）优化MySQL查询语句，使其使用查询缓存
对于相同的查询，MySQL引擎会使用缓存，但是如果在SQL语句中使用函数，如NOW()、RAND()、 CURDATE()等等，则拼凑出的查询不会被认为是相同的查询。
// 查询缓存不开启
$r = mysql_query("SELECT username FROM user WHERE signup_date >= CURDATE()");

// 开启查询缓存
$today = date("Y-m-d");
$r = mysql_query("SELECT username FROM user WHERE signup_date >= '$today'");

（2）当只要一行数据时使用LIMIT 1
	这样MySQL数据库引擎会在找到一条数据后停止搜索，而不是继续往后查少下一条符合记录的数据。

（3）为搜索字段建索引
索引并不一定就是给主键或是唯一的字段。如果在表中有某个字段总要会经常用来做搜索，那么请为其建立索引。

（4）在Join表的时候使用相当类型的列，并将其索引
对于那些STRING类型，还需要有相同的字符集才行（两个表的字符集有可能不一样）

（5）千万不要ORDER BY RAND()
You cannot use a column with RAND() values in an ORDER BY clause, because ORDER BY 
would evaluate the column multiple times. 
当记录数据过多时，会非常慢。

（6）避免SELECT *
应该养成一个需要什么就取什么的好的习惯。

（7）使用ENUM而不是VARCHAR
ENUM 实际保存的是TINYINT，但其外表上显示为字符串。如果有一个字段的取值是有限而且固定的，那么，应该使用ENUM而不是VARCHAR。

（8）尽可能的使用NOT NULL
“NULL columns require additional space in the row to record whether their values are NULL. For MyISAM tables, each NULL column takes one bit extra, rounded up to the nearest byte.”
NULL值需要额外的存储空间，而且在比较时也需要额外的逻辑。

（9）把IP地址存成 UNSIGNED INT，而不是VARCHAR(15)

（10）固定长度的表会更快
如果表中的所有字段都是“固定长度”的，整个表会被认为是 “static” 或 “fixed-length”。

（11）垂直分割
“垂直分割”是一种把数据库中的表按列变成几张表的方法，这样可以降低表的复杂度和字段的数目，从而达到优化的目的。
示例一：在Users表中有一个字段是家庭地址，这个字段是可选字段，相比起，而且你在数据库操作的时候除了个人信息外，你并不需要经常读取或是改写这个字段。那么，为什么不把他放到另外一张表中呢？ 这样会让你的表有更好的性能，只有用户ID，用户名，口令，用户角色等会被经常使用。小一点的表总是会有好的性能。
示例二： 你有一个叫 “last_login” 的字段，它会在每次用户登录时被更新。但是，每次更新时会导致该表的查询缓存被清空。所以，你可以把这个字段放到另一个表中，这样就不会影响你对用户ID，用户名，用户角色的不停地读取了，因为查询缓存会帮你增加很多性能。
另外，你需要注意的是，这些被分出去的字段所形成的表，你不会经常性地去Join他们，不然的话，这样的性能会比不分割时还要差，而且，会是极数级的下降。
总结：降低表的规模、方便使用缓存、被分出去的字段应该是不会经常要join的字段。

（12）拆分大的 DELETE 或 INSERT 语句
因为这两个操作是会锁表的，表一锁住了，别的操作都进不来了。
如果有一个大的处理，一定把其拆分，使用 LIMIT 条件是一个好的方法。下面是一个示例：
while (1) {
//每次只做1000条
mysql_query("DELETE FROM logs WHERE log_date <= '2009-11-01' LIMIT 1000");
if (mysql_affected_rows() == 0) {
    // 没得可删了，退出！
    break;
}
// 每次都要休息一会儿
usleep(50000);
}

（13）越小的列会越快
如使用TINYINT而不是INT，使用DATE而不是DATETIME。

（14）选择一个正确的存储引擎
MyISAM 适合于一些需要大量查询的应用，但其对于有大量写操作并不是很好。甚至你只是需要update一个字段，整个表都会被锁起来，而别的进程，就算是读进程都无法操作直到读操作完成。另外，MyISAM 对于 SELECT COUNT(*) 这类的计算是超快无比的。
InnoDB 的趋势会是一个非常复杂的存储引擎，对于一些小的应用，它会比 MyISAM 还慢。他是它支持“行锁” ，于是在写操作比较多的时候，会更优秀。并且，他还支持更多的高级应用，比如：事务。

（15）小心“永久链接”
“永久链接”的目的是用来减少重新创建MySQL链接的次数。当一个链接被创建了，它会永远处在连接的状态，就算是数据库操作已经结束了。而且，自从Apache开始重用它的子进程后——也就是说，下一次的HTTP请求会重用Apache的子进程，并重用相同的 MySQL 链接。
但是从个人经验上来说，这个功能制造出来的麻烦事更多。因为，你只有有限的链接数，内存问题，文件句柄数，等等。

（1）对接触的项目进行慢查询分析，发现TOP10的基本都是忘了加索引或者索引使用不当，如索引字段上加函数导致索引失效等(如where UNIX_TIMESTAMP(gre_updatetime)>123456789)

（2）另外很多同学在拉取全表数据时，喜欢用select xx from xx limit 5000,1000这种形式批量拉取，其实这个SQL每次都是全表扫描，建议添加1个自增id做索引，将SQL改为select xx from xx where id>5000 and id<6000;

只select出需要的字段，避免select *

尽量早做过滤，使Join或者Union等后续操作的数据量尽量小

（5）把能在逻辑层算的提到逻辑层来处理，如一些数据排序、时间函数计算等


## MySQL的索引有哪些类型？
索引是一种特殊的文件(InnoDB数据表上的索引是表空间的一个组成部分)，它们包含着对数据表里所有记录的引用指针。有了相应的索引之后，数据库会直接在索引中查找符合条件的选项。有以下索引类型：
（1）普通索引
这是最基本的索引，它没有任何限制，MyIASM中默认的BTREE类型的索引，也是大多数情况下用到的索引。
// 直接创建索引
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

（2）唯一索引
索引列的值必须唯一，但允许有空值（注意和主键不同）。如果是组合索引，则列值的组合必须唯一，创建方法和普通索引类似。
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

（3）全文索引
仅可用于MyISAM表。可以从CHAR、VARCHAR或TEXT列中作为CREATE TABLE语句的一部分被创建，或是随后使用ALTER TABLE或CREATE INDEX被添加。对于较大的数据集，将资料输入一个没有FULLTEXT索引的表中，然后创建索引，其速度比把资料输入现有FULLTEXT索引的速度更为快。不过切记对于大容量的数据表，生成全文索引是一个非常消耗时间非常消耗硬盘空间的做法。
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

（4）单列索引、多列索引
多个单列索引与单个多列索引的查询效果不同，因为执行查询时，MySQL只能使用一个索引，会从多个索引中选择一个限制最为严格的索引。

（5）组合索引
例如上表中针对title和time建立一个组合索引：
ALTER TABLE article ADD INDEX index_titme_time (title(50),time(10));
建立这样的组合索引，其实是相当于分别建立了下面两组组合索引：
–title,time
–title
为什么没有time这样的组合索引呢？这是因为MySQL组合索引“最左前缀”的结果。简单的理解就是只从最左面的开始组合。如下面的几个SQL所示：
// 使用到上面的索引
SELECT * FROM article WHREE title='测试' AND time=1234567890;		# 使用title,time索引
SELECT * FROM article WHREE utitle='测试';							# 使用title索引

// 不使用上面的索引
SELECT * FROM article WHREE time=1234567890;						

MySQL只对以下操作符才使用索引：<,<=,=,>,>=,between,in,以及某些时候的like(不以通配符%或_开头的情形)。而理论上每张表里面最多可创建16个索引。

## 不使用COUNT(*)加LIMIT，如何获取分页数据及总记录数？
在很多分页的程序中都这样写:
SELECT COUNT(*) from ‘table’ WHERE ......;  	# 查出符合条件的记录总数
SELECT * FROM ‘table’ WHERE ...... limit M,N; 	# 查询当页要显示的数据
这样的语句可以改成:
SELECT SQL_CALC_FOUND_ROWS * FROM ‘table’ WHERE ......  limit M, N;
SELECT FOUND_ROWS();
这样只要执行一次较耗时的复杂查询可以同时得到与不带limit同样的记录条数。
第二个SELECT返回一个数字，指示了在没有LIMIT子句的情况下，第一个SELECT返回了多少行。


## 什么是Covering Index？如何使用？如何判断索引是否生效？
对于SELECT a FROM … WHERE b = …这种查询，通常的做法是在b字段上建立索引，执行查询时系统会查询b索引进行定位，然后再利用此定位去表里查询需要的数据a。即该过程存在两次查询，一次是查询索引，一次是查询表。
使用Covering Index可以只查询一次索引就完成。建立一个组合索引’b,a’，当查询时，通过组合索引的b部分去定位，至于需要的数据a，立刻就可以在索引里得到，从而省略了表查询的过程。
如果使用Covering Index，要注意SELECT的方式，只SELECT必要的字段，而不能SELECT *，因为不太可能把所有的字段一起做索引。
可以使用EXPLAIN命令来确认是否使用了组合索引：如果在Extra里出现Using Index，就说明使用的是Covering Index。
实例1：
SELECT COUNT(*) FROM articles WHERE category_id = …
当在category_id建立索引后，这个查询使用的就是Covering Index。

实例2：
比如说在文章系统里分页显示的时候，一般的查询是这样的：
SELECT id, title, content FROM article ORDER BY created DESC LIMIT 10000, 10;
通常这样的查询会把索引建在created字段（其中id是主键），不过当LIMIT偏移很大时，查询效率仍然很低，改变一下查询：
SELECT id, title, content FROM article
INNER JOIN (
SELECT id FROM article ORDER BY created DESC LIMIT 10000, 10
) AS page USING(id)
此时，建立复合索引”created, id”就可以在子查询里利用上Covering Index，快速定位id。

## innodb_buffer_pool_size设置什么？
innodb_buffer_pool_size这个参数主要作用是缓存innodb表的索引、数据、插入数据时的缓冲。
默认值：128M
专用mysql服务器设置的大小： 操作系统内存的70%-80%最佳。
设置方法：
my.cnf文件
innodb_buffer_pool_size = 6G
此外，这个参数是非动态的，要修改这个值，需要重启mysqld服务。

如果因为内存不够，MySQL无法启动，就会在错误日志中出现如下报错：
InnoDB: mmap(137363456 bytes) failed; errno 12


## MySQL性能相关的配置参数有哪些？
max_connecttions：最大连接数
table_cache：缓存打开表的数量
key_buffer_size：索引缓存大小
query_cache_size：查询缓存大小
sort_buffer_size：排序缓存大小(会将排序完的数据缓存起来)
read_buffer_size：顺序读缓存大小
read_rnd_buffer_size：某种特定顺序读缓存大小(如order by子句的查询)

查看配置方法：show variables like '%max_connecttions%';

## MySQL如何进行慢查询日志分析？
慢查询相关的配置参数：
log_slow_queries					# 是否打开慢查询日志，得先确保=ON后面才有得分析
long_query_time					# 查询时间大于多少秒的SQL被当做是慢查询，一般设为1S
log_queries_not_using_indexes		# 是否将没有使用索引的记录写入慢查询日志
slow_query_log_file				# 慢查询日志存放路径


## 慢查询记录格式：
可以查看每个慢查询SQL的耗时
 User@Host: edu_online[edu_online] @  [10.139.10.167]
 Query_time: 1.958000  Lock_time: 0.000021 Rows_sent: 254786  Rows_examined: 254786
SET timestamp=1410883292;
select * from t_online_group_records;
日志显示该查询用了1.958秒，返回254786行记录，一共遍历了254786行记录。及具体的时间戳和SQL语句。

使用mysqldumpslow进行慢查询日志分析：
输入：
mysqldumpslow -s t -t 5 slow_log_20140819.txt 
-s：排序方法，t表示按时间（此外，c为按次数，r为按返回记录数等）
-t：取Top多少条，-t 5表示取前5条

输出：
Count: 1076100  Time=0.09s (99065s)  Lock=0.00s (76s)  Rows=408.9 (440058825), edu_online[edu_online]@28hosts
  select * from t_online_group_records where UNIX_TIMESTAMP(gre_updatetime) > N
Count: 1076099  Time=0.05s (52340s)  Lock=0.00s (91s)  Rows=62.6 (67324907), edu_online[edu_online]@28hosts
  select * from t_online_course where UNIX_TIMESTAMP(c_updatetime) > N
Count: 63889  Time=0.78s (49607s)  Lock=0.00s (3s)  Rows=0.0 (18), edu_online[edu_online]@[10.213.170.137]
  select f_uin from t_online_student_contact where f_modify_time > N
...
以第1条为例，表示这类SQL（N可以取很多值，这里mysqldumpslow会归并起来）在8月19号的慢查询日志内出现了1076100次，总耗时99065秒，总返回440058825行记录，有28个客户端IP用到。
通过慢查询日志分析，就可以找到最耗时的SQL，然后进行具体的SQL分析了


## 如何分析MySQL语句的执行情况？
mysql> explain select * from t_online_group_records where UNIX_TIMESTAMP(gre_updatetime) > 123456789;
+----+-------------+------------------------+------+---------------+------+---------+------+------+-------------+
| id | select_type | table                  | type | possible_keys | key  | key_len | ref  | rows | Extra       |
+----+-------------+------------------------+------+---------------+------+---------+------+------+-------------+
|  1 | SIMPLE      | t_online_group_records | ALL  | NULL          | NULL | NULL    | NULL |   47 | Using where |
+----+-------------+------------------------+------+---------------+------+---------+------+------+-------------+
1 row in set (0.00 sec)
如上面例子所示，重点关注下type，rows和Extra：
type：连接操作的类型，可以用来判断有无使用到索引。结果值从好到坏：... > range(使用到索引) > index > ALL(全表扫描)，一般查询应达到range级别，具体可能值如下：
SYSTEM	# CONST的特例，当表上只有一条记录匹配
CONST	# WHERE条件筛选后表上至多有一条记录匹配时，比如WHERE ID = 2 （ID是主键，值为2的要么有一条要么没有）
EQ_REF	# 参与连接运算的表是内表（在代码实现的算法中，两表连接时作为循环中的内循环遍历的对象，这样的表称为内表）。
基于索引（连接字段上存在唯一索引或者主键索引，且操作符必须是“=”谓词，索引值不能为NULL）做扫描，使得对外表的一条元组，内表只有唯一一条元组与之对应。
REF		# 可以用于单表扫描或者连接。参与连接运算的表，是内表。
基于索引（连接字段上的索引是非唯一索引，操作符必须是“=”谓词，连接字段值不可为NULL）做扫描，使得对外表的一条元组，内表可有若干条元组与之对应。
REF_OR_NULL		# 类似REF，只是搜索条件包括：连接字段的值可以为NULL的情况，比如 where col = 2 or col is null
RANGE			# 范围扫描，基于索引做范围扫描，为诸如BETWEEN，IN，>=，LIKE类操作提供支持
INDEX_SCAN		# 索引做扫描，是基于索引在索引的叶子节点上找满足条件的数据（不需要访问数据文件）
ALL				# 全表扫描或者范围扫描：不使用索引，顺序扫描，直接读取表上的数据（访问数据文件）
UNIQUE_SUBQUERY	# 在子查询中，基于唯一索引进行扫描，类似于EQ_REF
INDEX_SUBQUERY		# 在子查询中，基于除唯一索引之外的索引进行扫描
INDEX_MERGE		# 多重范围扫描。两表连接的每个表的连接字段上均有索引存在且索引有序，结果合并在一起。适用于作集合的并、交操作。
FT					# FULL TEXT，全文检索
rows：SQL执行检查的记录数
Extra：SQL执行的附加信息，如"Using index"表示查询只用到索引列，不需要去读表等

## 如何分析MySQL语句执行时间和消耗资源？
SET profiling=1; 						# 启动profiles，默认是没开启的
SELECT * FROM customers;			# 执行要分析的SQL语句

SHOW profiles;						# 查看SQL语句具体执行步骤及耗时

SHOW profile cpu,block io FOR QUERY 41;		#	查看ID为41的查询在各个环节的耗时和资源消耗


## MySQL如何进行主从数据同步？
复制机制（Replication）
master通过复制机制，将master的写操作通过binlog传到slave生成中继日志(relaylog)，slave再将中继日志redo，使得主库和从库的数据保持同步

复制相关的3个Mysql线程（属于slave主动请求拉取的模式）
（1）slave上的I/O线程：向master请求数据
（2）master上的Binlog Dump线程：读取binlog事件并把数据发送给slave的I/O线程
（3）slave上的SQL线程：读取中继日志并执行，更新数据库

相关监控命令
show processlist		# 查看MySQL进程信息，包括3个同步线程的当前状态
show master status		# 查看master配置及当前复制信息
show slave status		# 查看slave配置及当前复制信息

## 如何进行MySQL数据备份与恢复操作？
（1）备份：使用mysqldump导出数据
mysqldump -u 用户名 -p 数据库名 [表名] > 导出的文件名
mysqldump -uroot -p test mytable > mytable.20140921.bak.sql

（2）恢复：导入备份数据
mysql -uroot -p test < mytable.20140921.bak.sql

（3）恢复：导入备份数据之后发送的写操作。先使用mysqlbinlog导出这部分写操作SQL(基于时间点或位置)
// 导出2014-09-21 09:59:59之后的binlog：
mysqlbinlog --database="test" --start-date="2014-09-21 09:59:59" /var/lib/mysql/mybinlog.000001 > binlog.data.sql

// 导出起始id为123456之后的binlog：
mysqlbinlog --database="test" --start-position="123456" /var/lib/mysql/mybinlog.000001 > binlog.data.sql

// 把要恢复的binlog导入db
mysql -uroot -p test < binlog.data.sql


## MySQL一行记录的最大长度是多少？
MySQL表中一行的长度不能超过65535字节，VARCHAR(N)使用额外的1到2字节来存储值的长度，如果N<=255，则使用一个字节，否则使用两个字节；如果表格的编码为UTF8（一个字符占3个字节），那么VARCHAR(255)占用的字节数为255 * 3 + 2 = 767，这样，一行就最多只能有65535 / 765 = 85个VARCHAR(255)类型的列。

MySQL中有哪几种事务隔离级别？
1）READ UNCOMMITTED		# 可读取其他事务未提交的数据（脏读）
2）READ COMMITTED		# 只能读取已提交的数据，但是不可重复读（避免脏读）
3）REPEATABLE READ		# 可重复读
// 用户A查询完之后，用户B将无法更新用户A所查询到的数据集中的任何数据（但是可以更新、插入和删除用户A查询到的数据集之外的数据），直到用户A事务结束才可以进行更新，这样就有效的防止了用户在同一个事务中读取到不一致的数据。
	4）SERIALIZABLE			# 事务串行化，必须等待当前事务执行完，其他事务才可以执行写操作，有多个事务同时设置SERIALIZABLE时会产生死锁：
ERROR 1213 (40001): Deadlock found when trying to get lock; try restarting transaction
这是四个隔离级别中限制最大的级别。因为并发级别较低，所以应只在必要时才使用该选项。

使用事务时设置级别：
	START TRANSACTION
SET [SESSION | GLOBAL] TRANSACTION ISOLATION LEVEL {READ UNCOMMITTED | READ COMMITTED | REPEATABLE READ | SERIALIZABLE}
COMMIT
ROLLBACK

## 什么是MySQL的行级锁？
如果是InnoDB引擎，就可以在事务里使用行锁，格式为：
SELECT xx FROM xx [FORCE INDEX(PRIMARY)] WHERE xx FOR UPDATE 
被加锁的行，其他事务也能读取但如果想写的话就必须等待锁的释放。
只有查询用到的是主键索引或满足最左前缀的主键索引的一部分，并且具有明确的值，如：
索引列=值、索引列=值1 or 索引列=值2、索引列 IN(值1,值2)
才能实现行锁定，否则就会锁表。
注：非主键索引会锁表，如果有多个索引可指定用主键索引（FORCE INDEX(PRIMARY)），以免锁表。


## ON DUPLICATE KEY UPDATE语句的作用是什么？
INSERT INTO ... ON DUPLICATE KEY UPDATE col=VALUES(col)		# VALUES用来取插入的值，存在主键冲突时就更新，没有删除操作

例：更新统计表
老做法是写三条sql语句:
select * from player_count where player_id = 1;					# 查询统计表中是否有记录
insert into player_count(player_id,count) value(1,1);				# 没有记录就执行insert 操作
update player_count set count = count+1 where player_id = 1;		# 有记录就执行update操作
这种写法比较麻烦，用on duplicate key update 的做法如下：
insert into player_count(player_id,count) value(1,1) on duplicate key update count=count+1;


## ON与WHERE有什么区别？
执行连接操作时，可先用ON先进行过滤，减少连接操作的中间结果，然后用WHERE对连接产生的结果再一次过滤。但是，如果是左/右连接，在ON条件里对主表的过滤是无效的，仍然会用到主表的所有记录，连接产生的记录如果不满足主表的过滤条件那么从表部分的数据会置为NULL。

## MySQL如何创建分区表？
分区是一种粗粒度，简易的索引策略，适用于大数据的过滤场景。对于大数据(如10TB)而言，索引起到的作用相对小，因为索引的空间与维护成本很高，另外如果不是索引覆盖查询，将导致回表，造成大量磁盘IO。
分区表分为RANGE、LIST、HASH、KEY四种类型,并且分区表的索引是可以局部针对分区表建立的。
用RANGE创建分区表：
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
如果这么做，则order_day必须包含在主键中，且会产生一个问题：当年份超过阈值，到了2013，2014时需要手动创建这些分区，更好的方法是使用HASH：
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DOUBLE NOT NULL,
    order_day DATETIME NOT NULL
) ENGINE=Innodb PARTITION BY HASH(id DIV 1000000);
这种分区表示每100W条数据建立一个分区，且没有阈值范围的影响。

如果想为一个表创建分区，这个表最多只能有一个唯一索引（主键也是唯一索引）。如果没有唯一索引，可指定任何一列为分区列；否则就只能指定唯一索引中的任何一列为分区列。查询时需用到分区的列，不然会遍历所有的分区，比不分区的查询效率还低，MySQL支持子分区。

在表建立后也可以新增、删除、合并分区。


## 如何在查询时重新定义数值类型？
使用CASE来重新定义数值类型
SELECT id,title,(CASE date WHEN '0000-00-00' THEN '' ELSE date END) AS date
FROM your_table
  
SELECT id,title,
(CASE status WHEN 0 THEN 'open' WHEN 1 THEN 'close' ELSE 'standby' END) AS status
FROM your_table

## 如何为SELECT语句添加一个自动增加的列？
set @N = 0;
SELECT @N := @N +1 AS number, name, surname FROM gbtags_users;


## 什么是B树？为什么可以使用B数来做数据库的索引？
B树是对二叉查找树的改进。它的设计思想是，将相关数据尽量集中在一起，以便一次读取多个数据，减少硬盘操作次数。

特点如下：
（1）一个节点可以容纳多个值。比如上图中，最多的一个节点容纳了4个值。
（2）除非数据已经填满，否则不会增加新的层。也就是说，B树追求"层"越少越好。
（3）子节点中的值，与父节点中的值，有严格的大小对应关系。一般来说，如果父节点有a个值，那么就有a+1个子节点。比如上图中，父节点有两个值（7和16），就对应三个子节点，第一个子节点都是小于7的值，最后一个子节点都是大于16的值，中间的子节点就是7和16之间的值。
这种数据结构，非常有利于减少读取硬盘的次数。假定一个节点可以容纳100个值，那么3层的B树可以容纳100万个数据，如果换成二叉查找树，则需要20层！假定操作系统一次读取一个节点，并且根节点保留在内存中，那么B树在100万个数据中查找目标值，只需要读取两次硬盘。
数据库以B树格式储存，只解决了按照"主键"查找数据的问题。如果想查找其他字段，就需要建立索引（index）。所谓索引，就是以某个字段为关键字的B树文件（索引的一种）。

