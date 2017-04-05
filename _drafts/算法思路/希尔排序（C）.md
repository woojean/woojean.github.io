# 希尔排序（C）

先取一个正整数d1 < N，把所有相隔d1的元素放一组，共d1组，组内进行直接插入排序，再取d2 < d1，重复上述步骤，直至d=1.`只要最终步长为1，任何步长序列都可以`，当步长为1时，算法即为插入排序。（不稳定排序）

```c++
void ShellSort(int arr[],int len){
  
  int i,j,incr,tmp;
  
  // 14,7,3,1
  for(incr = len/2; incr > 0; incr /= 2){
    for(i = incr; i < len; i++){
      tmp = arr[i];
      for(j = i; j >= incr; j -= incr){
        if(tmp < arr[j-incr]){
          arr[j] = arr[j-incr];
        }
        else{
          break;
        }
      }
      arr[j] = tmp;
    }
  }
}
```