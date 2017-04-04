# 排序算法类模板（Java）

```java
public class Sort {
	@SuppressWarnings("unchecked")
	private static boolean less(Comparable v, Comparable w) { 
		return (v.compareTo(w) < 0);
	}
        
	private static void exch(Object[] a, int i, int j) {
		Object swap = a[i];
		a[i] = a[j];
		a[j] = swap;
	}       

	private static void show(Comparable[] a) {
		for (int i = 0; i < a.length; i++) {
			System.out.print(a[i]);
		}
	}

	public static void sort(Comparable[] a) {
		// 具体实现
	}
	
	public static void main(String[] args) {
		String[] a = 
{"E","F","G","A","B","Q","R","S","T","U","V","W","X","D","H","I","J","K","L","M","C","O","P","N","Y","Z","X","X"};
		Sort.sort(a);
		show(a);
	}
}	
```