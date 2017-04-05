# ES6块级作用域

ES5只有全局作用域和函数作用域，没有块级作用域，这会造成很多不合理的场景，比如：

```
var tmp = new Date();

function f() {
  console.log(tmp);
  if (false) {
    var tmp = "hello world";  // 变量提升，会覆盖外层tmp变量
  }
}

f(); // undefined

```
let为JavaScript新增了块级作用域，使得获得广泛应用的立即执行匿名函数（IIFE）不再必要了。

ES5标准规定不允许在块级作用域中声明函数，但是很多浏览器没有遵守这个规定。ES6明确允许在块级作用域中声明函数：
```
// ES6严格模式
'use strict';
if (true) {
  function f() {}
}
// 不报错
```
并且ES6规定，块级作用域之中，函数声明语句的行为类似于let，在块级作用域之外不可引用。考虑到环境导致的行为差异太大，应该避免在块级作用域内声明函数。如果确实需要，也应该写成函数表达式，而不是函数声明语句：
```
// 函数声明语句
{
  let a = 'secret';
  function f() {
    return a;
  }
}

// 函数表达式
{
  let a = 'secret';
  let f = function () {
    return a;
  };
}
```