# 计数排序（C）

（稳定排序）

```c++
void CountingSort(int arr[],int len){
  
  int i, min, max;
  min = max = arr[0];

  // 找出范围
  for(i = 1; i < len; i++) {
    if (arr[i] < min)
      min = arr[i];
    else if (arr[i] > max)
      max = arr[i];
  }
  int range = max - min + 1;
  
  int *count = (int*)malloc(range * sizeof(int));
  for(i = 0; i < range; i++){
    count[i] = 0;
  }
  for(i = 0; i < len; i++){
    count[ arr[i] - min ]++;
  }
  
  int j, z = 0;
  for(i = min; i <= max; i++){
    for(j = 0; j < count[ i - min ]; j++){
      arr[z++] = i;
    }
  }
  free(count);
}
```