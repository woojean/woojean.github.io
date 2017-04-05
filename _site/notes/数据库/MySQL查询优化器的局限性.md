# MySQL查询优化器的局限性

使用WHERE ... IN(SELECT ...)查询，性能会非常糟

```
SELECT * FROM sakila.film
  WHERE film_id IN ( SELECT film_id FROM sakila.film_actor WHERE actor_id = 1);
```
上面的查询`并不会`优化成如下的方式（以便利用IN()的高效列表查询）：
```
SELECT GROUP_CONCAT(film_id) FROM sakila.film_actor WHERE actor_id = 1;
-- Result:1,2,3,4,5...
SELECT * FROM sakila.film
  WHERE film_id IN ( 1,2,3,4,5... );
```

实际上MySQL会将外层表压到子查询中：
```
SELECT * FROM sakila.film
  WHERE EXISTS(SELECT * FROM sakila.film_actor 
                 WHERE actor_id = 1
                 AND film_actor.film_id = film.film_id);
```
MySQL会先对film表进行全表扫描（因为此时在子查询中用到了外层表的film_id字段），然后根据返回的film_id逐个执行子查询。如果外层的表是一个非常大的表，这个查询的性能将会非常糟糕。

可以如下改写：
```
SELECT film.* FROM sakila.film
  INNER JOIN sakila.film_actor USING(film_id)
WHERE actor_id = 1;
```

要不要使用子查询，没有绝对的答案，应该以测试为准，详略。

UNION查询的优化限制，略

MySQL无法利用多核特性来并行执行查询。

MySQL不支持`松散索引扫描`，即无法按照不连续的方式扫描一个索引，即使需要的数据是索引某个片段中的少数几个，MySQL仍然要扫描这个片段中的每一个条目。
5.6以后的版本，关于松散索引扫描的一些限制会通过`索引条件下推`的方式解决。