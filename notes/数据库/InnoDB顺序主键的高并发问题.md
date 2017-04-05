# InnoDB顺序主键的高并发问题

## InnoDB和MyISAM的数据分布对比
InnoDB中聚簇索引的每一个叶子节点都包含了主键值、事务ID、用于事务和MVCC的回滚指针以及所有的剩余列。如果主键是一个列前缀索引，InnoDB也会包含完整的主键和剩下的其他列。
（详略）

使用InnoDB时应该尽可能地按主键顺序插入数据，并且尽可能地使用单调增加的聚簇键的值来插入新行。


## InnoDB顺序主键的高并发问题
在高并发情况下，在InnoDB中按主键顺序插入可能会导致明显的争用，当前主键的上界会成为“热点”，导致锁竞争。
此外AUTO_INCREMENT锁机制（表锁）也会导致竞争。可以通过修改innodb_autoinc_lock_mode来优化：
innodb_autoinc_lock_mode = 0 ("traditional" lock mode：全部使用表锁)
innodb_autoinc_lock_mode = 1 (默认)("consecutive" lock mode：可`预判行数`时使用新方式，不可时使用表锁) 
innodb_autoinc_lock_mode = 2 ("interleaved" lock mode：全部使用新方式，不安全，不适合replication)