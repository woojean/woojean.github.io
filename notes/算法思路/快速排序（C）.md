# 快速排序（C）

（不稳定排序）

```c++
void QuickSort(int a[],int low,int high){
  
  int i = low;
  int j = high;  
  int temp = a[i]; 
  
  if( low < high){          
    while(i < j)  // 若条件为i<=j，则将所有判定都加等号则会发生死循环
    {
      while((a[j] >= temp) && (i < j)){ 
        j--; 
      }
      a[i] = a[j];

      while((a[i] <= temp) && (i < j)){
        i++; 
      }  
      a[j]= a[i];  
    }
    a[i] = temp;
    QuickSort(a,low,i-1);
    QuickSort(a,j+1,high);
  }
}
```