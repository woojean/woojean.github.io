# Redis应用场景

1.取最新N个数据的操作
先lpush，然后用lrange返回。
可以用ltrim裁剪（含首尾），以节省空间。

```
FUNCTION get_latest_comments(start,num_items):
    id_list = redis.lrange("latest.comments",start,start+num_items-1)
    IF id_list.length < num_items
        id_list = SQL_DB("SELECT ... ORDER BY time LIMIT ...")
    END
    RETURN id_list
END
```

2.取TOP N操作
前面操作以时间为权重，这个是以某个条件为权重，使用sorted set：
```
zadd ss 1 a 2 b 3 c 4 d 0 x
zincrby ss 10 x  			  # 将x的分数增加10
zrevrange ss 0 2 withscores   # 按分数高低列出前2名
```


3.需要精准设定过期时间的应用
expire

4.计数器应用
INCR、DECR等原子性操作

5.Uniq操作，获取某段时间所有数据排重值
set

6.实时系统，反垃圾系统
通过set知道一个终端用户是否进行了某个操作，可以找到其操作的集合并进行分析统计对比等


7.Pub/Sub构建实时消息系统
publish、pubsub

8.构建队列系统
list、sorted set（优先级队列）

9.缓存