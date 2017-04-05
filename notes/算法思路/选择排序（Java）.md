# 选择排序（Java）

首先找到数组中最小的那个元素，然后将它和数组的第一个元素交换位置（如果第一个元素就是最小元素，那么它就和自己交换位置）。在剩下的元素中找到最小的元素，将它与数组的第二个元素交换位置，如此往复，直到将整个数组排序。

```java
public static void selectionSort(Comparable[] a) {
		int N = a.length;
		for (int i = 0; i < N; i++) {
			int min = i;
			for (int j = i+1; j < N; j++) {
				if (less(a[j], a[min])) min = j;
			}
			exch(a, i, min);
		}
	}
```