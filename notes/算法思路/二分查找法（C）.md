# 二分查找法（C）

```c++
int HalfSearch(int arr[], int low, int high, int num){
  int mid;
  mid = (low+high) / 2;
  if( (low>=high) && (arr[mid]!=num) ){
    return -1;
  }
  else{
    if( arr[mid]==num ){
      return mid;
    }
    else if( arr[mid]>num ){
      high = mid-1;
    }
    else{ 
      low = mid+1;
    }
    return HalfSearch(arr,low,high,num);
  }
}
```