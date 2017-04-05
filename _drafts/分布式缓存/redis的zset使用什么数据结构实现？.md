# redis的zset使用什么数据结构实现？

zset使用跳表实现。
如果是一个简单的链表，在链表中查找一个元素I的话，需要将整个链表遍历一次。 如果是说链表是排序的，并且节点中还存储了指向前面第二个节点的指针的话，那么在查找一个节点时，仅仅需要遍历N/2个节点即可。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/skiplist_1.png)
这基本上就是跳表的核心思想，其实也是一种通过“空间来换取时间”的一个算法，通过在每个节点中增加了向后的指针，从而提升查找的效率。

如果一个结点存在k个向后的指针的话，那么称该节点是k层的节点。一个跳表的层MaxLevel义为跳表中所有节点中最大的层数。
![image](https://github.com/woojean/woojean.github.io/blob/master/images/skiplist_2.png)

数据结构定义：
```c
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
```