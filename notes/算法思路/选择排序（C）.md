# 选择排序（C）

首先找到最小元素置于起始位置，再从剩余元素中继续寻找最小者放到已排序序列末尾，依次类推（不稳定排序）

```c++
void SelectionSort(int arr[],int len){
  
  int i,j,min,tmp;

  for(i = 0; i < len; i++){
    min = i;
    for(j = i; j < len; j++){
      if(arr[j] < arr[min]){
        min = j;
      }
    }
    tmp = arr[i];
    arr[i] = arr[min];
    arr[min] = tmp;
  }
}
```