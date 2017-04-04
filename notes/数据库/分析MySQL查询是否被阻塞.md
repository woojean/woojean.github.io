# 分析MySQL查询是否被阻塞

show processlist;

查看当前所有查询的状态，当查询被锁阻塞时，State字段为Locked。