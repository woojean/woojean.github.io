# 单链表相关操作（Java）

```java
public class LinkList<Key, Value> {
	private int N; // 链表长度
    private Node first; // 头结点

	// 链表结点
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

	// 搜索
    public Value get(Key key) {
        for (Node x = first; x != null; x = x.next) {
            if (key.equals(x.key)){ 
				return x.val;
			}
        }
        return null;
    }
	
	// 添加新结点
    public void put(Key key, Value val) {
        for ( Node x = first; x != null; x = x.next ){
            if ( key.equals(x.key) ) { 
				x.val = val; 
				return; 
			}
		}
        first = new Node(key, val, first);
    }
	
	// 删除结点
    public void delete(Key key) {
        first = delete(first, key);
    }

	// 递归删除结点
    private Node delete(Node x, Key key) {
        if (x == null) 
			return null;
        if ( key.equals(x.key) ){ 
			N--; 
			return x.next; 
		}
        x.next = delete(x.next, key);
        return x;
    }
	
	// 反转链表
	public void reverse(){
		Node prev = null;
		Node curr = first;
		while(curr!=null){
			Node next = curr.next;
			// next.next = curr;
			curr.next = prev;
			prev = curr;
			curr = next;
		}
		first = prev;
	}
	
	// 打印链表
	public void show(){
		for (Node x = first; x != null; x = x.next){
            System.out.print(x.key+"["+x.val+"] ");
		}
		System.out.println();
	}
	
	public static void main(String[] args) { 
		// 初始化、新增结点
		LinkList<String, String> st = new LinkList<String, String>();
        st.put("A","1");
        st.put("B","2");
		st.put("C","3");
		st.put("D","4");
		st.put("E","5");
		st.put("F","6");
		st.put("G","7");
		st.put("H","8");
		st.put("I","9");
		st.put("J","10");
		st.show();
		
		// 查找
		System.out.println(st.get("I"));
		
		// 删除
		String delKey = "I";
		st.delete(delKey);
		st.show();
		
		// 反转
		st.reverse();
		st.show();
		
	}
}
```