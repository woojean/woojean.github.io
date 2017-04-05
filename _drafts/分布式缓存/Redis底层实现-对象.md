# Redis底层实现-对象

Redis并没有直接使用简单动态字符串（SDS）、双端链表、字典、压缩列表、整数集合这些数据结构来实现键值对数据库，而是基于这些数据结构创建了一个对象系统，这个系统包含字符串对象、列表对象、哈希对象、集合对象和有序集合对象这五种类型的对象，每种对象都用到了至少一种如上的数据结构。

## 对象的类型与编码
Redis使用对象来表示数据库中的键和值，每当在Redis的数据库中新创建一个键值对时，至少会创建两个对象，一个对象用作键值对的键（键对象），另一个对象用作键值对的值（值对象）。

```
typedef struct redisObject {    
  unsigned type:4;     // 类型(字符串、列表、哈希、集合、有序集合)
  unsigned encoding:4; // 编码(即底层数据结构的实际类型)
  void *ptr;           // 指向底层实现数据结构的指针
  // ...
} robj;
```

键总是一个字符串对象，而值则可以是字符串对象、列表对象、哈希对象、集合对象或者有序集合对象的其中一种。

## 字符串对象
字符串对象的编码可以是int、raw或者embstr，取决于实际存储的内容。
```
SET number 10086  # int
SET story "Long, long ago there lived a king ..."  # raw
SET msg "hello"   # embstr
```

注意：可以用long double类型表示的浮点数在Redis中也是作为字符串值来保存的。
```
redis> SET pi 3.14
OK

redis> OBJECT ENCODING pi
"embstr"

redis> INCRBYFLOAT pi 2.0
"5.14"

redis> OBJECT ENCODING pi
"embstr"
```

int编码的字符串对象和embstr编码的字符串对象在条件满足的情况下，会被转换为raw编码的字符串对象。
```
redis> SET number 10086  # int
redis> APPEND number " is a good number!"  # raw
```
embstr编码的字符串对象实际上是只读的。当对embstr编码的字符串对象执行任何修改命令时（比如APPEND），程序会先将对象的编码从embstr转换成raw，然后再执行修改命令。

## 列表对象
列表对象的编码可以是ziplist或者linkedlist。

当列表对象可以同时满足以下两个条件时，列表对象使用ziplist编码：   1.列表对象保存的所有字符串元素的长度都小于64字节；  
2.列表对象保存的元素数量小于512个；
不能满足这两个条件的列表对象需要使用linkedlist编码。当然，这两个限制在Redis配置文件中是可以修改的list-max-ziplist-value，list-max-ziplist-entries。

当条件不满足时，ziplist编码的对象会自动转换为linkedlist编码。

## 哈希对象
哈希对象的编码可以是ziplist或者hashtable。

ziplist编码的哈希对象使用压缩列表作为底层实现，每当有新的键值对要加入时，程序会先将保存了键的压缩列表节点推入到压缩列表表尾，然后再将保存了值的压缩列表节点推入到压缩列表表尾，因此保存了同一键值对的两个节点总是紧挨在一起，保存键的节点在前，保存值的节点在后。

hashtable编码的哈希对象使用字典作为底层实现，哈希对象中的每个键值对都使用一个字典键值对来保存。

当哈希对象可以同时满足以下两个条件时，哈希对象使用ziplist编码：   1.哈希对象保存的所有键值对的键和值的字符串长度都小于64字节；   
2.哈希对象保存的键值对数量小于512个；不能满足这两个条件的哈希对象需要使用hashtable编码。
限制条件可以在配置文件中修改（hash-max-ziplist-value，hash-max-ziplist-entries）。
当条件不满足时，ziplist编码的对象会自动转换为hashtable编码。

## 集合对象
集合对象的编码可以是intset或者hashtable。
intset编码的集合对象使用整数集合作为底层实现，集合对象包含的所有元素都被保存在整数集合里面。
，hashtable编码的集合对象使用字典作为底层实现，字典的每个键都是一个字符串对象，每个字符串对象包含了一个集合元素，而字典的值则全部被设置为NULL。

当集合对象可以同时满足以下两个条件时，对象使用intset编码：   1.集合对象保存的所有元素都是整数值；   
2.集合对象保存的元素数量不超过512个。（可以配置set-max-intset-entries）
当条件不满足时，intset编码的对象会自动转换为hashtable编码。

## 有序集合对象
有序集合的编码可以是ziplist或者skiplist。

ziplist编码的压缩列表对象使用压缩列表作为底层实现，每个集合元素使用两个紧挨在一起的压缩列表节点来保存，第一个节点保存元素的成员（member），而第二个元素则保存元素的分值（score）。压缩列表内的集合元素按分值从小到大进行排序。

当有序集合对象可以同时满足以下两个条件时，对象使用ziplist编码：   1.有序集合保存的元素数量小于128个；            # zset-max-ziplist-entries
2.有序集合保存的所有元素成员的长度都小于64字节； # zset-max-ziplist-value
当条件不满足时，ziplist编码的对象会自动转换为skiplist编码。

## 类型检查与命令多态
为了确保只有指定类型的键可以执行某些特定的命令，在执行一个类型特定的命令之前，Redis会先检查输入键的类型是否正确，然后再决定是否执行给定的命令。（redisObject结构的type属性）

Redis除了会根据值对象的类型来判断键是否能够执行指定命令之外，还会根据值对象的编码方式，选择正确的命令实现代码来执行命令。

## 内存回收
Redis在自己的对象系统中构建了一个引用计数（redisObject结构的refcount属性）技术实现的内存回收机制，通过这一机制，程序可以通过跟踪对象的引用计数信息，在适当的时候自动释放对象并进行内存回收。

## 对象共享
对象的引用计数属性还带有对象共享的作用。在Redis中，让多个键共享同一个值对象需要执行以下两个步骤：
1.将数据库键的值指针指向一个现有的值对象；
2.将被共享的值对象的引用计数增一。

目前，Redis会在初始化服务器时创建一万个字符串对象，这些对象包含了从0到9999的所有整数值，当服务器需要用到值为0到9999的字符串对象时（包括嵌套对象的引用），服务器就会使用这些共享对象，而不是新创建对象。

查看引用计数：
```
redis> SET A 100
OK

redis> OBJECT REFCOUNT A
(integer) 2

redis> SET B 100
OK

redis> OBJECT REFCOUNT A
(integer) 3

redis> OBJECT REFCOUNT B
(integer) 3
```

尽管共享更复杂的对象可以节约更多的内存，但受到CPU时间的限制，Redis只对包含整数值的字符串对象进行共享。

## 对象的空转时长
redisObject结构的lru属性记录了对象最后一次被命令程序访问的时间。

`OBJECT IDLETIME`命令可以打印出给定键的空转时长，这个命令在访问键的值对象时，不会修改值对象的lru属性。

如果服务器打开了maxmemory选项，并且服务器用于回收内存的算法为volatile-lru或者allkeys-lru，那么当服务器占用的内存数超过了maxmemory选项所设置的上限值时，空转时长较高的那部分键会优先被服务器释放，从而回收内存。
