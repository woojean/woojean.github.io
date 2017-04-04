# 有哪些MySQL性能优化的技巧？

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
示例一：在Users表中有一个字段是家庭地址，这个字段是可选字段，相比起，而且你在数据库操作的时候除了个人信息外，你`并不需要经常读取或是改写这个字段`。那么，为什么不把他放到另外一张表中呢？ 这样会让你的表有更好的性能，只有用户ID，用户名，口令，用户角色等会被经常使用。小一点的表总是会有好的性能。
示例二： 你有一个叫 “last_login” 的字段，它会在每次用户登录时被更新。但是，每次更新时会导致该表的查询缓存被清空。所以，你可以把这个字段放到另一个表中，这样就不会影响你对用户ID，用户名，用户角色的不停地读取了，因为查询缓存会帮你增加很多性能。
另外，你需要注意的是，这些被分出去的字段所形成的表，你不会经常性地去Join他们，不然的话，这样的性能会比不分割时还要差，而且，会是极数级的下降。
总结：降低表的规模、方便使用缓存、被分出去的字段应该是不会经常要join的字段。

（12）拆分大的 DELETE 或 INSERT 语句
因为这两个操作是会锁表的，表一锁住了，别的操作都进不来了。
如果有一个大的处理，一定把其拆分，使用 LIMIT 条件是一个好的方法。下面是一个示例：
```
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
```

（13）越小的列会越快
如使用TINYINT而不是INT，使用DATE而不是DATETIME。

（14）选择一个正确的存储引擎
MyISAM 适合于一些需要大量查询的应用，但其对于有大量写操作并不是很好。甚至你只是需要update一个字段，整个表都会被锁起来，而别的进程，就算是读进程都无法操作直到读操作完成。另外，MyISAM 对于 SELECT COUNT(*) 这类的计算是超快无比的。
InnoDB 的趋势会是一个非常复杂的存储引擎，对于一些小的应用，它会比 MyISAM 还慢。他是它支持“行锁” ，于是在写操作比较多的时候，会更优秀。并且，他还支持更多的高级应用，比如：事务。

（15）小心“永久链接”
“永久链接”的目的是用来减少重新创建MySQL链接的次数。当一个链接被创建了，它会永远处在连接的状态，就算是数据库操作已经结束了。而且，自从Apache开始重用它的子进程后——也就是说，下一次的HTTP请求会重用Apache的子进程，并重用相同的 MySQL 链接。
但是从个人经验上来说，这个功能制造出来的麻烦事更多。因为，你只有有限的链接数，内存问题，文件句柄数，等等。

（1）对接触的项目进行慢查询分析，发现TOP10的基本都是忘了加索引或者索引使用不当，如索引字段上加函数导致索引失效等(如where UNIX_TIMESTAMP(gre_updatetime)>123456789)

（2）另外很多同学在拉取全表数据时，喜欢用select xx from xx limit 5000,1000这种形式批量拉取，其实这个SQL每次都是全表扫描，建议添加1个自增id做索引，将SQL改为`select xx from xx where id>5000 and id<6000`;

只select出需要的字段，避免select *

尽量早做过滤，使Join或者Union等后续操作的数据量尽量小

（5）把能在逻辑层算的提到逻辑层来处理，如一些数据排序、时间函数计算等