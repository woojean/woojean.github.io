# Redis有哪些数据类型？每种数据类型的存储极限是什么？

五种数据类型：string（字符串）、hash（哈希）、list（列表）、set（集合）及zset(sorted set：有序集合)。
String
string类型是二进制安全的，所以redis的string可以包含任何数据。比如jpg图片或者序列化的对象。`一个string最大能存储512MB`。
redis 127.0.0.1:6379> SET name "w3cschool.cc"
OK
redis 127.0.0.1:6379> GET name
"w3cschool.cc"

Hash
Redis hash是一个string类型的field和value的映射表，特别适合用于存储对象。每个 hash 可以存储2^32-1键值对（40多亿）。
redis 127.0.0.1:6379> HMSET user:1 username w3cschool.cc password w3cschool.cc points 200
OK
redis 127.0.0.1:6379> HGETALL user:1		# user:1 为键值
1) "username"
2) "w3cschool.cc"
3) "password"
4) "w3cschool.cc"
5) "points"
6) "200"

List
Redis列表是简单的字符串列表，按照插入顺序排序。可以添加一个元素到列表的头部（左边）或者尾部（右边）。
列表最多可存储2^32-1元素 (4294967295, 每个列表可存储40多亿)。
redis 127.0.0.1:6379> lpush w3cschool.cc redis
(integer) 1
redis 127.0.0.1:6379> lpush w3cschool.cc mongodb
(integer) 2
redis 127.0.0.1:6379> lpush w3cschool.cc rabitmq
(integer) 3
redis 127.0.0.1:6379> lrange w3cschool.cc 0 10
1) "rabitmq"
2) "mongodb"
3) "redis"

Set
Redis的Set是string类型的无序集合。集合是通过哈希表实现的，所以添加，删除，查找的复杂度都是O(1)。集合中最大的成员数为2^32-1(4294967295, 每个集合可存储40多亿个成员)。
redis 127.0.0.1:6379> sadd w3cschool.cc redis
(integer) 1			# 成功，返回1
redis 127.0.0.1:6379> sadd w3cschool.cc mongodb
(integer) 1
redis 127.0.0.1:6379> sadd w3cschool.cc rabitmq
(integer) 1
redis 127.0.0.1:6379> sadd w3cschool.cc rabitmq
(integer) 0			# 元素已经在集合中，返回0，第二次插入的元素将被忽略
redis 127.0.0.1:6379> smembers w3cschool.cc
1) "rabitmq"
2) "mongodb"
3) "redis"

zset
Redis zset和set一样也是string类型元素的集合,且不允许重复的成员。不同的是每个元素都会关联一个double类型的分数（score）。redis正是通过分数来为集合中的成员进行从小到大的排序。zset的成员是唯一的,但分数却可以重复。
redis 127.0.0.1:6379> zadd w3cschool.cc 0 redis
(integer) 1
redis 127.0.0.1:6379> zadd w3cschool.cc 0 mongodb
(integer) 1
redis 127.0.0.1:6379> zadd w3cschool.cc 0 rabitmq
(integer) 1
redis 127.0.0.1:6379> zadd w3cschool.cc 0 rabitmq
(integer) 0			# 元素在集合中存在则更新对应score
redis 127.0.0.1:6379> ZRANGEBYSCORE w3cschool.cc 0 1000
1) "redis"
2) "mongodb"
3) "rabitmq"