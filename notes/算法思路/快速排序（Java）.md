# 快速排序（Java）

快速排序将长度为N的数组排序所需的时间和NlogN成正比，且所需空间小于归并排序。
它将一个数组分成两个子数组，并将两部分独立地排序，该方法的关键在于切分：对于一个选定的元素，每次切分后使得该元素处于最终的位置，即其左边的元素都不大于它，而右边的元素都不小于它。

```java
// 快速排序的切分操作
private static int partition(Comparable[] a, int lo, int hi) {
        int i = lo;
        int j = hi + 1;
        Comparable v = a[lo]; 	// 选定第一个元素为切分基准
        while (true) { 
            while (less(a[++i], v))
                if (i == hi) break;
            while (less(v, a[--j]))
                if (j == lo) break;
            if (i >= j) 
				break;
            exch(a, i, j);
        }
        exch(a, lo, j);
        return j;
    }
 
// 快速排序的实际递归操作
    private static void quickSort(Comparable[] a, int lo, int hi) { 
        if (hi <= lo) 
			return;
        int j = partition(a, lo, hi);
        quickSort(a, lo, j-1);
        quickSort(a, j+1, hi);
    }
	
// 快速排序的接口
	public static void quickSort(Comparable[] a) {
        quickSort(a, 0, a.length - 1);
    }
```