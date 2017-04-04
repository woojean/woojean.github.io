# 如何兼容MySQL的时间格式？

MySql中的日期和时间是按照ISO标准处理的，即形如2008-03-29或者08-02-29这两种格式。PHP和MySql的通信常常会需要进行日期和时间的转换，这可以在其中任意一端进行：
PHP：可以使用date()函数进行转换。需要注意的是，月份和具体的日期应该使用带有前导0的格式。
MySql：可以使用`DATE_FORMATE()`或者`UNIX_TIMESTAMP()`;
SELECT DATE_FORMATE(date_column,’%m %d %y’) FROM tablename;
SELECT UNIX_TIMESTAMP(date_column) FROM tablename;

