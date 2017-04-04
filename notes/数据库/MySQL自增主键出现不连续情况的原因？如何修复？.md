# MySQL自增主键出现不连续情况的原因？如何修复？

A、使用insert into插入数据时，若abc的值已存在，因其为唯一键，故不会插入成功。但此时，那个AUTO_INCREMENT已然+1了。
eg : insert into `table` set `abc` = '123'


B、使用replace插入数据时，若abc的值已存在，则会先删除表中的那条记录，尔后插入新数据。
eg : replace into `table` set `abc` = '123'
（注：上一行中的into可省略；这只是一种写法。）

  1、insert语句不管是否成功，都会增加AUTO_INCREMENT值。

    2、进行了delete相关操作。



    3、rollback相关。
修复：
```
INSERT INTO th_page2(site,url,title,title_index,content,tag,created_at,updated_at,deleted_at)
SELECT site,url,title,title_index,content,tag,created_at,updated_at,deleted_at FROM th_page ORDER BY tag;

DROP TABLE th_page;

ALTER TABLE th_page2 RENAME th_page;
```