# 无序链表中的顺序查找（Java）

```java
public class SequentialSearchST<Key, Value> {
    private Node first; 

// 内部类，用来表示链表的节点
    private class Node {
        private Key key;
        private Value val;
        private Node next;

        public Node(Key key, Value val, Node next)  {
            this.key  = key;
            this.val  = val;
            this.next = next;
        }
    }

// 根据键值进行查找
    public Value get(Key key) {
        for (Node x = first; x != null; x = x.next) {
            if (key.equals(x.key)) return x.val;
        }
        return null;
    }
	
// 往链表添加节点，相同键值的数据会被覆盖
    public void put(Key key, Value val) {
        for (Node x = first; x != null; x = x.next)
            if (key.equals(x.key)) { x.val = val; return; }
        first = new Node(key, val, first);
    }

    public static void main(String[] args) {
        SequentialSearchST<String, String> st = new SequentialSearchST<String, String>();

        st.put("www.cs.princeton.edu",   "128.112.136.11");
        st.put("www.princeton.edu",      "128.112.130.211");
        st.put("www.math.princeton.edu", "128.112.18.11");
...		
		System.out.println(st.get("www.cs.princeton.edu"));
    }
}
```