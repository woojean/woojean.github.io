# 使用数组初始化vector对象

使用数组初始化vector对象，必须指出用于初始化式的第一个元素以及数组最后一个元素的下一位置的地址：

```C++
const size_t arr_size = 6;
int int_arr[arr_size] = {0, 1, 2, 3, 4, 5};
// ivec has 6 elements: each a copy of the corresponding element in int_arr
vector<int> ivec(int_arr, int_arr + arr_size);
```
被标出的元素范围可以是数组的子集：
```C++
vector<int> ivec(int_arr + 1, int_arr + 4);
```