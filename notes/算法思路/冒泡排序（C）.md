# 冒泡排序（C）

（稳定排序）

```c++
void BubbleSort(int arr[],int len){
  
  int i,j,tmp;

  for(i = 0; i < len; i++){
    for(j = i+1; j < len; j++){
      if(arr[j] < arr[i]){
        tmp = arr[j];
        arr[j] = arr[i];
        arr[i] =tmp;
      }
    }
  }
}
```