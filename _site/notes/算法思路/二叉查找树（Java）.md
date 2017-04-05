# 二叉查找树（Java）

一棵二叉查找树是一棵二叉树，其中每个结点都含有一个Comparable的键及其相关联的值，且每个结点的键都大于其左子树中的任意结点的键而小于右子树的任意结点的值。

```java
public class BST<Key extends Comparable<Key>, Value> {
    private Node root;

    private class Node {
        private Key key;
        private Value val;
        private Node left, right; 
        private int N; // 含根节点在内的整个二叉树中的节点数

        public Node(Key key, Value val, int N) {
            this.key = key;
            this.val = val;
            this.N = N;
        }
    }
	
    private int size(Node x) {
        if (x == null) return 0;
        else return x.N;
    }
	
	// 查找接口
    public Value get(Key key) {
        return get(root, key);
    }

	// 实际的递归查找
    private Value get(Node x, Key key) {
        if (x == null) return null;
        int cmp = key.compareTo(x.key);
        if	(cmp < 0) return get(x.left, key);
        else if (cmp > 0) return get(x.right, key);
        else	return x.val;
    }

	// 添加新节点的接口
    public void put(Key key, Value val) {
        root = put(root, key, val);
    }

	// 实际的递归添加
    private Node put(Node x, Key key, Value val) {
        if (x == null) return new Node(key, val, 1);
        int cmp = key.compareTo(x.key);
        if      
			(cmp < 0) x.left  = put(x.left,key,val);
        else 
			if (cmp > 0) x.right = put(x.right,key,val);
        else	
			x.val = val;
        x.N = 1 + size(x.left) + size(x.right);
        return x;
    }


    public static void main(String[] args) { 
        BST<String, String> st = new BST<String, String>();
        st.put("www.cs.princeton.edu",   "128.112.136.11");
        st.put("www.princeton.edu",      "128.112.130.211");
        st.put("www.math.princeton.edu", "128.112.18.11");
   		...
		
        System.out.println(st.get("www.princeton.edu"));
    }
}
```