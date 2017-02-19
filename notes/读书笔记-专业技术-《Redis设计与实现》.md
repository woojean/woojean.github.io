# Redis设计与实现

# 前言
关系数据库并不直接支持交集计算操作，要计算两个集合的交集，除了需要对两个数据表执行合并（join）操作之外，还需要对合并的结果执行去重复（distinct）操作，最终导致交集操作的实现变得异常复杂。


# 第1章　引言
略。


# 第2章　简单动态字符串

## SDS，simple dynamic string，简单动态字符串。
在Redis的数据库里面，包含字符串值的`键值对`（注意，键也是）在底层都是由SDS实现的。（在Redis源码里，C字符串只会作为字符串字面量用在一些无须对字符串值进行修改的地方，比如打印日志）

如：
```
redis> SET msg "hello world"
OK
```
Redis将在数据库中创建一个新的键值对，其中键是一个字符串对象，对象的底层实现是一个保存着字符串“msg”的SDS。值也是一个字符串对象，对象的底层实现是一个保存着字符串“hello world”的SDS。

再如：
```
redis> RPUSH fruits "apple" "banana" "cherry"
(integer) 3
```
键是一个字符串对象，对象的底层实现是一个保存了字符串“fruits”的SDS。值是一个列表对象，列表对象包含了三个字符串对象，这三个字符串对象分别由三个SDS实现：第一个SDS保存着字符串“apple”，第二个SDS保存着字符串“ba-nana”，第三个SDS保存着字符串“cherry”。

除了用来保存数据库中的字符串值之外，SDS还被用作缓冲区（buffer）：AOF模块中的AOF缓冲区，以及客户端状态中的输入缓冲区，都是由SDS实现的。

## SDS的定义
```
struct sdshdr {    
  int len;     // 记录buf数组中已使用字节的数量，等于SDS所保存字符串的长度
  int free;    // 记录buf数组中未使用字节的数量
  char buf[];  // 字节数组，用于保存字符串(最后一个字节为`\0`，是额外分配的，不算在len中，即当len为5、free为5时，buf的实际长度为11。遵循空字符结尾这一惯例的好处是，SDS可以直接重用一部分C字符串函数库里面的函数)
};
```

## SDS相比较C字符串的优势
1.常数复杂度获取字符串长度；  # STRLEN

2.减少内存分配系统调用，并杜绝缓冲区溢出和内存泄露；
通过未使用空间，SDS实现了空间预分配和惰性空间释放两种优化策略；
空间预分配策略：
（1）如果对SDS进行修改之后，SDS的长度（也即是len属性的值）将小于1MB，那么程序分配和len属性同样大小的未使用空间，这时SDS len属性的值将和free属性的值相同。
（2）如果对SDS进行修改之后，SDS的长度将大于等于1MB，那么程序会分配1MB的未使用空间。
惰性空间释放策略：
（1）当SDS的API需要缩短SDS保存的字符串时，程序并不立即使用内存重分配来回收缩短后多出来的字节，而是使用free属性将这些字节的数量记录起来，并等待将来使用。（SDS也提供了专门的API用来真正释放空间）

3.二进制安全；
所有SDS API都会以处理二进制的方式来处理SDS存放在buf数组里的数据，程序不会对其中的数据做任何限制、过滤、或者假设，数据在写入时是什么样的，它被读取时就是什么样，所以SDS不仅仅可以保存文本数据。（因为SDS使用len属性的值而不是空字符来判断字符串是否结束）

4.兼容部分C字符串函数；

## SDS API
略。


# 第3章　链表
Redis中除了链表键之外，发布与订阅、慢查询、监视器等功能也用到了链表，Redis服务器本身还使用链表来保存多个客户端的状态信息，以及使用链表来构建客户端输出缓冲区。

## Redis链表(双端链表)的定义
```
// 链表节点定义
typedef struct listNode {    
  struct listNode * prev; // 前置节点 
  struct listNode * next; // 后置节点
  void * value;           // 节点的值
}listNode;

// 链表类型定义
typedef struct list {
  listNode * head;   // 表头节点  
  listNode * tail;   // 表尾节点        
  unsigned long len; // 链表所包含的节点数量       
  void *(*dup)(void *ptr);  // 节点值复制函数         
  void (*free)(void *ptr);  // 节点值释放函数        
  int (*match)(void *ptr,void *key); // 节点值对比函数
} list;
```

## Redis链表的特性
1.双端：链表节点带有prev和next指针，获取某个节点的前置节点和后置节点的复杂度都是O（1）。   2.无环：表头节点的prev指针和表尾节点的next指针都指向NULL，对链表的访问以NULL为终点。   
3.带表头指针和表尾指针：通过list结构的head指针和tail指针，程序获取链表的表头节点和表尾节点的复杂度为O（1）。
4.带链表长度计数器：程序使用list结构的len属性来对list持有的链表节点进行计数，程序获取链表中节点数量的复杂度为O（1）。
5.多态：链表节点使用void*指针来保存节点值，并且可以通过list结构的dup、free、match三个属性为节点值`设置类型特定函数`，所以链表可以用于保存各种不同类型的值。

# 链表API
略。


# 第4章　字典
Redis的数据库就是使用字典来作为底层实现的，对数据库的增、删、查、改操作也是构建在对字典的操作之上的。

## 字典的实现
Redis的字典使用哈希表作为底层实现，一个哈希表里面可以有多个哈希表节点，而每个哈希表节点就保存了字典中的一个键值对。

```
// 哈希表
typedef struct dictht {    
  dictEntry **table;    // 哈希表数组，每个dictEntry都保存着一个键值对
  unsigned long size;   // 哈希表大小  
  unsigned long sizemask;  // 哈希表大小掩码，用于和哈希值一起计算哈希索引（总是等于size-1）
  unsigned long used;  // 该哈希表已有节点的数量
} dictht;


// 哈希表节点
typedef struct dictEntry {
  void *key;// 键 

  union{  // 值      
    void *val;        
    uint64_tu64;        
    int64_ts64;    
  } v;      

  struct dictEntry *next; // 指向下个哈希表节点，形成链表（解决键冲突）
} dictEntry;


// 字典
typedef struct dict {    
  dictType *type;   // 类型特定函数
  void *privdata;   // 私有数据
  dictht ht[2];     // 哈希表，字典只使用ht[0]哈希表，ht[1]哈希表只会在对ht[0]哈希表进行rehash时使用
  in trehashidx;    // rehash索引，当rehash不在进行时，值为-1
} dict;
```
type属性是一个指向dictType结构的指针，每个dictType结构保存了一簇用于操作特定类型键值对的函数，Redis会为用途不同的字典设置不同的类型特定函数。而privdata属性则保存了需要传给那些类型特定函数的可选参数。

```
typedef struct dictType {    
  unsigned int (*hashFunction)(const void *key);  // 计算哈希值的函数  
  void *(*keyDup)(void *privdata, const void *key);  // 复制键的函数  
  void *(*valDup)(void *privdata, const void *obj);   // 复制值的函数 
  int (*keyCompare)(void *privdata, const void *key1, const void *key2);   // 对比键的函数 
  void (*keyDestructor)(void *privdata, void *key);  // 销毁键的函数  
  void (*valDestructor)(void *privdata, void *obj);  // 销毁值的函数
} dictType;
```

## 哈希算法
当要将一个新的键值对添加到字典里面时，程序需要先根据键值对的键计算出哈希值和索引值，然后再根据索引值，将包含新键值对的哈希表节点放到哈希表数组的指定索引上面。

Redis使用MurmurHash算法来计算键的哈希值，这种算法的优点在于，即使输入的键是有规律的，算法仍能给出一个很好的随机分布性，并且算法的计算速度也非常快。

## 解决键冲突
当有两个或以上数量的键被分配到了哈希表数组的同一个索引上面时，称这些键发生了冲突。Redis的哈希表使用链地址法来解决键冲突，每个哈希表节点都有一个next指针，多个哈希表节点可以用next指针构成一个单向链表，被分配到同一个索引上的多个节点可以用这个单向链表连接起来，以此解决键冲突的问题。

因为dictEntry节点组成的链表没有指向链表表尾的指针，所以为了速度考虑，程序总是将新节点添加到链表的表头位置（复杂度为O（1）），排在其他已有节点的前面。

## rehash
当哈希表保存的键值对数量太多或者太少时，程序需要对哈希表的大小进行相应的扩展或者收缩。这可以通过执行rehash（重新散列）操作来完成，Redis对字典的哈希表执行rehash的步骤如下：
1.为字典的ht[1]哈希表分配空间，这个哈希表的空间大小取决于要执行的操作，以及ht[0]当前包含的键值对数量（也即是ht[0].used属性的值）。
2.将保存在ht[0]中的所有键值对rehash到ht[1]上面：re-hash指的是重新计算键的哈希值和索引值，然后将键值对放置到ht[1]哈希表的指定位置上。
3.当ht[0]包含的所有键值对都迁移到了ht[1]之后（ht[0]变为空表），释放ht[0]，将ht[1]设置为ht[0]，并在ht[1]新创建一个空白哈希表，为下一次rehash做准备。

当以下条件中的任意一个被满足时，程序会自动开始对哈希表执行扩展操作：
1.服务器目前没有在执行BGSAVE命令或者BGREWRITEAOF命令，并且哈希表的负载因子大于等于1。
2.服务器目前正在执行BGSAVE命令或者BGREWRITEAOF命令，并且哈希表的负载因子大于等于5。
负载因子= 哈希表已保存节点数量/ 哈希表大小
另一方面，当哈希表的负载因子小于0.1时，程序自动开始对哈希表执行收缩操作。

在执行BGSAVE命令或BGREWRITEAOF命令的过程中，Redis需要创建当前服务器进程的子进程，而大多数操作系统都采用写时复制（copy-on-write）技术来优化子进程的使用效率，所以在子进程存在期间，服务器会提高执行扩展操作所需的负载因子，从而尽可能地避免在子进程存在期间进行哈希表扩展操作，这可以避免不必要的内存写入操作，最大限度地节约内存。

为了避免rehash对服务器性能造成影响，服务器不是一次性将ht[0]里面的所有键值对全部rehash到ht[1]，而是分多次、渐进式地将ht[0]里面的键值对慢慢地rehash到ht[1]。在渐进式rehash进行期间，字典的删除（delete）、查找（find）、更新（update）等操作会在两个哈希表上进行。（例如，要在字典里面查找一个键的话，程序会先在ht[0]里面进行查找，如果没找到的话，就会继续到ht[1]里面进行查找。在渐进式rehash执行期间，新添加到字典的键值对一律会被保存到ht[1]里面，而ht[0]则不再进行任何添加操作）

## 字典API
略。


# 第5章　跳跃表
跳跃表（skiplist）是一种有序数据结构，它通过在每个节点中维持多个指向其他节点的指针，从而达到快速访问节点的目的。支持平均O（logN）、最坏O（N）复杂度的节点查找，还可以通过顺序性操作来批量处理节点。

Redis只在两个地方用到了跳跃表，一个是实现有序集合键，另一个是在集群节点中用作内部数据结构。

## 跳跃表节点
```
typedef struct zskiplistNode {  

  struct zskiplistLevel {           // 层      
    struct zskiplistNode *forward;  // 前进指针    
    unsigned int span;              // 跨度 (记录两个节点之间的距离)
  } level[];    

  struct zskiplistNode *backward;  // 后退指针(每次只能后退至前一个节点)
  double score;                    // 分值(double类型的浮点数,排序的依据)
  robj *obj;                       // 成员对象（唯一性）
} zskiplistNode;
```

## 跳跃表
```
typedef struct zskiplist {    
  structz skiplistNode *header, *tail;  // 表头节点和表尾节点
  unsigned long length;   // 表中节点的数量
  int level;              // 表中层数最大的节点的层数（1至32之间的随机数）
} zskiplist;
```

## 跳跃表API
略。


# 第6章　整数集合
当一个集合只包含整数值元素，并且这个集合的元素数量不多时，Redis就会使用整数集合作为底层实现。
```
redis> SADD numbers 1 3 5 7 9
(integer) 5
redis> OBJECT ENCODING numbers
"intset"
```

## 整数集合的实现
```
typedef struct intset {    
  uint32_t encoding;  // 编码方式  
  uint32_t length;    // 集合包含的元素数量 
  int8_t contents[];  // 保存元素的数组，按值大小有序排列且不重复
} intset;
```

## 升级
虽然intset结构将contents属性声明为int8_t类型的数组，但实际上contents数组并不保存任何int8_t类型的值，contents数组的真正类型取决于encoding属性的值。
每当要将一个新元素添加到整数集合里面，并且新元素的类型比整数集合现有所有元素的类型都要长时，整数集合需要先进行升级，然后才能将新元素添加到整数集合里面。

升级过程（主要就是内存的重新分配），详略。

升级的好处：
1.提升整数集合的灵活性：可以随意地将int16_t、int32_t或者int64_t类型的整数添加到集合中，而不必担心出现类型错误；
2.尽可能地节约内存：升级操作只会在有需要的时候进行；

## 降级
整数集合不支持降级操作，一旦对数组进行了升级，编码就会一直保持升级后的状态。

## 整数集合API
略。


# 第7章　压缩列表
压缩列表（ziplist）是列表和哈希的底层实现之一。当一个列表只包含少量列表项，并且每个列表项要么就是小整数值，要么就是长度比较短的字符串，那么Redis就会使用压缩列表来做列表的底层实现。（哈希的键值对的键和值具有这样的特征时，也会使用ziplist来实现）

## 压缩列表的构成
压缩列表是Redis为了节约内存而开发的，是由一系列特殊编码的连续内存块组成的顺序型（sequential）数据结构。一个压缩列表可以包含任意多个节点（entry），每个节点可以保存一个字节数组或者一个整数值。

## 压缩列表节点的构成
略。

## 压缩列表API
添加新节点到压缩列表，或者从压缩列表中删除节点，可能会引发连锁更新操作。
连锁更新在最坏情况下需要对压缩列表执行N次空间重分配操作，而每次空间重分配的最坏复杂度为O（N），所以连锁更新的最坏复杂度为O（N2）。
但这种操作出现的几率并不高，需要当前的内存分布情况恰好满足一定的条件时才可能触发。


# 第8章　对象
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


# 第9章　数据库






































































