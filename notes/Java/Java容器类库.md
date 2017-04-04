# Java容器类库

总体上所有数据结构实现了`2个根接口`：Collection、Map，独立于这2个根接口之外还有`3个辅助根接口`：Iterator、Comparable、Comparator。

Collection是所有列表类数据结构的接口，Map是所有映射类数据结构的接口，Iterator用于遍历一个序列，Collection可以生成这样的序列，而Map接口可以生成Collection（entrySet()、values()）。：
所有实现Collection的数据结构都支持生成一个ListIterator接口，该接口是Iterator的子类。
```
Map -----生成-----> Collection -----生成-----> Iterator
                                                 ↑
                       ...      ---生成-----> ListIterator
```

## Collection族的继承树
```
Collection接口
    - List接口
       - ArrayList（标记了RandomAccess接口）
       -------------------- LinkedList（同时实现了List、Queue接口）
    - Set接口                 |
       - HashSet             |
         - LinkedHashSet     |
       - TreeSet             |
    - Queue接口              |
       - PriorityQueue      |
       ----------------------
```
除了TreeSet，其他Set都拥有与Collection完全一样的接口。
以上未包括Queue的concurrent实现。
新版本容器类库没有Stack，可以用LinkedList模拟（也没有Queue类）。

## Map族的继承树
```
Map接口
    - HashMap
        - LinkedHashMap
    - TreeMap
```
Comparable与Comparator可以互相生成。

不应该再使用过时的Vector、Hashtable、Stack等容器类。