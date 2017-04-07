# 求最大的子序 列和的联机算法（C）

```c++
int  MaxSubSequenceSum(const int arr[],int len){
  int  tmpSum, maxSum, j;
  tmpSum = maxSum = 0;
  
  for( j=0; j<len; j++ ){
    tmpSum += arr[j];
    if(tmpSum > maxSum){
      maxSum = tmpSum;
    }
    else if(tmpSum < 0){
      tmpSum = 0;
    }
  }
  return maxSum;
}
```