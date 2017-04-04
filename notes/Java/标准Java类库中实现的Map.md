# 标准Java类库中实现的Map

HashMap：插入和查询的开销是固定的；
LinkedHashMap：迭代遍历时，取得键值对的顺序就是其插入顺序或者最近最少使用次序；
TreeMap：基于红黑树，是唯一带有subMap()方法的Map，是目前唯一实现的SortedMap；
WeakHashMap：如果Map之外没有引用指向某个键，则此键可以被垃圾回收器回收；
ConcurrentHashMap：线程安全的Map，无需同步加锁；
IdentityHashMap：使用==代替equals()对键进行比较；

hashCode()是Object中定义的方法，返回代表对象的整数值。