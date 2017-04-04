# ES6对函数的扩展

## 函数参数的默认值
```
function log(x, y = 'World') {
  console.log(x, y);
}

log('Hello') // Hello World
log('Hello', 'China') // Hello China
log('Hello', '') // Hello
```

如果传入undefined，将触发参数等于默认值，null则没有这个效果:
```
function foo(x = 5, y = 6) {
  console.log(x, y);
}

foo(undefined, null)
// 5 null
```

## 函数的length属性
指定了默认值以后，函数的length属性，将返回没有指定默认值的参数个数。
```
(function (a) {}).length // 1
(function (a = 5) {}).length // 0
(function (a, b, c = 5) {}).length // 2
```

## rest参数
ES6引入rest参数（形式为“...变量名”），用于获取函数的多余参数。rest参数搭配的变量是一个数组，该变量将多余的参数放入数组中。
```
function add(...values) {
  let sum = 0;

  for (var val of values) {
    sum += val;
  }

  return sum;
}

add(2, 5, 3) // 10
```
rest参数之后不能再有其他参数（即只能是最后一个参数），否则会报错。
函数的length属性，不包括rest参数。

## 扩展运算符
将一个数组转为用逗号分隔的参数序列。
```
console.log(...[1, 2, 3])
// 1 2 3

console.log(1, ...[2, 3, 4], 5)
// 1 2 3 4 5

[...document.querySelectorAll('div')]
// [<div>, <div>, <div>]

[...'hello']
// [ "h", "e", "l", "l", "o" ]
```
`扩展运算符内部调用的是数据结构的Iterator接口`，因此只要具有Iterator接口的对象，都可以使用扩展运算符。

由于扩展运算符可以展开数组，所以不再需要apply方法，将数组转为函数的参数了：
```

function f(x, y, z) {
  // ...
}
f.apply(null, args); // ES5的写法
f(...args); // ES6的写法
```

## name属性
返回该函数的函数名：
```
function foo() {}
foo.name // "foo"
```
如果将一个匿名函数赋值给一个变量，ES5的name属性，会返回空字符串，而ES6的name属性会返回实际的函数名：
```
var func1 = function () {};

// ES5
func1.name // ""

// ES6
func1.name // "func1"
```
如果将一个具名函数赋值给一个变量，则ES5和ES6的name属性都返回这个具名函数原本的名字。

Function构造函数返回的函数实例，name属性的值为“anonymous”。
```
(new Function).name // "anonymous"
```
bind返回的函数，name属性值会加上“bound ”前缀。
```
function foo() {};
foo.bind({}).name // "bound foo"

(function(){}).bind({}).name // "bound "
```

## 箭头函数
```
var f = v => v;
var f = () => 5;
var sum = (num1, num2) => num1 + num2;
var sum = (num1, num2) => { return num1 + num2; }
```

由于大括号被解释为代码块，所以如果箭头函数直接返回一个对象，必须在对象外面加上括号：
```
var getTempItem = id => ({ id: id, name: "Temp" });
```

箭头函数有几个使用注意点：
1.函数体内的this对象，就是定义时所在的对象，而不是使用时所在的对象。
```
function foo() {
  setTimeout(() => {
    console.log('id:', this.id);
  }, 100);
}

var id = 21;

foo.call({ id: 42 });
// id: 42
```
2.不可以当作构造函数，也就是说，不可以使用new命令，否则会抛出一个错误。
3.不可以使用arguments对象，该对象在函数体内不存在。如果要用，可以用Rest参数代替。
4.不可以使用yield命令，因此箭头函数不能用作Generator函数。

箭头函数中this指向的固定化，并不是因为箭头函数内部有绑定this的机制，实际原因是箭头函数根本没有自己的this，导致内部的this就是外层代码块的this。正是因为它没有this，所以也就不能用作构造函数。箭头函数转成ES5的代码如下：
```
// ES6
function foo() {
  setTimeout(() => {
    console.log('id:', this.id);
  }, 100);
}

// ES5
function foo() {
  var _this = this;

  setTimeout(function () {
    console.log('id:', _this.id);
  }, 100);
}
```
除了this，以下三个变量在箭头函数之中也是不存在的，指向外层函数的对应变量：arguments、super、new.target。
```
function foo() {
  setTimeout(() => {
    console.log('args:', arguments);
  }, 100);
}

foo(2, 4, 6, 8)
// args: [2, 4, 6, 8]
```
由于箭头函数没有自己的this，所以当然也就不能用call()、apply()、bind()这些方法去改变this的指向。


## 函数绑定
ES7提出了“函数绑定”（function bind）运算符，用来取代call、apply、bind调用。函数绑定运算符是并排的两个双冒号（::），双冒号左边是一个对象，右边是一个函数。该运算符会自动将左边的对象，作为上下文环境（即this对象），绑定到右边的函数上面。
```
foo::bar;
// 等同于
bar.bind(foo);

foo::bar(...arguments);
// 等同于
bar.apply(foo, arguments);

const hasOwnProperty = Object.prototype.hasOwnProperty;
function hasOwn(obj, key) {
  return obj::hasOwnProperty(key);
}
```

## 尾调用优化
尾调用（Tail Call）指某个函数的最后一步操作是返回另一个函数的调用。
```
function f(x){
  return g(x);
}
```

不属于尾调用的情况：
```
// 情况一
function f(x){
  let y = g(x);  // 调用后还有操作
  return y;
}

// 情况二
function f(x){
  return g(x) + 1;  // 调用后还有操作
}

// 情况三
function f(x){
  g(x);  // 相当于return undefined
}
```

`尾调用优化`：函数调用会在内存形成一个“调用记录”，又称“调用帧”（call frame），保存调用位置和内部变量等信息。如果在函数A的内部调用函数B，那么在A的调用帧上方，还会形成一个B的调用帧。等到B运行结束，将结果返回到A，B的调用帧才会消失。所有的调用帧，就形成一个“调用栈”（call stack）。尾调用由于是函数的最后一步操作，所以不需要保留外层函数的调用帧，因为调用位置、内部变量等信息都不会再用到了，只要直接用内层函数的调用帧，取代外层函数的调用帧就可以了。
```
function f() {
  let m = 1;
  let n = 2;
  return g(m + n);
}
f();

// 等同于
function f() {
  return g(3);
}
f();

// 等同于
g(3);  // 由于调用g之后，函数f就结束了，所以执行到最后一步，完全可以删除 f(x) 的调用帧，只保留 g(3) 的调用帧。
```
“尾调用优化”（Tail call optimization），即只保留内层函数的调用帧。如果所有函数都是尾调用，那么完全可以做到每次执行时，调用帧只有一项，这将大大节省内存。这就是“尾调用优化”的意义。


尾递归：如果尾调用自身，就称为尾递归。递归非常耗费内存，因为需要同时保存成千上百个调用帧，很容易发生“栈溢出”错误（stack overflow）。但对于尾递归来说，由于只存在一个调用帧，所以永远不会发生“栈溢出”错误。
尾递归的实现，往往需要改写递归函数，确保最后一步只调用自身，做到这一点的方法，就是把所有用到的内部变量改写成函数的参数。：
```
// 阶乘函数，计算n的阶乘，最多需要保存n个调用记录，复杂度 O(n) 
function factorial(n) {
  if (n === 1) return 1;
  return n * factorial(n - 1);  // 不属于尾调用
}

factorial(5) // 120
```
阶乘函数 factorial 需要用到一个中间变量 total ，那就把这个中间变量改写成函数的参数，改写成尾递归：
```
// 复杂度 O(1)
function factorial(n, total) {
  if (n === 1) return total;
  return factorial(n - 1, n * total);
}

factorial(5, 1) // 120
```
“尾调用优化”对递归操作意义重大，所以一些函数式编程语言将其写入了语言规格。ES6也是如此，第一次明确规定，所有ECMAScript的实现，都必须部署“尾调用优化”。这就是说，`在ES6中，只要使用尾递归，就不会发生栈溢出，相对节省内存`。

ES6的尾调用优化只在严格模式下开启，正常模式是无效的。这是因为在正常模式下，函数内部有两个变量，可以跟踪函数的调用栈:func.arguments、func.caller尾调用优化发生时，函数的调用栈会改写，因此上面两个变量就会失真。严格模式禁用这两个变量，所以尾调用模式仅在严格模式下生效。

## 函数参数的尾逗号
ES7有一个提案，允许函数的最后一个参数有尾逗号（trailing comma）,这样，当以后需要再添加新参数时，这一行代码在版本控制中不会有改变：
```
function clownsEverywhere(
  param1,
  param2,
) { /* ... */ }
```