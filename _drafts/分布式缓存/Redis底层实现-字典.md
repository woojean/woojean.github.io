# Redis底层实现-字典

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

