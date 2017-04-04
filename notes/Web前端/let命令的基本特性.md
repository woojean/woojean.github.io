# let命令的基本特性

```
// 使用let声明的变量只在let命令所在的代码块内有效
{
  let a = 10;
  var b = 1;
}

a // ReferenceError: a is not defined.
b // 1
```


```
// 使用let声明的变量不会“变量提升”，变量一定要在声明后使用
console.log(foo); // 输出undefined
console.log(bar); // 报错ReferenceError

var foo = 2;  // 会变量提升，即脚本开始运行时变量foo就已存在，但没有值
let bar = 2;
```

```
// 只要块级作用域内存在let命令，它所声明的变量就“绑定”（binding）这个区域，不再受外部的影响（const同理）
var tmp = 123;

if (true) {
  tmp = 'abc'; // ReferenceError （暂时性死区）
  let tmp;
}
```

```
// let不允许在相同作用域内，重复声明同一个变量
function () {
  let a = 10;
  var a = 1;  // 报错
  let a = 1;  // 报错
}
```
