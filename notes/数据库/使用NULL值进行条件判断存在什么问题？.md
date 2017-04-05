# 使用NULL值进行条件判断存在什么问题？

空值检查

```
SELECT prod_name
FROM products
WHERE prod_price IS NULL;
```
无法通过过滤条件“选择出不具有特定值的行”来返回具有NULL值的行，因为“未知”具有特殊的含义，数据库不知道它们是否匹配，所以在匹配过滤或不匹配过滤时，不返回它们。即NULL值既非等于，也非不等于。