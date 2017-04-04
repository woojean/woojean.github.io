# 有序数组中的二分查找（Java）

在N个键的有序数组中进行二分查找最多需要lgN+1次比较，无论成功与否。

```java
public class BinarySearch<Key extends Comparable<Key>, Value> {
    private Key[] keys;
    private Value[] vals;
    private int N = 0;

	@SuppressWarnings("unchecked")
    public BinarySearch(int capacity) { 
        keys = (Key[]) new Comparable[capacity]; 
        vals = (Value[]) new Object[capacity]; 
    }   

// 在put时调用，当数组容量不够时扩容
	@SuppressWarnings("unchecked")
    private void resize(int capacity) {
        Key[]   tempk = (Key[])   new Comparable[capacity];
        Value[] tempv = (Value[]) new Object[capacity];
        for (int i = 0; i < N; i++) {
            tempk[i] = keys[i];
            tempv[i] = vals[i];
        }
        vals = tempv;
        keys = tempk;
    }
 
// 查询接口
    public Value get(Key key) {
        if (N==0) 
			return null;
        int i = rank(key); 
        if (i < N && keys[i].compareTo(key) == 0) return vals[i];
        return null;
    } 

// 返回表中小于给定键的键的数量 -- 循环版
    public int rank(Key key) {
        int lo = 0, hi = N-1; 
        while (lo <= hi) { 
            int mid = lo + (hi - lo) / 2; 
            int cmp = key.compareTo(keys[mid]); 
            if(cmp < 0) hi = mid - 1; 
            else if (cmp > 0) lo = mid + 1; 
            else return mid; 
        } 
        return lo;
}

// 返回表中小于给定键的键的数量 -- 递归版
public int rank(Key key,int lo,int hi) {
        if( hi < lo ) 
			return lo;
		int mid = lo+(hi - lo)/2;
		int cmp = key.compareTo(keys[mid]);
		if( cmp<0 )
			return rank(key,lo,mid-1);
		else if(cmp>0)
			return rank(key,mid+1,hi);
		else 
			return mid;
    }


// 在插入新元素前将所有较大的键向后移动一格
    public void put(Key key, Value val)  {
        int i = rank(key);
        if (i < N && keys[i].compareTo(key) == 0) {
            vals[i] = val;
            return;
        }
		
        if (N == keys.length) resize(2*keys.length);

        for (int j = N; j > i; j--)  {
            keys[j] = keys[j-1];
            vals[j] = vals[j-1];
        }
        keys[i] = key;
        vals[i] = val;
        N++;
    } 
 
    public static void main(String[] args) { 
        BinarySearch<String, String> st = new BinarySearch<String, String>(10);
        st.put("www.cs.princeton.edu",   "128.112.136.11");
        st.put("www.princeton.edu",      "128.112.130.211");
        ...
        st.put("www.iitb.ac.in",         "202.68.145.210");
		
        System.out.println(st.get("www.iitb.ac.in"));
    }
}
```