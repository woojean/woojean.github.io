# 堆排序（C）

（不稳定排序）

```c++
#define LeftChild(i)  (2*(i) + 1)

//对数组A中以下标为i的元素作为根，大小为len的元素序列构成的堆进行堆调整，使该根节点放到合适的位置
void Sink(int arr[],int i,int len){
  int child,tmp;

  for(tmp = arr[i]; LeftChild(i)<len; i=child){
    child = LeftChild(i);

    if( child != len-1 && arr[child+1] > arr[child]){
      child++;
    }

    if( tmp < arr[child] ){
      arr[i] = arr[child];
    }
    else{
      break;
    }
  }
  arr[i] = tmp;
}

void HeapSort(int arr[],int len){
  int i,tmp;
  for( i = len/2; i >= 0; i-- ){  // BuildHeap从下往上建堆
    Sink( arr, i, len );
  }
  
  for( i = len - 1; i > 0; i-- ){ 
    /* DeleteMax */
    tmp = arr[0];
    arr[0] = arr[i];
    arr[i] = tmp;
    Sink( arr, 0, i );
  }

}
```