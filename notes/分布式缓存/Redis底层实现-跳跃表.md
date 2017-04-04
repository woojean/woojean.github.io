# Redis底层实现-跳跃表

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