# 插入排序（C）

对于未排序数据在已排序序列中从后向前扫描，找到相应位置并插入（稳定排序）

```c++
void InsertionSort(int arr[],int len){
  
  int i,j,tmp;

  for(i = 1; i < len; i++){
    tmp = arr[i];
    for(j = i; j>0 && arr[j-1]>tmp; j--){
      arr[j] = arr[j-1];
      arr[j-1] = tmp;
    }
  }
}
```