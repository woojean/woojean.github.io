# define中为何经常会使用 do{...}while(0);来包装多条语句代码

do{...}while(0)的目的是为了在for循环和if语句时，避免出现下面的情况：

```c++
#define xxx  i++; i--;
for (I = 0 ; I < 10; I ++) xxx;
```
展开后变为
```c++
for ( I = 0 ; I < 10; I ++ ) I ++; I --;
```
(对define中do{}while(0)的理解)