# 希尔排序（Java）

希尔排序基于插入排序改进而来。对于大规模的乱序数组，插入排序很慢，因为它只会交换相邻的元素。希尔排序为了加快速度，使数组中任意间隔为h的元素都是有序的（h有序数组），即交换不相邻的元素以对数组的局部进行排序，并最终用插入排序（h最终为1）将局部有序数组排序。希尔排序需要选择一个递减序列，该序列最终收敛为1。（相当于在插入排序之外再加一层循环用来将h进行递减）

```java
public static void shellSort(Comparable[] a) {
        int N = a.length;

        // 3x+1 increment sequence:  1, 4, 13, 40, 121, 364, 1093, ... 
        int h = 1;
        while (h < N/3) 
h = 3*h + 1; 

        while (h >= 1) {
            // h-sort the array
            for (int i = h; i < N; i++) {
                for (int j = i; j >= h && less(a[j], a[j-h]); j -= h) {
                    exch(a, j, j-h);
                }
            }
            h /= 3;
        }
}
```
希尔排序时间复杂度与所选择的步长有关。