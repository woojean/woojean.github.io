# Hashmap如何同步?

当我们需要一个同步的HashMap时，有两种选择：
•使用Collections.synchronizedMap（..）来同步HashMap。
•使用ConcurrentHashMap
这两个选项之间的首选是使用ConcurrentHashMap，这是因为我们不需要锁定整个对象，以及通过ConcurrentHashMap分区地图来获得锁。