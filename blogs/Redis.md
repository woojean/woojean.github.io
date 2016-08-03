 ## 什么是Redis的管道技术？
Redis是一种基于客户端-服务端模型以及请求/响应协议的TCP服务。这意味着通常情况下一个请求会遵循以下步骤：
（1）客户端向服务端发送一个查询请求，并监听Socket返回，通常是以阻塞模式，等待服务端响应。
（2）服务端处理命令，并将结果返回给客户端。
Redis管道技术可以在服务端未响应时，客户端可以继续向服务端发送请求，并最终一次性读取所有服务端的响应。
如：
$(echo -en "PING\r\n SET w3ckey redis\r\nGET w3ckey\r\nINCR visitor\r\nINCR visitor\r\nINCR visitor\r\n"; sleep 10) | nc localhost 6379

+PONG
+OK
redis
:1
:2
:3
以上实例中通过使用PING命令查看redis服务是否可用，之后设置了w3ckey的值为redis，然后获取w3ckey的值并使得visitor自增3次。在返回的结果中可以看到这些命令一次性向redis服务提交，并最终一次性读取所有服务端的响应
管道技术最显著的优势是提高了redis服务的性能。

## Redis有哪些基本的特点及优势？
Redis（REmote DIctionary Server），特点及优势：
（1）开源。
（2）Redis数据库完全在内存中，使用磁盘仅用于持久性。可以将内存中的数据保持在磁盘中，重启的时候可以再次加载进行使用。
（3）相比许多键值数据存储，Redis拥有一套较为丰富的数据类型。
（4）Redis可以将数据复制到任意数量的从服务器。
（5）异常快速：Redis的速度非常快，每秒能读约11万集合，写约81000条记录。
（6）操作都是原子性：所有Redis操作是原子的，这保证了如果两个客户端同时访问的Redis服务器将获得更新后的值。同时Redis还支持对几个操作全并后的原子性执行。
（7）多功能实用工具：Redis是一个多功能的实用工具，可以在多个用例如缓存、消息、队列使用（Redis原生支持发布/订阅）。

## Redis应用场景
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
zadd ss 1 a 2 b 3 c 4 d 0 x
zincrby ss 10 x  			  # 将x的分数增加10
zrevrange ss 0 2 withscores   # 按分数高低列出前2名


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

## Redis的发布订阅系统如何使用？
Redis发布订阅(pub/sub)是一种消息通信模式：发送者(pub)发送消息，订阅者(sub)接收消息。Redis客户端可以订阅任意数量的频道。

订阅：
redis 127.0.0.1:6379> SUBSCRIBE redisChat
Reading messages... (press Ctrl-C to quit)
1) "subscribe"
2) "redisChat"
3) (integer) 1

发布：（另一个客户端）
redis 127.0.0.1:6379> PUBLISH redisChat "Redis is a great caching technique"
(integer) 1
redis 127.0.0.1:6379> PUBLISH redisChat "Learn redis by w3cschool.cc"
(integer) 1

订阅者客户端显示：
 订阅者的客户端会显示如下消息
1) "message"
2) "redisChat"
3) "Redis is a great caching technique"
1) "message"
2) "redisChat"
3) "Learn redis by w3cschool.cc"

常用命令：
（1）PSUBSCRIBE pattern [pattern ...] 			# 订阅一个或多个符合给定模式的频道
（2）PUBSUB subcommand [argument [argument ...]] 	# 查看订阅与发布系统状态
（3）PUBLISH channel message 					# 将信息发送到指定的频道
（4）PUNSUBSCRIBE [pattern [pattern ...]] 		# 退订所有给定模式的频道
（5）SUBSCRIBE channel [channel ...] 			# 订阅给定的一个或多个频道的信息
（6）UNSUBSCRIBE [channel [channel ...]] 		# 指退订给定的频道

## 什么是Redis HyperLogLog ？
基数：如数据集 {1, 3, 5, 7, 5, 7, 8}，那么这个数据集的基数集为 {1, 3, 5 ,7, 8}, 基数(不重复元素)为5。 基数估计就是在误差可接受的范围内，快速计算基数。
Redis HyperLogLog是用来做基数统计的算法，HyperLogLog的优点是，在输入元素的数量或者体积非常非常大时，计算基数所需的空间总是固定的、并且是很小的。因为HyperLogLog只会根据输入元素来计算基数，而不会储存输入元素本身，所以HyperLogLog不能像集合那样，返回输入的各个元素。
例如：
redis 127.0.0.1:6379> PFADD w3ckey "redis"
1) (integer) 1
redis 127.0.0.1:6379> PFADD w3ckey "mongodb"
1) (integer) 1
redis 127.0.0.1:6379> PFADD w3ckey "mysql"
1) (integer) 1
redis 127.0.0.1:6379> PFCOUNT w3ckey
(integer) 3

基本命令：
（1）PFADD key element [element ...] 		# 添加指定元素到HyperLogLog中
（2）PFCOUNT key [key ...] 					# 返回给定 HyperLogLog 的基数估算值
（3）PFMERGE destkey sourcekey [sourcekey ...]  	# 将多个HyperLogLog合并为一个HyperLogLog


## 如何对Redis数据进行备份和恢复？
创建当前数据库的备份，将在redis安装目录中创建dump.rdb文件
redis 127.0.0.1:6379> SAVE 					
OK

恢复数据：将备份文件 (dump.rdb) 移动到redis安装目录并启动服务即可
redis 127.0.0.1:6379> CONFIG GET dir		# 获取redis目录
1) "dir"
2) "/usr/local/redis/bin"

在后台备份数据
127.0.0.1:6379> BGSAVE
Background saving started


## 如何使用Redis的事务功能？
redis 127.0.0.1:6379> MULTI					# 开始一个事务
OK
redis 127.0.0.1:6379> SET book-name "Mastering C++ in 21 days"		
QUEUED										# 事务入队
redis 127.0.0.1:6379> GET book-name
QUEUED										# 事务入队
redis 127.0.0.1:6379> SADD tag "C++" "Programming" "Mastering Series"
QUEUED										# 事务入队
redis 127.0.0.1:6379> SMEMBERS tag
QUEUED										# 事务入队
redis 127.0.0.1:6379> EXEC					# 执行所有事务
1) OK
2) "Mastering C++ in 21 days"
3) (integer) 3
4) 1) "Mastering Series"
   2) "C++"
   3) "Programming"

常用命令：
（1）DISCARD 								# 取消事务，放弃执行事务块内的所有命令
（2）EXEC 									# 执行所有事务块内的命令
（3）MULTI 									# 标记一个事务块的开始
（4）UNWATCH 								# 取消WATCH命令对所有key的监视
（5）WATCH key [key ...] 					# 监视一个（或多个）key ，如果在事务执行之前这个（或这些） key被其他命令所改动，那么事务将被打断

## redis通信协议
发送格式：
*<参数的个数>CR LF
$<参数1字节数>CR LF
<参数1>CR LF
...
$<参数n字节数>CR LF
<参数n>CR LF

例如`set mykey myvalue`命令，相应的字符串为：*3\r\n$3\r\nSET\r\n$5\r\nmykey\r\n$7\r\nmyvalue\r\n

响应格式：
响应的类型都是由返回数据的第一个字节决定的，有如下几种类型：
"+" 代表一个状态信息 如 +ok 
"-" 代表发送了错误  （如：操作运算操作了错误的类型）
":" 返回的是一个整数  格式如：":11\r\n。
 一些命令返回一些没有任何意义的整数，如LastSave返回一个时间戳的整数值， INCR返回一个加1后的数值；一些命令如exists将返回0或者1代表是否true or false；其他一些命令如SADD, SREM 在确实执行了操作时返回1 ，否则返回0
"$" 返回一个块数据，被用来返回一个二进制安全的字符串
"*" 返回多个块数据（用来返回多个值， 总是第一个字节为"*"， 后面写着包含多少个相应值，如：
C:LRANGE mylist 0 3
S:*4
S:$3
S:foo
S:$3
S:bar
S:$5
$:world
如果指定的值不存在，那么返回*0


## redis的zset使用什么数据结构实现？
zset使用跳表实现。
如果是一个简单的链表，在链表中查找一个元素I的话，需要将整个链表遍历一次。 如果是说链表是排序的，并且节点中还存储了指向前面第二个节点的指针的话，那么在查找一个节点时，仅仅需要遍历N/2个节点即可。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/skiplist_1.png)
这基本上就是跳表的核心思想，其实也是一种通过“空间来换取时间”的一个算法，通过在每个节点中增加了向后的指针，从而提升查找的效率。

如果一个结点存在k个向后的指针的话，那么称该节点是k层的节点。一个跳表的层MaxLevel义为跳表中所有节点中最大的层数。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/skiplist_2.png)

数据结构定义：
typedef struct nodeStructure *node;
typedef struct nodeStructure
{
    keyType key;		// key值
    valueType value;	// value值
    node forward[1];	// 指针数组，根据该节点层数的，不同指向不同大小的数组
};

// 定义跳表数据类型
typedef struct listStructure{
   int level; 	  					/* Maximum level of the list 
   struct nodeStructure * header; 	/* pointer to header */
} * list; 

## 如何对Redis进行配置？有哪些常见的配置选项？
Redis的配置文件位于Redis安装目录下，文件名为redis.conf。
$ ./redis-server redis.conf				# 指定配置文件启动redis

redis 127.0.0.1:6379> CONFIG GET loglevel				# 查看loglevel配置项
1) "loglevel"
2) "notice"

redis 127.0.0.1:6379> CONFIG GET *						# 查看所有配置项
1) "dbfilename"
2) "dump.rdb"
3) "requirepass"
4) ""
5) "masterauth"
6) ""
7) "unixsocket"
8) ""

redis 127.0.0.1:6379> CONFIG SET loglevel "notice"		# 设置loglevel项
OK

常见配置参数：
（1）daemonize no						# 启用守护进程，默认不启用

（2）pidfile /var/run/redis.pid			# 指定pid文件，当以守护进程方式运行时用到

（3）port 6379							# 指定Redis监听端口

（4）bind 127.0.0.1						# 绑定的主机地址

（5）timeout 300					# 当客户端闲置指定时间后关闭连接，如果指定为0，表示关闭该功能

（6）loglevel verbose					# 指定日志记录级别：debug、verbose、notice、warning

（7）logfile stdout  					# 日志记录方式，默认为标准输出，如果配置Redis为守护进程方式运行，而这里又配置为日志记录方式为标准输出，则日志将会发送给/dev/null

（8）databases 16						# 设置数据库的数量，默认数据库为0，可以使用SELECT <dbid>命令在连接上指定数据库id

（9）指定在多长时间内，有多少次更新操作，就将数据同步到数据文件，可以多个条件配合
    save <seconds> <changes>
    Redis默认配置文件中提供了三个条件：
    save 900 1		# 900秒（15分钟）内有1个更改
    save 300 10		# 300秒（5分钟）内有10个更改
    save 60 10000	# 60秒内有10000个更改
 
（10）rdbcompression yes				# 指定存储至本地数据库时是否压缩数据，默认为yes，Redis采用LZF压缩，如果为了节省CPU时间，可以关闭该选项，但会导致数据库文件变的巨大

（11）dbfilename dump.rdb				# 指定本地数据库文件名，默认值为dump.rdb

（12）dir ./							# 指定本地数据库存放目录

（13）slaveof <masterip> <masterport>		# 设置当本机为slav服务时，设置master服务的IP地址及端口，在Redis启动时，它会自动从master进行数据同步

（14）masterauth <master-password> 		# 当master服务设置了密码保护时，slav服务连接master的密码

（15）requirepass foobared				# 设置Redis连接密码，如果配置了连接密码，客户端在连接Redis时需要通过AUTH <password>命令提供密码，默认关闭

（16）maxclients 128					# 设置同一时间最大客户端连接数，默认无限制，Redis可以同时打开的客户端连接数为Redis进程可以打开的最大文件描述符数，如果设置 maxclients 0，表示不作限制。当客户端连接数到达限制时，Redis会关闭新的连接并向客户端返回max number of clients reached错误信息

（17）maxmemory <bytes>					# 指定Redis最大内存限制，Redis在启动时会把数据加载到内存中，达到最大内存后，Redis会先尝试清除已到期或即将到期的Key，当此方法处理后，仍然到达最大内存设置，将无法再进行写入操作，但仍然可以进行读取操作。Redis新的vm机制，会把Key存放内存，Value会存放在swap区。

（18）appendonly no						# 指定是否在每次更新操作后进行日志记录，Redis在默认情况下是异步的把数据写入磁盘，如果不开启，可能会在断电时导致一段时间内的数据丢失。因为 redis本身同步数据文件是按上面save条件来同步的，所以有的数据会在一段时间内只存在于内存中。默认为no

（19）appendfilename appendonly.aof		# 指定更新日志文件名，默认为appendonly.aof

（20）appendfsync everysec  			#指定更新日志条件，共有3个可选值： 
    	no			# 等操作系统进行数据缓存同步到磁盘（快） 
    	always		# 每次更新操作后手动调用fsync()将数据写到磁盘（慢，安全） 
    	everysec	# 每秒同步一次（折衷，默认值）
    
（21）vm-enabled no						# 指定是否启用虚拟内存机制，默认值为no，简单的介绍一下，VM机制将数据分页存放，由Redis将访问量较少的页即冷数据swap到磁盘上，访问多的页面由磁盘自动换出到内存中

（22）vm-swap-file /tmp/redis.swap		# 虚拟内存文件路径，默认值为/tmp/redis.swap，不可多个Redis实例共享

（23）vm-max-memory 0 					# 将所有大于vm-max-memory的数据存入虚拟内存,无论vm-max-memory设置多小,所有索引数据都是内存存储的(Redis的索引数据 就是keys),也就是说,当vm-max-memory设置为0的时候,其实是所有value都存在于磁盘。默认值为0

（24）vm-page-size 32 					# Redis swap文件分成了很多的page，一个对象可以保存在多个page上面，但一个page上不能被多个对象共享，vm-page-size是要根据存储的 数据大小来设定的，作者建议如果存储很多小对象，page大小最好设置为32或者64bytes；如果存储很大大对象，则可以使用更大的page，如果不确定，就使用默认值

（25）vm-pages 134217728				# 设置swap文件中的page数量，由于页表（一种表示页面空闲或使用的bitmap）是在放在内存中的，在磁盘上每8个pages将消耗1byte的内存。

（26）vm-max-threads 4					# 设置访问swap文件的线程数,最好不要超过机器的核数,如果设置为0,那么所有对swap文件的操作都是串行的，可能会造成比较长时间的延迟。默认值为4

（27）glueoutputbuf yes					# 设置在向客户端应答时，是否把较小的包合并为一个包发送，默认为开启

（28）指定在超过一定的数量或者最大的元素超过某一临界值时，采用一种特殊的哈希算法
    hash-max-zipmap-entries 64
    hash-max-zipmap-value 512

（29）activerehashing yes				# 指定是否激活重置哈希，默认为开启（后面在介绍Redis的哈希算法时具体介绍）

（30）include /path/to/local.conf		# 指定包含其它的配置文件，可以在同一主机上多个Redis实例之间使用同一份配置文件，而同时各个实例又拥有自己的特定配置文件

## Redis分区有什么优势和不足？有哪些类型？ 
分区是分割数据到多个Redis实例的处理过程，因此每个实例只保存key的一个子集。

分区的优势：
（1）通过利用多台计算机内存的和值，允许构造更大的数据库。
（2）通过多核和多台计算机，允许扩展计算能力；通过多台计算机和网络适配器，允许扩展网络带宽。

分区的不足：
（1）涉及多个key的操作通常是不被支持的。举例来说，当两个set映射到不同的redis实例上时，就不能对这两个set执行交集操作。
（2）涉及多个key的redis事务不能使用。
（3）当使用分区时，数据处理较为复杂，比如需要处理多个rdb/aof文件，并且从多个实例和主机备份持久化文件。
（4）增加或删除容量也比较复杂。redis集群大多数支持在运行时增加、删除节点的透明数据平衡的能力，但是类似于客户端分区、代理等其他系统则不支持这项特性。然而，一种叫做presharding的技术对此是有帮助的。

分区类型：
Redis有两种类型分区。假设有4个Redis实例R0，R1，R2，R3，和类似user:1，user:2这样的表示用户的多个key，对既定的key有多种不同方式来选择这个key存放在哪个实例中。也就是说，有不同的系统来映射某个key到某个Redis服务。
（1）范围分区
最简单的分区方式是按范围分区，就是映射一定范围的对象到特定的Redis实例。
比如，ID从0到10000的用户会保存到实例R0，ID从10001到 20000的用户会保存到R1，以此类推。
这种方式是可行的，并且在实际中使用，不足就是要有一个区间范围到实例的映射表。这个表要被管理，同时还需要各种对象的映射表，通常对Redis来说并非是好的方法。
（2）哈希分区
另外一种分区方法是hash分区。这对任何key都适用，也无需是object_name:这种形式，像下面描述的一样简单：
用一个hash函数将key转换为一个数字，比如使用crc32 hash函数。对key foobar执行crc32(foobar)会输出类似93024922的整数。
对这个整数取模，将其转化为0-3之间的数字，就可以将这个整数映射到4个Redis实例中的一个了。93024922 % 4 = 2，就是说key foobar应该被存到R2实例中。注意：取模操作是取除的余数，通常在多种编程语言中用%操作符实现。

## Redis有哪些数据类型？每种数据类型的存储极限是什么？
五种数据类型：string（字符串）、hash（哈希）、list（列表）、set（集合）及zset(sorted set：有序集合)。
String
string类型是二进制安全的，所以redis的string可以包含任何数据。比如jpg图片或者序列化的对象。一个string最大能存储512MB。
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

## Redis是以单线程方式运行的吗？有什么问题？
Redis实际上是单线程运行的。虽然Redis是单线程运行的，但是可以同时运行多个Redis客户端进程，常见的并发问题还是会出现。像如下的代码，在get运行之后，set运行之前，powerlevel的值可能会被另一个Redis客户端给改变，从而造成错误：
redis.multi()
current = redis.get('powerlevel')
redis.set('powerlevel', current + 1)
redis.exec()

## 当客户端连接后，Redis会执行什么操作？
Redis通过监听一个TCP端口或者Unix socket的方式来接收来自客户端的连接，当一个连接建立后，Redis内部会进行以下一些操作：
（1）首先，客户端socket会被设置为非阻塞模式，因为Redis在网络事件处理上采用的是非阻塞多路复用模型
（2）然后为这个socket设置TCP_NODELAY属性，禁用Nagle算法
（3）然后创建一个可读的文件事件用于监听这个客户端socket的数据发送

## Redis中如何切换数据库？
在Redis里，数据库简单的使用一个数字编号来进行辨认，默认数据库的数字编号是0。如果想切换到一个不同的数据库，可以使用select命令来实现。在命令行界面里键入select 1，Redis应该会回复一条OK的信息，然后命令行界面里的提示符会变成类似redis 127.0.0.1:6379[1]>这样。如果想切换回默认数据库，只要在命令行界面键入select 0即可。


## 使用redis的setnx来实现锁存在什么问题？
SETNX，是「SET if Not eXists」的缩写，也就是只有不存在的时候才设置。

// 缓存过期时通过SetNX获取锁，如果成功了就更新缓存，然后删除锁
$ok = $redis->setNX($key, $value);
if ($ok) {
    $cache->update();
    $redis->del($key);
}
存在问题：如果请求执行因为某些原因意外退出了，导致创建了锁但是没有删除锁，那么这个锁将一直存在，以至于以后缓存再也得不到更新。

因此需要给锁加一个过期时间以防不测。
// 加锁
$redis->multi();
$redis->setNX($key, $value);
$redis->expire($key, $ttl);
$redis->exec();
存在问题：当多个请求到达时，虽然只有一个请求的SetNX可以成功，但是任何一个请求的Expire却都可以成功，如此就意味着即便获取不到锁，也可以刷新过期时间，如果请求比较密集的话，那么过期时间会一直被刷新，导致锁一直有效。

从 2.6.12 起，SET涵盖了SETEX的功能，并且SET本身已经包含了设置过期时间的功能：
$ok = $redis->set($key, $value, array('nx', 'ex' => $ttl));
if ($ok) {
    $cache->update();
    $redis->del($key);
}

可以利用incr命令的原子性来实现锁：
$value = $redis->get($lock);
if($value < 1 ){
	$redis->incr($lock,1);
	// ...
	$redis->decr($lock,1);
}

不使用incr：
// 被WATCH的键会被监视，并会发觉这些键是否被改动过了。 如果有至少一个被监视的键在EXEC执行之前被修改了，那么整个事务都会被取消
	WATCH mykey
		$val = GET mykey   // 乐观锁
		$val = $val + 1
	MULTI
		SET mykey $val
	EXEC























































