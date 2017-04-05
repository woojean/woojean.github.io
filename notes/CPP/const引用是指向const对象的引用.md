# const引用是指向const对象的引用

const对象的引用只能是const引用，但是const引用也可用于非const对象。

```C++
const int ival = 1024;
const int &refVal = ival;      // ok: both reference and object are const
int &ref2 = ival;            // error: 将普通的引用绑定到 const 对象是不合法的
```
可以读取但不能修改refVal ，因此，任何对 refVal 的赋值都是不合法的。这个限制有其意义：不能直接对 ival 赋值，因此不能通过使用 refVal 来修改 ival。
const 引用可以初始化为不同类型的对象或者初始化为右值，如字面值常量：
```C++
int i = 42;
//  legal for const references only
const int &r = 42;
const int &r2 = r + i;
```