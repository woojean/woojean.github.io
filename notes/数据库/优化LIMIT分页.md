# 优化LIMIT分页

对于偏移量非常大的查询应该尽可能地使用索引覆盖扫描进行优化：

```
SELECT film_id,descriptionn FROM sakila.film ORDER BY title LIMIT 50000,5;
```
上面的语句将查询50000条记录，然后只返回最后5条。最好改写成下面形式：
```
SELECT film.film_id, film.descriptionn 
FROM sakila.film
  INNER JOIN(
    SELECT film_id FROM sakila.film
    ORDER BY title LIMIT 50000,5
) AS lim USING(film_id);
```

也可以在应用程序中记录上次查询位置，或者计算值的上下边界（值需要连续）来优化。