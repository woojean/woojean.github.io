# LIMIT语句起始行号如何计算？有无替代方法？

返回不多于5行（小于等于）

```
SELECT prod_name
FROM products
LIMIT 5;
```

返回从第6行开始的5行（行号从0开始）
```
SELECT prod_name
FROM products
LIMIT 5,5;
```

返回从第6行开始的5行（LIMIT的一种替代语法）
```
SELECT prod_name
FROM products
LIMIT 5 OFFSET 5;
```