# 基于线性探测法的散列表查找（Java）

用大小为M的数组保存N个键值对，M>N，依靠数组中的空位来解决碰撞冲突。算出hash值后，找到其在数组中对应的索引，检查改索引对应的值和被查找的值是否相同，如果不同则将索引增大继续查找，到达数组结尾时折回数组的开头，直到找到该键或者遇到一个空元素。

```java
public class LinearProbingHashST<Key, Value> {
    private int N;
    private int M;
    private Key[] keys;
    private Value[] vals;

	@SuppressWarnings("unchecked")
    public LinearProbingHashST(int capacity) {
        M = capacity;
        keys = (Key[])   new Object[M];
        vals = (Value[]) new Object[M];
    }

    private int hash(Key key) {
        return (key.hashCode() & 0x7fffffff) % M;
    }

    private void resize(int capacity) {
        LinearProbingHashST<Key, Value> temp = new LinearProbingHashST<Key, Value>(capacity);
        for (int i = 0; i < M; i++) {
            if (keys[i] != null) {
                temp.put(keys[i], vals[i]);
            }
        }
        keys = temp.keys;
        vals = temp.vals;
        M = temp.M;
    }

// 往散列表表中插入数据
    public void put(Key key, Value val) {
        if (N >= M/2) resize(2*M);
        int i;
        for (i = hash(key); keys[i] != null; i = (i + 1) % M) {
            if (keys[i].equals(key)) { 
				vals[i] = val; 
				return; 
			}
        }
        keys[i] = key;
        vals[i] = val;
        N++;
    }

// 搜索
    public Value get(Key key) {
        for (int i = hash(key); keys[i] != null; i = (i + 1) % M) 
            if (keys[i].equals(key))
                return vals[i];
        return null;
    }

    public static void main(String[] args) { 
        LinearProbingHashST<String, String> st = new LinearProbingHashST<String, String>(10);

        st.put("www.cs.princeton.edu",   "128.112.136.11");
        st.put("www.princeton.edu",      "128.112.130.211");
        ...
		
		System.out.println(st.get("www.cs.princeton.edu")); 
    }
}
```