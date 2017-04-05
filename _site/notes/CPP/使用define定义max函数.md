# 使用define定义max函数

```c++
#define MAX(a,b)  (a)>(b)?(a): (b)
```
（对于define中()使用的把握）
例：
```
#define TEST1 a+b
#define TEST2 (a+b)
void main(void)
{
    int a, b, c, d;
    c = TEST1;       //相当于 c = a+b;
    d = TEST2;       //相当于 d = (a+b);
}
```
这样写是防止忽略运算符优先级而导致的错误。