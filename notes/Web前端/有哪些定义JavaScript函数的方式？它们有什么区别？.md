# 有哪些定义JavaScript函数的方式？它们有什么区别？

函数实际上是对象。每个函数都是 Function 类型的实例，而且都与其他引用类型一样具有属性和方法。由于函
数是对象，因此函数名实际上也是一个指向函数对象的指针，不会与某个函数绑定：

```
function sum (num1, num2) {
  return num1 + num2;
}
```
这与下面使用函数表达式定义函数的方式几乎相差无几（注意函数末尾有一个分号）
```
// 在使用函数表达式定义函数时，没有必要使用函数名
var sum = function(num1, num2){
  return num1 + num2;
};
```
也可以使用 Function 构造函数来定义函数：
var sum = new Function("num1", "num2", "return num1 + num2"); // 不推荐
不推荐使用这种方法定义函数，因为这种语法会导致解析两次代码（第一次是解析常规 ECMAScript代码，第二次是解析传入构造函数中的字符串），从而影响性能。

解析器在向执行环境中加载数据时，对函数声明和函数表达式并非一视同仁。解析器会率先读取函数声明，并使其在执行任何代码之前可用（可以访问）；至于函数表达式，则必须等到解析器执行到它所在的代码行，才会真
正被解释执行
```
alert(sum(10,10));	// OK
function sum(num1, num2){
  return num1 + num2;
}
```
在代码开始执行之前，解析器就已经通过一个名为函数声明提升（function declaration hoisting）的过程，读取并将函数声明添加到执行环境中。
```
alert(sum(10,10)); // FAILED
  var sum = function(num1, num2){
  return num1 + num2;
};
```