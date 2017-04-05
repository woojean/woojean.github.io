# MySQL性能相关的配置参数有哪些？

`max_connecttions`：最大连接数
`table_cache`：缓存打开表的数量
`key_buffer_size`：索引缓存大小
`query_cache_size`：查询缓存大小
`sort_buffer_size`：排序缓存大小(会将排序完的数据缓存起来)
`read_buffer_size`：顺序读缓存大小
`read_rnd_buffer_size`：某种特定顺序读缓存大小(如order by子句的查询)
查看配置方法：show variables like '%max_connecttions%';