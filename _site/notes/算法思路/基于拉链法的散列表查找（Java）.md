# 基于拉链法的散列表查找（Java）

所有hash值相同的节点（发生冲突了）都存放在同一个链表中。

```java
public class SeparateChainingHashST<Key, Value> {
    private int N; 
    private int M;
    private SequentialSearchST<Key, Value>[] st; // 一个链表，用于存放hash结果相同的所有节点

    public SeparateChainingHashST(int M) {
        this.M = M;
        st = (SequentialSearchST<Key, Value>[]) new SequentialSearchST[M];
        for (int i = 0; i < M; i++)
            st[i] = new SequentialSearchST<Key, Value>();
    } 

    // 计算hash值
    private int hash(Key key) {
        return (key.hashCode() & 0x7fffffff) % M;  // hashCode()是Object的方法
    } 

    // 查找接口：先算出hash值用来确定所要查找的链表，再在该链表中用指定的key进行查找
    public Value get(Key key) {
        int i = hash(key);
        return st[i].get(key);
    } 

    // 往hash列表中加入节点
    public void put(Key key, Value val) {
        int i = hash(key);
        if (!st[i].contains(key)) N++;
        st[i].put(key, val);
    } 
}
```