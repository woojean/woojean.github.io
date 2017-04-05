# 冒泡排序（Java）

```java
public static void bubbleSort(Comparable[] a) {
        int N = a.length;
		for(int i =0; i<N-1; i++){
			for(int j =0;j<N-1-i;j++){
				if(less(a[j+1],a[j])){
					exch(a,j,j+1);
				}
			}
		}
}
```