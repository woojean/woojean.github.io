# 归并排序（Java）

归并算法基于归并操作，即将两个有序的数组归并成一个更大的有序数组。要将一个数组排序，可以先递归地将它分成两半分别排序，然后将结果归并起来。
归并排序能够保证将任意长度为N的数组排序所需时间和NlogN成正比。其主要缺点是所需的额外空间和N成正比。

```java
// 原地归并方法：先将原数组整个拷贝，再合并回去
	public static void merge(Comparable[] a, Comparable[] aux, int lo, int mid, int hi) {
        for (int k = lo; k <= hi; k++) {
            aux[k] = a[k]; 
        }

        int i = lo, j = mid+1;
        for (int k = lo; k <= hi; k++) {
            if      (i > mid)              a[k] = aux[j++];
            else if (j > hi)               a[k] = aux[i++];
            else if (less(aux[j], aux[i])) a[k] = aux[j++];
            else                           a[k] = aux[i++];
        }
    }

	// 实际的递归归并操作
    private static void mergeSort(Comparable[] a, Comparable[] aux, int lo, int hi) {
        if (hi <= lo) 
			return;
        int mid = lo + (hi - lo) / 2;
        mergeSort(a, aux, lo, mid);
        mergeSort(a, aux, mid + 1, hi);
        merge(a, aux, lo, mid, hi);
    }

	// 归并排序接口
    public static void mergeSort(Comparable[] a) {
        Comparable[] aux = new Comparable[a.length];  // 交换空间
        mergeSort(a, aux, 0, a.length-1);
}
```