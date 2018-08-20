---
layout: post
title:  "《ECMAScript 6入门》读书笔记"
date: 2017-04-05 00:03:00
categories: 编程
tags: JavaScript
excerpt: ""
---

* content
{:toc}

## 第1章 ECMAScript 6简介

### Babel转码器
#### .babelrc配置文件
放在项目根目录下，用来配置转码规则和插件，如：
```javascript
{
  "presets": [
    "es2015",
    "react",
    "stage-2"
    ], 
  "plugins": []
}
```
转码规则需要单独安装，如：
$ npm install --save-dev babel-preset-es2015

#### babel-cli
用于命令行执行转码，如：
$ babel example.js --out-file compiled.js
$ babel src --out-dir lib

#### babel-node
是babel-cli自带的一个命令，无需单独安装，可以直接运行ES6代码：
$ babel-node es6.js  # 替代node

#### babel-register
该模块会改写require命令，每当使用require加载.js、.jsx、.es和.es6后缀名的文件，就会先用Babel进行转码：
require("babel-register");
require("./index.js");

#### babel-core
babel-core提供babel的API，有些代码需要通过调用Babel的API才能进行转码，如：
```javascript
var es6Code = 'let x = n => n + 1';
var es5Code = require('babel-core')
  .transform(es6Code, {
    presets: ['es2015']
  })
  .code;
```

#### babel-polyfill
Babel默认只转换新的JavaScript句法（syntax），而不转换新的API，比如Iterator、Generator、Set、Maps、Proxy、Reflect、Symbol、Promise等全局对象，以及一些定义在全局对象上的方法（比如Object.assign）都不会转码。如果也想转换这些对象，需要使用babel-polyfill：
import 'babel-polyfill';
// 或者
require('babel-polyfill');

#### babel用于浏览器环境
```javascript
<script src="node_modules/babel-core/browser.js"></script>
<script type="text/babel">
// ES6 code
</script>
```
直接在浏览器中进行转码性能太差，可以配合browserify在服务器端把代码转换为浏览器可以直接执行的代码：
$ npm install --save-dev babelify babel-preset-es2015
$ browserify script.js -o bundle.js -t [ babelify --presets [ es2015 ] ]

可以在package.json中进行配置，这样就不用每次都在命令行输入参数了：
```javascript
{
  "browserify": {
    "transform": [["babelify", { "presets": ["es2015"] }]]
  }
}
```

### Traceur
（略）


## 第2章 let和const命令

### let
```javascript
// 使用let声明的变量只在let命令所在的代码块内有效
{
  let a = 10;
  var b = 1;
}

a // ReferenceError: a is not defined.
b // 1
```


```javascript
// 使用let声明的变量不会“变量提升”，变量一定要在声明后使用
console.log(foo); // 输出undefined
console.log(bar); // 报错ReferenceError

var foo = 2;  // 会变量提升，即脚本开始运行时变量foo就已存在，但没有值
let bar = 2;
```

```javascript
// 只要块级作用域内存在let命令，它所声明的变量就“绑定”（binding）这个区域，不再受外部的影响（const同理）
var tmp = 123;

if (true) {
  tmp = 'abc'; // ReferenceError （暂时性死区）
  let tmp;
}
```

```javascript
// let不允许在相同作用域内，重复声明同一个变量
function () {
  let a = 10;
  var a = 1;  // 报错
  let a = 1;  // 报错
}
```

### 块级作用域
ES5只有全局作用域和函数作用域，没有块级作用域，这会造成很多不合理的场景，比如：
```javascript
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
```javascript
// ES6严格模式
'use strict';
if (true) {
  function f() {}
}
// 不报错
```
并且ES6规定，块级作用域之中，函数声明语句的行为类似于let，在块级作用域之外不可引用。考虑到环境导致的行为差异太大，应该避免在块级作用域内声明函数。如果确实需要，也应该写成函数表达式，而不是函数声明语句：
```javascript
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

### const
const声明的变量不得改变值，一旦声明，就必须立即初始化：
```javascript
const foo;
// SyntaxError: Missing initializer in const declaration
```

对于复合类型的变量，变量名不指向数据，而是指向数据所在的地址。const命令只是保证变量名指向的地址不变，并不保证该地址的数据不变：
```javascript
const foo = {};
foo.prop = 123;

foo.prop
// 123

foo = {}; // TypeError: "foo" is read-only
```
如果真的想将对象冻结，应该使用`Object.freeze`方法：
```javascript
// 彻底冻结一个对象（包括对象的属性）
var constantize = (obj) => {
  Object.freeze(obj);
  Object.keys(obj).forEach( (key, value) => {
    if ( typeof obj[key] === 'object' ) {
      constantize( obj[key] );
    }
  });
};
```

`ES6一共有6种声明变量的方法`：var、function、let、const、import、class


### 全局对象的属性
全局对象是最顶层的对象，在浏览器环境指的是window对象，在Node.js指的是global对象。
ES5之中，全局对象的属性与全局变量是等价的，未声明的全局变量，自动成为全局对象window的属性。
从ES6开始，全局变量将逐步与全局对象的属性脱钩，let命令、const命令、class命令声明的全局变量，不属于全局对象的属性：
```javascript
var a = 1;
// 如果在Node的REPL环境，可以写成global.a
// 或者采用通用方法，写成this.a
window.a // 1

let b = 1;
window.b // undefined
```


## 第3章 变量的解构赋值

### 数组的解构赋值
```javascript
var [a, b, c] = [1, 2, 3];

let [foo, [[bar], baz]] = [1, [[2], 3]];
foo // 1
bar // 2
baz // 3

let [ , , third] = ["foo", "bar", "baz"];
third // "baz"

let [x, , y] = [1, 2, 3];
x // 1
y // 3

let [head, ...tail] = [1, 2, 3, 4];
head // 1
tail // [2, 3, 4]

let [x, y, ...z] = ['a'];
x // "a"
y // undefined
z // []


// 支持不完全结构
let [x, y] = [1, 2, 3];
x // 1
y // 2

let [a, [b], d] = [1, [2, 3], 4];
a // 1
b // 2
d // 4

// 允许指定默认值
var [foo = true] = [];
foo // true

[x, y = 'b'] = ['a']; // x='a', y='b'
[x, y = 'b'] = ['a', undefined]; // x='a', y='b'

// ES6内部使用严格相等运算符（===），判断一个位置是否有值
var [x = 1] = [undefined];
x // 1

var [x = 1] = [null];
x // null

```

如果等号的右边不是`可遍历`的结构，那么将会报错:
```javascript
// 报错
let [foo] = 1;
let [foo] = false;
let [foo] = NaN;
let [foo] = undefined;
let [foo] = null;
let [foo] = {};
```

如果默认值是一个表达式，那么这个表达式是惰性求值的：
```javascript
// 因为x能取到值，所以函数f根本不会执行
function f() {
  console.log('aaa');
}

let [x = f()] = [1];
```

### 对象的解构赋值
对象的属性没有次序，变量必须与属性同名，才能取到正确的值：
```javascript
var { bar, foo } = { foo: "aaa", bar: "bbb" };
foo // "aaa"
bar // "bbb"

var { baz } = { foo: "aaa", bar: "bbb" };
baz // undefined
```

对象的解构赋值是下面形式的简写：
```javascript
var { foo: foo, bar: bar } = { foo: "aaa", bar: "bbb" };
```
即对象的解构赋值的内部机制，是先找到同名属性，然后再赋给对应的变量。真正被赋值的是后者，而不是前者。因此，如果变量名与属性名不一致，可以写成下面这样：
```javascript
var { foo: baz } = { foo: 'aaa', bar: 'bbb' };
baz // "aaa"

let obj = { first: 'hello', last: 'world' };
let { first: f, last: l } = obj;
f // 'hello'
l // 'world'
```

解构也可以用于嵌套结构的对象：
```javascript
var node = {
  loc: {
    start: {
      line: 1,
      column: 5
    }
  }
};

var { loc: { start: { line }} } = node;
line // 1
loc  // error: loc is undefined
start // error: start is undefined
```
只有line是变量，loc和start都是模式，不会被赋值。

如果要将一个已经声明的变量用于解构赋值，必须使用()：
```javascript
var x;
{x} = {x: 1};
// SyntaxError: syntax error
```
上面代码的写法会报错，因为JavaScript引擎会将{x}理解成一个代码块，从而发生语法错误。`只有不将大括号写在行首，避免JavaScript将其解释为代码块`，才能解决这个问题。
```javascript
({x} = {x: 1});
```

### 字符串的解构赋值
```javascript
const [a, b, c, d, e] = 'hello';
a // "h"
b // "e"
c // "l"
d // "l"
e // "o"

let {length : len} = 'hello';
len // 5
```

### 数值和布尔值的解构赋值
如果等号右边是数值和布尔值，则会先转为对象
```javascript
let {toString: s} = 123;
s === Number.prototype.toString // true

let {toString: s} = true;
s === Boolean.prototype.toString // true
```

解构赋值的规则是，只要等号右边的值不是对象，就先将其转为对象。`由于undefined和null无法转为对象，所以对它们进行解构赋值，都会报错`。
```javascript
let { prop: x } = undefined; // TypeError
let { prop: y } = null; // TypeError
```

### 函数参数的解构赋值
```javascript
function add([x, y]){
  return x + y;
}

add([1, 2]); // 3

[[1, 2], [3, 4]].map(([a, b]) => a + b);
// [ 3, 7 ]
```

undefined就会触发函数参数的默认值:
```javascript
[1, undefined, 3].map((x = 'yes') => x);
// [ 1, 'yes', 3 ]
```


### 圆括号问题
建议只要有可能，就不要在模式中放置圆括号。
（不要自找麻烦）

### 用途
遍历Map结构:
```javascript
for (let [key, value] of map) {
  console.log(key + " is " + value);
}

for (let [key] of map) {
  // ...
}

for (let [,value] of map) {
  // ...
}
```

输入模块的指定方法：
```javascript
const { SourceMapConsumer, SourceNode } = require("source-map");
```

## 第4章 字符串的扩展
JavaScript`允许采用\uxxxx形式表示一个字符`，其中“xxxx”表示字符的`码点`。但是，这种表示法只限于\u0000——\uFFFF之间的字符。超出这个范围的字符，必须用两个双字节的形式表达。
```javascript
"\uD842\uDFB7"
// "𠮷"

"\u20BB7"  // 超过0xFFFF，ES5会理解成“\u20BB+7”，\u20BB是一个不可打印字符，所以只会显示一个空格
// " 7"
```

ES6对Unicode字符的表示做出了改进，只要将码点放入大括号，就能正确解读4字节字符：
```javascript
"\u{20BB7}"
// "𠮷"
```

因此ES6共有6种方法可以表示一个字符：
```javascript
'\z' === 'z'  // true
'\172' === 'z' // true
'\x7A' === 'z' // true
'\u007A' === 'z' // true
'\u{7A}' === 'z' // true
```


### 字符串的遍历器接口
ES6为字符串添加了遍历器接口，使得字符串可以被for...of循环遍历：
```javascript
for (let codePoint of 'foo') {
  console.log(codePoint)
}
// "f"
// "o"
// "o"
```

### 模板字符串
模板字符串（template string）是增强版的字符串，用反引号（`）标识。它可以当作普通字符串使用，也可以用来定义多行字符串，或者在字符串中嵌入变量。
```javascript
// 普通字符串
`In JavaScript '\n' is a line-feed.`

// 多行字符串
`In JavaScript this is
 not legal.`

console.log(`string text line 1
string text line 2`);

// 字符串中嵌入变量
var name = "Bob", time = "today";
`Hello ${name}, how are you ${time}?`
```


### 标签模板
模板字符串可以紧跟在一个函数名后面，该函数将被调用来处理这个模板字符串：
```javascript
alert`123`
// 等同于
alert(123)
```
标签模板其实不是模板，而是函数调用的一种特殊形式。
如果模板字符里面有变量，会将模板字符串先处理成多个参数，再调用函数：
```javascript
var a = 5;
var b = 10;

tag`Hello ${ a + b } world ${ a * b }`;
// 等同于
tag(['Hello ', ' world ', ''], 15, 50);
```

### API
codePointAt()  // 能够正确处理4个字节储存的字符，返回一个字符的码点
String.fromCodePoint()  // 用于从码点返回对应字符
at()  // 可以识别Unicode编号大于0xFFFF的字符，返回正确的字符
normalize()  // 用来将字符的不同表示方法统一为同样的形式，这称为Unicode正规化（字符合成）
includes()  // 返回布尔值，表示是否找到了参数字符串。
startsWith()  // 返回布尔值，表示参数字符串是否在源字符串的头部。
endsWith()  // 返回布尔值，表示参数字符串是否在源字符串的尾部。
repeat() // 返回一个新字符串，表示将原字符串重复n次
padStart() // 头部补全
padEnd() // 尾部补全
String.raw()  // 往往用来充当模板字符串的处理函数，返回一个斜杠都被转义（即斜杠前面再加一个斜杠）的字符串，对应于替换变量后的模板字符串。


## 第5章 正则的扩展

### 构造函数的改变
ES6改变了ES5中RegExp的构造函数的行为：构造函数第一个参数是一个正则对象，那么可以使用第二个参数指定修饰符。而且，返回的正则表达式会忽略原有的正则表达式的修饰符，只使用新指定的修饰符。
```javascript
new RegExp(/abc/ig, 'i').flags
// "i"
```

### 字符串的正则方法
字符串对象共有4个方法，可以使用正则表达式：match()、replace()、search()和split()。


### u修饰符
ES6对正则表达式添加了u修饰符，用来正确处理大于\uFFFF的Unicode字符
```javascript
/^\uD83D/u.test('\uD83D\uDC2A')
// false
/^\uD83D/.test('\uD83D\uDC2A')
// true
```

### y修饰符
ES6为正则表达式添加了y修饰符，叫做“粘连”（sticky）修饰符，y修饰符的作用与g修饰符类似，也是全局匹配，后一次匹配都从上一次匹配成功的下一个位置开始。不同之处在于，g修饰符只要剩余位置中存在匹配就可，而y修饰符确保匹配必须从剩余的第一个位置开始，这也就是“粘连”的涵义：
```javascript
var s = 'aaa_aa_a';
var r1 = /a+/g;
var r2 = /a+/y;

r1.exec(s) // ["aaa"]
r2.exec(s) // ["aaa"]

r1.exec(s) // ["aa"]
r2.exec(s) // null
```

### sticky属性 
ES6的正则对象多了sticky属性，表示是否设置了y修饰符

### flags属性
新增了flags属性，会返回正则表达式的修饰符
```javascript
// ES5的source属性
// 返回正则表达式的正文
/abc/ig.source
// "abc"

// ES6的flags属性
// 返回正则表达式的修饰符
/abc/ig.flags
// 'gi'
```

### RegExp.escape()
ES7，（略）

### 后行断言
ES7，（略）



## 第6章 数值的扩展

### 二进制和八进制表示法
ES6提供了二进制和八进制数值的新的写法，分别用前缀0b（或0B）和0o（或0O）表示：
```javascript
0b111110111 === 503 // true
0o767 === 503 // true
```

### Number.isFinite()
用来检查一个数值是否为有限的（finite）：
```javascript
Number.isFinite(15); // true
Number.isFinite(0.8); // true
Number.isFinite(NaN); // false
Number.isFinite(Infinity); // false
Number.isFinite(-Infinity); // false
Number.isFinite('foo'); // false
Number.isFinite('15'); // false
Number.isFinite(true); // false
```

### Number.isNaN()
用来检查一个值是否为NaN：
```javascript
Number.isNaN(NaN) // true
Number.isNaN(15) // false
Number.isNaN('15') // false
Number.isNaN(true) // false
Number.isNaN(9/NaN) // true
Number.isNaN('true'/0) // true
Number.isNaN('true'/'true') // true
```

### Number.parseInt(), Number.parseFloat()
ES6将全局方法parseInt()和parseFloat()，移植到Number对象上面，行为完全保持不变。
```javascript
Number.parseInt === parseInt // true
Number.parseFloat === parseFloat // true
```

### Number.isInteger()
用来判断一个值是否为整数，在JavaScript内部，整数和浮点数是同样的储存方法，所以3和3.0被视为同一个值：
```javascript
Number.isInteger(25) // true
Number.isInteger(25.0) // true
Number.isInteger(25.1) // false
Number.isInteger("15") // false
Number.isInteger(true) // false
```

### Number.EPSILON
新增的一个极小的常量：
```javascript
Number.EPSILON
// 2.220446049250313e-16
Number.EPSILON.toFixed(20)
// '0.00000000000000022204'
```

### Number.isSafeInteger()
JavaScript能够准确表示的整数范围在-2^53到2^53之间（不含两个端点），超过这个范围，无法精确表示这个值。ES6引入了Number.MAX_SAFE_INTEGER和Number.MIN_SAFE_INTEGER这两个常量，用来表示这个范围的上下限。Number.isSafeInteger()则是用来判断一个整数是否落在这个范围之内。
实际使用这个函数时，需要注意。验证运算结果是否落在安全整数的范围内，不要只验证运算结果，而要同时验证参与运算的每个值。
```javascript
Number.isSafeInteger(9007199254740993)
// false
Number.isSafeInteger(990)
// true
Number.isSafeInteger(9007199254740993 - 990)
// true
9007199254740993 - 990
// 返回结果 9007199254740002
// 正确答案应该是 9007199254740003
```

### Math对象的扩展
Math.trunc()  // 用于去除一个数的小数部分，返回整数部分
Math.sign()  // 用来判断一个数到底是正数、负数、还是零
Math.cbrt()  // 用于计算一个数的立方根
Math.clz32()  // 返回一个数的32位无符号整数形式有多少个前导0
Math.imul()  // 返回两个数以32位带符号整数形式相乘的结果，返回的也是一个32位的带符号整数
Math.fround()  // 返回一个数的单精度浮点数形式
Math.hypot()  // 返回所有参数的平方和的平方根
（新增若干对数、指数、三角函数方法，略）

### 指数运算符
ES7新增了一个指数运算符（**）
```javascript
2 ** 2 // 4
2 ** 3 // 8
```


## 第7章 数组的扩展

### Array.from()
用于将两类对象转为真正的数组：
1. 类似数组的对象（array-like object）
2. 可遍历（iterable）的对象（包括ES6新增的数据结构Set和Map，只要是部署了Iterator接口的数据结构，Array.from都能将其转为数组）
```javascript
let arrayLike = {
    '0': 'a',
    '1': 'b',
    '2': 'c',
    length: 3
};

// ES5的写法
var arr1 = [].slice.call(arrayLike); // ['a', 'b', 'c']

// ES6的写法
let arr2 = Array.from(arrayLike); // ['a', 'b', 'c']
```

扩展运算符（...）也可以将某些数据结构转为数组：
```javascript
// arguments对象
function foo() {
  var args = [...arguments];
}

// NodeList对象
[...document.querySelectorAll('div')]
```
扩展运算符背后调用的是遍历器接口（Symbol.iterator），如果一个对象没有部署这个接口，就无法转换。Array.from方法则是还支持类似数组的对象。`所谓类似数组的对象，本质特征只有一点，即必须有length属性。因此，任何有length属性的对象，都可以通过Array.from方法转为数组，而此时扩展运算符就无法转换`。
```javascript
Array.from({ length: 3 });
// [ undefined, undefined, undefined ]
```

Array.from还可以接受第二个参数，作用类似于数组的map方法，用来对每个元素进行处理，将处理后的值放入返回的数组:
```javascript
Array.from(arrayLike, x => x * x);
// 等同于
Array.from(arrayLike).map(x => x * x);

Array.from([1, 2, 3], (x) => x * x)
// [1, 4, 9]
```

如果map函数里面用到了this关键字，还可以传入Array.from的第三个参数，用来绑定this。

### Array.of()
用于将一组值，转换为数组：
```javascript
Array.of(3, 11, 8) // [3,11,8]
Array.of(3) // [3]
Array.of(3).length // 1

Array.of() // []
Array.of(undefined) // [undefined]
Array.of(1) // [1]
Array.of(1, 2) // [1, 2]
```
这个方法的主要目的，是弥补数组构造函数Array()的不足。因为参数个数的不同，会导致Array()的行为有差异（只有当参数个数不少于2个时，Array()才会返回由参数组成的新数组。参数个数只有一个时，实际上是指定数组的长度）：
```javascript
Array() // []
Array(3) // [, , ,]
Array(3, 11, 8) // [3, 11, 8]
```

### 数组实例的copyWithin() 
在当前数组内部，将指定位置的成员复制到其他位置（会覆盖原有成员），然后返回当前数组。也就是说，使用这个方法，会修改当前数组。
Array.prototype.copyWithin(target, start = 0, end = this.length)
```javascript
[1, 2, 3, 4, 5].copyWithin(0, 3)
// [4, 5, 3, 4, 5]
```

### 数组实例的find()和findIndex() 
find方法，用于找出第一个符合条件的数组成员。它的参数是一个回调函数，所有数组成员依次执行该回调函数，直到找出第一个返回值为true的成员，然后返回该成员。如果没有符合条件的成员，则返回undefined。
```javascript
[1, 4, -5, 10].find((n) => n < 0)
// -5

[1, 5, 10, 15].find(function(value, index, arr) {
  return value > 9;
}) // 10
```

findIndex方法的用法与find方法非常类似，返回第一个符合条件的数组成员的位置，如果所有成员都不符合条件，则返回-1。
这两个方法都可以发现NaN，弥补了数组的IndexOf方法的不足。
```javascript
[NaN].indexOf(NaN)
// -1

[NaN].findIndex(y => Object.is(NaN, y))
// 0
```

### 数组实例的fill()
使用给定值，填充一个数组
```javascript
['a', 'b', 'c'].fill(7)
// [7, 7, 7]

new Array(3).fill(7)
// [7, 7, 7]

['a', 'b', 'c'].fill(7, 1, 2)
// ['a', 7, 'c']
```


### 数组实例的entries()，keys()和values()
ES6提供三个新的方法——entries()，keys()和values()——用于遍历数组。它们都返回一个遍历器对象，可以用for...of循环进行遍历，唯一的区别是keys()是对键名的遍历、values()是对键值的遍历，entries()是对键值对的遍历。
```javascript
for (let index of ['a', 'b'].keys()) {
  console.log(index);
}
// 0
// 1

for (let elem of ['a', 'b'].values()) {
  console.log(elem);
}
// 'a'
// 'b'

for (let [index, elem] of ['a', 'b'].entries()) {
  console.log(index, elem);
}
// 0 "a"
// 1 "b"
```

### 数组实例的includes() 
返回一个布尔值，表示某个数组是否包含给定的值：
```javascript
[1, 2, 3].includes(2);     // true
[1, 2, 3].includes(4);     // false
[1, 2, NaN].includes(NaN); // true
```

### 数组的空位
数组的空位指，数组的某一个位置没有任何值。比如，Array构造函数返回的数组都是空位：
```javascript
Array(3) // [, , ,]
```
注意，空位不是undefined，一个位置的值等于undefined，依然是有值的：
```javascript
0 in [undefined, undefined, undefined] // true
0 in [, , ,] // false
```

ES5对空位的处理很不一致，大多数情况下会忽略空位:
```javascript
// forEach方法
[,'a'].forEach((x,i) => console.log(i)); // 1

// filter方法
['a',,'b'].filter(x => true) // ['a','b']

// every方法
[,'a'].every(x => x==='a') // true

// some方法
[,'a'].some(x => x !== 'a') // false

// map方法
[,'a'].map(x => 1) // [,1]

// join方法
[,'a',undefined,null].join('#') // "#a##"

// toString方法
[,'a',undefined,null].toString() // ",a,,"
```
ES6则是明确将空位转为undefined。
由于空位的处理规则非常不统一，所以建议避免出现空位。

## 第8章 函数的扩展

### 函数参数的默认值
```javascript
function log(x, y = 'World') {
  console.log(x, y);
}

log('Hello') // Hello World
log('Hello', 'China') // Hello China
log('Hello', '') // Hello
```

如果传入undefined，将触发参数等于默认值，null则没有这个效果:
```javascript
function foo(x = 5, y = 6) {
  console.log(x, y);
}

foo(undefined, null)
// 5 null
```

### 函数的length属性
指定了默认值以后，函数的length属性，将返回没有指定默认值的参数个数。
```javascript
(function (a) {}).length // 1
(function (a = 5) {}).length // 0
(function (a, b, c = 5) {}).length // 2
```

### rest参数
ES6引入rest参数（形式为“...变量名”），用于获取函数的多余参数。rest参数搭配的变量是一个数组，该变量将多余的参数放入数组中。
```javascript
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

### 扩展运算符
将一个数组转为用逗号分隔的参数序列。
```javascript
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
```javascript
function f(x, y, z) {
  // ...
}
f.apply(null, args); // ES5的写法
f(...args); // ES6的写法
```

### name属性
返回该函数的函数名：
```javascript
function foo() {}
foo.name // "foo"
```
如果将一个匿名函数赋值给一个变量，ES5的name属性，会返回空字符串，而ES6的name属性会返回实际的函数名：
```javascript
var func1 = function () {};

// ES5
func1.name // ""

// ES6
func1.name // "func1"
```
如果将一个具名函数赋值给一个变量，则ES5和ES6的name属性都返回这个具名函数原本的名字。

Function构造函数返回的函数实例，name属性的值为“anonymous”。
```javascript
(new Function).name // "anonymous"
```

bind返回的函数，name属性值会加上“bound ”前缀。
```javascript
function foo() {};
foo.bind({}).name // "bound foo"

(function(){}).bind({}).name // "bound "
```

### 箭头函数
```javascript
var f = v => v;
var f = () => 5;
var sum = (num1, num2) => num1 + num2;
var sum = (num1, num2) => { return num1 + num2; }
```

由于大括号被解释为代码块，所以如果箭头函数直接返回一个对象，必须在对象外面加上括号：
```javascript
var getTempItem = id => ({ id: id, name: "Temp" });
```

箭头函数有几个使用注意点：
1. 函数体内的this对象，就是定义时所在的对象，而不是使用时所在的对象。
```javascript
function foo() {
  setTimeout(() => {
    console.log('id:', this.id);
  }, 100);
}

var id = 21;

foo.call({ id: 42 });
// id: 42
```
2. 不可以当作构造函数，也就是说，不可以使用new命令，否则会抛出一个错误。
3. 不可以使用arguments对象，该对象在函数体内不存在。如果要用，可以用Rest参数代替。
4. 不可以使用yield命令，因此箭头函数不能用作Generator函数。

箭头函数中this指向的固定化，并不是因为箭头函数内部有绑定this的机制，实际原因是箭头函数根本没有自己的this，导致内部的this就是外层代码块的this。正是因为它没有this，所以也就不能用作构造函数。箭头函数转成ES5的代码如下：
```javascript
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
```javascript
function foo() {
  setTimeout(() => {
    console.log('args:', arguments);
  }, 100);
}

foo(2, 4, 6, 8)
// args: [2, 4, 6, 8]
```
由于箭头函数没有自己的this，所以当然也就不能用call()、apply()、bind()这些方法去改变this的指向。


### 函数绑定
ES7提出了“函数绑定”（function bind）运算符，用来取代call、apply、bind调用。函数绑定运算符是并排的两个双冒号（::），双冒号左边是一个对象，右边是一个函数。该运算符会自动将左边的对象，作为上下文环境（即this对象），绑定到右边的函数上面。
```javascript
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

### 尾调用优化
尾调用（Tail Call）指某个函数的最后一步操作是返回另一个函数的调用。
```javascript
function f(x){
  return g(x);
}
```

不属于尾调用的情况：
```javascript
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
```javascript
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
```javascript
// 阶乘函数，计算n的阶乘，最多需要保存n个调用记录，复杂度 O(n) 
function factorial(n) {
  if (n === 1) return 1;
  return n * factorial(n - 1);  // 不属于尾调用
}

factorial(5) // 120
```
阶乘函数 factorial 需要用到一个中间变量 total ，那就把这个中间变量改写成函数的参数，改写成尾递归：
```javascript
// 复杂度 O(1)
function factorial(n, total) {
  if (n === 1) return total;
  return factorial(n - 1, n * total);
}

factorial(5, 1) // 120
```
“尾调用优化”对递归操作意义重大，所以一些函数式编程语言将其写入了语言规格。ES6也是如此，第一次明确规定，所有ECMAScript的实现，都必须部署“尾调用优化”。这就是说，`在ES6中，只要使用尾递归，就不会发生栈溢出，相对节省内存`。

ES6的尾调用优化只在严格模式下开启，正常模式是无效的。这是因为在正常模式下，函数内部有两个变量，可以跟踪函数的调用栈:func.arguments、func.caller尾调用优化发生时，函数的调用栈会改写，因此上面两个变量就会失真。严格模式禁用这两个变量，所以尾调用模式仅在严格模式下生效。

### 函数参数的尾逗号
ES7有一个提案，允许函数的最后一个参数有尾逗号（trailing comma）,这样，当以后需要再添加新参数时，这一行代码在版本控制中不会有改变：
```javascript
function clownsEverywhere(
  param1,
  param2,
) { /* ... */ }
```


## 第9章 对象的扩展

### 属性的简洁表示法
ES6允许直接写入变量和函数，作为对象的属性和方法（允许在对象之中，只写属性名，不写属性值。这时，属性值等于属性名所代表的变量的值）：
```javascript
var birth = '2000/01/01';

var Person = {

  name: '张三',

  //等同于birth: birth
  birth,

  // 等同于hello: function ()...
  hello() { console.log('我的名字是', this.name); }

};
```
### 属性名表达式
ES6允许字面量定义对象时，用表达式作为属性名（或方法名），表达式必须放在方括号内：
```javascript
let propKey = 'foo';

let obj = {
  [propKey]: true,
  ['a' + 'bc']: 123,
  ['h'+'ello']() {
    return 'hi';
  }
};
```

属性名表达式与简洁表示法，不能同时使用:
```javascript
// 报错
var foo = 'bar';
var bar = 'abc';
var baz = { [foo] };

// 正确
var foo = 'bar';
var baz = { [foo]: 'abc'};
```

### 方法的name属性
```javascript
var person = {
  sayName() {
    console.log(this.name);
  },
  get firstName() {
    return "Nicholas";
  }
};

person.sayName.name   // "sayName"
person.firstName.name // "get firstName"
```
有两种特殊情况：bind方法创造的函数，name属性返回“bound”加上原函数的名字；Function构造函数创造的函数，name属性返回“anonymous”。
```javascript
(new Function()).name // "anonymous"

var doSomething = function() {
  // ...
};
doSomething.bind().name // "bound doSomething"
```

如果对象的方法是一个Symbol值，那么name属性返回的是这个Symbol值的描述：
```javascript
const key1 = Symbol('description');
const key2 = Symbol();
let obj = {
  [key1]() {},
  [key2]() {},
};
obj[key1].name // "[description]"
obj[key2].name // ""
```

### Object.is()
相等运算符（==）和严格相等运算符（===）都有缺点，前者会自动转换数据类型，后者的NaN不等于自身，以及+0等于-0。ES6提出“Same-value equality”（同值相等）算法，用来解决这个问题。Object.is就是部署这个算法的新方法。它用来比较两个值是否严格相等，与严格比较运算符（===）的行为基本一致，不同之处只有两个：一是+0不等于-0，二是NaN等于自身：
```javascript
+0 === -0 //true
NaN === NaN // false

Object.is(+0, -0) // false
Object.is(NaN, NaN) // true
```

### Object.assign()
Object.assign方法用于对象的合并，将源对象（source）的所有可枚举属性，复制到目标对象（target）：
```javascript
var target = { a: 1 };

var source1 = { b: 2 };
var source2 = { c: 3 };

Object.assign(target, source1, source2);
target // {a:1, b:2, c:3}
```
如果目标对象与源对象有同名属性，或多个源对象有同名属性，则后面的属性会覆盖前面的属性。
Object.assign拷贝的属性是有限制的，只拷贝源对象的自身属性（不拷贝继承属性），也不拷贝不可枚举的属性（enumerable: false）。
属性名为Symbol值的属性，也会被Object.assign拷贝。
```javascript
Object.assign({ a: 'b' }, { [Symbol('c')]: 'd' })
// { a: 'b', Symbol(c): 'd' }
```
Object.assign方法实行的是浅拷贝，而不是深拷贝。也就是说，如果源对象某个属性的值是对象，那么目标对象拷贝得到的是这个对象的引用。

对于嵌套的对象，一旦遇到同名属性，Object.assign的处理方法是替换，而不是添加:
```javascript
var target = { a: { b: 'c', d: 'e' } }
var source = { a: { b: 'hello' } }
Object.assign(target, source)
// { a: { b: 'hello' } }
```

Object.assign可以用来处理数组，但是会把数组视为对象：
```javascript
Object.assign([1, 2, 3], [4, 5])
// [4, 5, 3]
```

### 属性的可枚举性
对象的每个属性都有一个描述对象（Descriptor），用来控制该属性的行为。`Object.getOwnPropertyDescriptor`方法可以获取该属性的描述对象。
```javascript
let obj = { foo: 123 };
Object.getOwnPropertyDescriptor(obj, 'foo')
//  {
//    value: 123,
//    writable: true,
//    enumerable: true,
//    configurable: true
//  }
```
ES5有三个操作会忽略enumerable为false的属性。
1. for...in循环：只遍历对象自身的和继承的可枚举的属性
2. Object.keys()：返回对象自身的所有可枚举的属性的键名
3. JSON.stringify()：只串行化对象自身的可枚举的属性
ES6新增了一个操作Object.assign()，会忽略enumerable为false的属性，只拷贝对象自身的可枚举的属性。
这四个操作之中，只有for...in会返回继承的属性。
另外，ES6规定，所有Class的原型的方法都是不可枚举的。
```javascript
Object.getOwnPropertyDescriptor(class {foo() {}}.prototype, 'foo').enumerable
// false
```

### 属性的遍历
ES6一共有5种方法可以遍历对象的属性。
1. `for...in`
for...in循环遍历对象自身的和继承的可枚举属性（不含Symbol属性）。

2. `Object.keys(obj)`
Object.keys返回一个数组，包括对象自身的（不含继承的）所有可枚举属性（不含Symbol属性）。

3. `Object.getOwnPropertyNames(obj)`
Object.getOwnPropertyNames返回一个数组，包含对象自身的所有属性（不含Symbol属性，但是包括不可枚举属性）。

4. `Object.getOwnPropertySymbols(obj)`
Object.getOwnPropertySymbols返回一个数组，包含对象自身的所有Symbol属性。

5. `Reflect.ownKeys(obj)`
Reflect.ownKeys返回一个数组，包含对象自身的所有属性，不管是属性名是Symbol或字符串，也不管是否可枚举。

以上的5种方法遍历对象的属性，都遵守同样的属性遍历的次序规则：
首先遍历所有属性名为`数值`的属性，按照数字排序。
其次遍历所有属性名为`字符串`的属性，按照生成时间排序。
最后遍历所有属性名为`Symbol值`的属性，按照生成时间排序。
```javascript
Reflect.ownKeys({ [Symbol()]:0, b:0, 10:0, 2:0, a:0 })
// ['2', '10', 'b', 'a', Symbol()]
```

### __proto__属性，Object.setPrototypeOf()，Object.getPrototypeOf() 
__proto__属性用来读取或设置当前对象的prototype对象,在实现上，__proto__调用的是Object.prototype.__proto__。如果一个对象本身部署了__proto__属性，则该属性的值就是对象的原型。
```javascript
// es6的写法
var obj = {
  method: function() { ... }
};
obj.__proto__ = someOtherObj;

// es5的写法
var obj = Object.create(someOtherObj);
obj.method = function() { ... };
```
无论从语义的角度，还是从兼容性的角度，都不要使用这个属性，而是使用下面的Object.setPrototypeOf()（写操作）、Object.getPrototypeOf()（读操作）、Object.create()（生成操作）代替。

### Object.values()，Object.entries()
ES7有一个提案，引入了跟Object.keys配套的Object.values和Object.entries。
(略)

### 对象的扩展运算符
ES7有一个提案，将Rest解构赋值/扩展运算符（...）引入对象
```javascript
let { x, y, ...z } = { x: 1, y: 2, a: 3, b: 4 };
x // 1
y // 2
z // { a: 3, b: 4 }
```

Rest解构赋值不会拷贝继承自原型对象的属性：
```javascript
let o1 = { a: 1 };
let o2 = { b: 2 };
o2.__proto__ = o1;
let o3 = { ...o2 };
o3 // { b: 2 }
```

扩展运算符（...）用于取出参数对象的所有可遍历属性，拷贝到当前对象之中：
```javascript
let z = { a: 3, b: 4 };
let n = { ...z };
n // { a: 3, b: 4 }
```
这等同于使用Object.assign方法。


### Object.getOwnPropertyDescriptors() 
ES7有一个提案，提出了Object.getOwnPropertyDescriptors方法，返回指定对象所有自身属性（非继承属性）的描述对象。

可以用来实现Mixin（混入）模式：
```javascript
let mix = (object) => ({
  with: (...mixins) => mixins.reduce(
    (c, mixin) => Object.create(
      c, Object.getOwnPropertyDescriptors(mixin)
    ), object)
});

// multiple mixins example
let a = {a: 'a'};
let b = {b: 'b'};
let c = {c: 'c'};
let d = mix(c).with(a, b);
```

## 第10章 Symbol
ES6引入了一种新的原始数据类型Symbol，表示独一无二的值。它是`JavaScript语言的第七种数据类型`，前六种是：Undefined、Null、布尔值（Boolean）、字符串（String）、数值（Number）、对象（Object）。
Symbol值通过Symbol函数生成。这就是说，`对象的属性名现在可以有两种类型`，一种是原来就有的字符串，另一种就是新增的Symbol类型。凡是属性名属于Symbol类型，就都是独一无二的，可以保证不会与其他属性名产生冲突。

Symbol函数前不能使用new命令，否则会报错。这是因为生成的Symbol是一个原始类型的值，不是对象。也就是说，由于Symbol值不是对象，所以不能添加属性。`基本上，它是一种类似于字符串的数据类型`。
Symbol函数可以接受一个字符串作为参数，表示对Symbol实例的描述，主要是为了在控制台显示，或者转为字符串时，比较容易区分。

```javascript
let s = Symbol();

typeof s
// "symbol"

var s1 = Symbol('foo');
var s2 = Symbol('bar');

s1 // Symbol(foo)
s2 // Symbol(bar)

s1.toString() // "Symbol(foo)"
s2.toString() // "Symbol(bar)"

// 没有参数的情况
var s1 = Symbol();
var s2 = Symbol();

s1 === s2 // false

// 有参数的情况
var s1 = Symbol("foo");
var s2 = Symbol("foo");

s1 === s2 // false
```

Symbol值不能与其他类型的值进行运算，但是，Symbol值可以显式转为字符串，也可以转为布尔值，但是不能转为数值。：
```javascript
var sym = Symbol('My symbol');

"your symbol is " + sym
// TypeError: can't convert symbol to string
`your symbol is ${sym}`
// TypeError: can't convert symbol to string

var sym = Symbol('My symbol');

String(sym) // 'Symbol(My symbol)'
sym.toString() // 'Symbol(My symbol)'

var sym = Symbol();
Boolean(sym) // true
!sym  // false

if (sym) {
  // ...
}

Number(sym) // TypeError
sym + 2 // TypeError
```

### 作为属性名的Symbol
由于每一个Symbol值都是不相等的，这意味着Symbol值可以作为标识符，用于对象的属性名，就能保证不会出现同名的属性：
```javascript
var mySymbol = Symbol();

// 第一种写法
var a = {};
a[mySymbol] = 'Hello!';

// 第二种写法
var a = {
  [mySymbol]: 'Hello!'   // 在对象的内部，使用Symbol值定义属性时，Symbol值必须放在方括号之中
};

// 第三种写法
var a = {};
Object.defineProperty(a, mySymbol, { value: 'Hello!' });

// 以上写法都得到同样结果
a[mySymbol] // "Hello!"
```

Symbol值作为对象属性名时，不能用点运算符:
```javascript
var mySymbol = Symbol();
var a = {};

a.mySymbol = 'Hello!';
a[mySymbol] // undefined
a['mySymbol'] // "Hello!"
```

### 属性名的遍历
Symbol作为属性名，该属性不会出现在for...in、for...of循环中，也不会被Object.keys()、Object.getOwnPropertyNames()返回。但是，它也不是私有属性，有一个Object.getOwnPropertySymbols方法，可以获取指定对象的所有Symbol属性名。

另一个新的API，Reflect.ownKeys方法可以返回所有类型的键名，包括常规键名和Symbol键名。

### Symbol.for()，Symbol.keyFor()
Symbol.for方法接受一个字符串作为参数，然后搜索有没有以该参数作为名称的Symbol值。如果有，就返回这个Symbol值，否则就新建并返回一个以该字符串为名称的Symbol值。
```javascript
var s1 = Symbol.for('foo');
var s2 = Symbol.for('foo');

s1 === s2 // true
```

Symbol.for为Symbol值登记的名字，是全局环境的，可以在不同的iframe或service worker中取到同一个值。

Symbol.keyFor方法返回一个已登记的Symbol类型值的key：
```javascript
var s1 = Symbol.for("foo");
Symbol.keyFor(s1) // "foo"

var s2 = Symbol("foo");
Symbol.keyFor(s2) // undefined
```

由于以Symbol值作为名称的属性，不会被常规方法遍历得到，可以利用这个特性，为对象定义一些非私有的、但又希望只用于内部的方法：
```javascript
var size = Symbol('size');

class Collection {
  constructor() {
    this[size] = 0;
  }

  add(item) {
    this[this[size]] = item;
    this[size]++;
  }

  static sizeOf(instance) {
    return instance[size];
  }
}

var x = new Collection();
Collection.sizeOf(x) // 0

x.add('foo');
Collection.sizeOf(x) // 1

Object.keys(x) // ['0']
Object.getOwnPropertyNames(x) // ['0']
Object.getOwnPropertySymbols(x) // [Symbol(size)]
```

### 使用Symbol实现模块的 Singleton 模式
```javascript
// mod.js
const FOO_KEY = Symbol.for('foo');

function A() {
  this.foo = 'hello';
}

if (!global[FOO_KEY]) {
  global[FOO_KEY] = new A();
}

module.exports = global[FOO_KEY];
```
上面代码中，可以保证global[FOO_KEY]不会被其他脚本改写。


### 内置的Symbol值
ES6还提供了11个内置的Symbol值，指向语言内部使用的方法：
1. Symbol.hasInstance：foo instanceof Foo在语言内部，实际调用的是Foo[Symbol.hasInstance](foo)

2. Symbol.isConcatSpreadable：表示该对象使用Array.prototype.concat()时，是否可以展开
```javascript
let arr1 = ['c', 'd'];
['a', 'b'].concat(arr1, 'e') // ['a', 'b', 'c', 'd', 'e']
arr1[Symbol.isConcatSpreadable] // undefined

let arr2 = ['c', 'd'];
arr2[Symbol.isConcatSpreadable] = false;
['a', 'b'].concat(arr2, 'e') // ['a', 'b', ['c','d'], 'e']
```

3. Symbol.species:指向一个方法。该对象作为构造函数创造实例时，会调用这个方法。即如果this.constructor[Symbol.species]存在，就会使用这个属性作为构造函数，来创造新的实例对象。

4. Symbol.match :指向一个函数。当执行str.match(myObject)时，如果该属性存在，会调用它，返回该方法的返回值

5. Symbol.replace:指向一个方法，当该对象被String.prototype.replace方法调用时，会返回该方法的返回值。

6. Symbol.search:指向一个方法，当该对象被String.prototype.search方法调用时，会返回该方法的返回值。

7. Symbol.split:指向一个方法，当该对象被String.prototype.split方法调用时，会返回该方法的返回值。

8. Symbol.iterator:指向该对象的默认遍历器方法
```javascript
var myIterable = {};
myIterable[Symbol.iterator] = function* () {
  yield 1;
  yield 2;
  yield 3;
};

[...myIterable] // [1, 2, 3]
```

9. Symbol.toPrimitive：指向一个方法。该对象被转为原始类型的值时，会调用这个方法，返回该对象对应的原始类型值。

10. Symbol.toStringTag：指向一个方法。在该对象上面调用Object.prototype.toString方法时，如果这个属性存在，它的返回值会出现在toString方法返回的字符串之中，表示对象的类型。也就是说，这个属性可以用来定制[object Object]或[object Array]中object后面的那个字符串。ES6新增内置对象的Symbol.toStringTag属性值如下：
JSON[Symbol.toStringTag]：'JSON'
Math[Symbol.toStringTag]：'Math'
Module对象M[Symbol.toStringTag]：'Module'
ArrayBuffer.prototype[Symbol.toStringTag]：'ArrayBuffer'
DataView.prototype[Symbol.toStringTag]：'DataView'
Map.prototype[Symbol.toStringTag]：'Map'
Promise.prototype[Symbol.toStringTag]：'Promise'
Set.prototype[Symbol.toStringTag]：'Set'
%TypedArray%.prototype[Symbol.toStringTag]：'Uint8Array'等
WeakMap.prototype[Symbol.toStringTag]：'WeakMap'
WeakSet.prototype[Symbol.toStringTag]：'WeakSet'
%MapIteratorPrototype%[Symbol.toStringTag]：'Map Iterator'
%SetIteratorPrototype%[Symbol.toStringTag]：'Set Iterator'
%StringIteratorPrototype%[Symbol.toStringTag]：'String Iterator'
Symbol.prototype[Symbol.toStringTag]：'Symbol'
Generator.prototype[Symbol.toStringTag]：'Generator'
GeneratorFunction.prototype[Symbol.toStringTag]：'GeneratorFunction'

11. Symbol.unscopables：指向一个对象。该对象指定了使用with关键字时，哪些属性会被with环境排除。


## 第11章 Proxy和Reflect
Proxy可以理解成，在目标对象之前架设一层“拦截”，外界对该对象的访问，都必须先通过这层拦截，因此提供了一种机制，可以对外界的访问进行过滤和改写。ES6原生提供Proxy构造函数，用来生成Proxy实例：
var proxy = new Proxy(target, handler);
target参数表示所要拦截的目标对象，handler参数也是一个对象，用来定制拦截行为。

```javascript
var obj = new Proxy({}, {
  get: function (target, key, receiver) {
    console.log(`getting ${key}!`);
    return Reflect.get(target, key, receiver);
  },
  set: function (target, key, value, receiver) {
    console.log(`setting ${key}!`);
    return Reflect.set(target, key, value, receiver);
  }
});

obj.count = 1
//  setting count!
++obj.count
//  getting count!
//  setting count!
//  2
```

### Proxy支持的拦截操作
对于可以设置、但没有设置拦截的操作，则直接落在目标对象上，按照原先的方式产生结果。
1. get(target, propKey, receiver)
拦截对象属性的读取，比如proxy.foo和proxy['foo']。
参数receiver是一个对象，可选。

2. set(target, propKey, value, receiver)
拦截对象属性的设置，比如proxy.foo = v或proxy['foo'] = v，返回一个布尔值。

3. has(target, propKey)
拦截propKey in proxy的操作，以及对象的hasOwnProperty方法，返回一个布尔值。

4. deleteProperty(target, propKey)
拦截delete proxy[propKey]的操作，返回一个布尔值。

5. ownKeys(target)
拦截Object.getOwnPropertyNames(proxy)、Object.getOwnPropertySymbols(proxy)、Object.keys(proxy)，返回一个数组。该方法返回对象所有自身的属性，而Object.keys()仅返回对象可遍历的属性。

6. getOwnPropertyDescriptor(target, propKey)
拦截Object.getOwnPropertyDescriptor(proxy, propKey)，返回属性的描述对象。

7. defineProperty(target, propKey, propDesc)
拦截Object.defineProperty(proxy, propKey, propDesc）、Object.defineProperties(proxy, propDescs)，返回一个布尔值。

8. preventExtensions(target)
拦截Object.preventExtensions(proxy)，返回一个布尔值。

9. getPrototypeOf(target)
拦截Object.getPrototypeOf(proxy)，返回一个对象。

10. isExtensible(target)
拦截Object.isExtensible(proxy)，返回一个布尔值。

11. setPrototypeOf(target, proto)
拦截Object.setPrototypeOf(proxy, proto)，返回一个布尔值。
如果目标对象是函数，那么还有两种额外操作可以拦截。

12. apply(target, object, args)
拦截Proxy实例作为函数调用的操作，比如proxy(...args)、proxy.call(object, ...args)、proxy.apply(...)。

13. construct(target, args)
拦截Proxy实例作为构造函数调用的操作，比如new proxy(...args)。

### Proxy.revocable()
Proxy.revocable方法返回一个对象，该对象的proxy属性是Proxy实例，revoke属性是一个函数，可以取消Proxy实例:
```javascript
let target = {};
let handler = {};

let {proxy, revoke} = Proxy.revocable(target, handler);

proxy.foo = 123;
proxy.foo // 123

revoke();
proxy.foo // TypeError: Revoked
```

### Reflect
Reflect对象的设计目的有这样几个：
1. 将Object对象的一些明显属于语言内部的方法（比如Object.defineProperty），放到Reflect对象上。现阶段，某些方法同时在Object和Reflect对象上部署，未来的新方法将只部署在Reflect对象上。

2. 修改某些Object方法的返回结果，让其变得更合理。比如，Object.defineProperty(obj, name, desc)在无法定义属性时，会抛出一个错误，而Reflect.defineProperty(obj, name, desc)则会返回false。

3. 让Object操作都变成函数行为。某些Object操作是命令式，比如name in obj和delete obj[name]，而Reflect.has(obj, name)和Reflect.deleteProperty(obj, name)让它们变成了函数行为。

4. Reflect对象的方法与Proxy对象的方法一一对应，只要是Proxy对象的方法，就能在Reflect对象上找到对应的方法。这就`让Proxy对象可以方便地调用对应的Reflect方法，完成默认行为，作为修改行为的基础`。也就是说，`不管Proxy怎么修改默认行为，总可以在Reflect上获取默认行为`：
```javascript
var loggedObj = new Proxy(obj, {
  get(target, name) {
    console.log('get', target, name);
    return Reflect.get(target, name);
  },
  deleteProperty(target, name) {
    console.log('delete' + name);
    return Reflect.deleteProperty(target, name);
  },
  has(target, name) {
    console.log('has' + name);
    return Reflect.has(target, name);
  }
});
```
上面代码中，每一个Proxy对象的拦截操作（get、delete、has），内部都调用对应的Reflect方法，保证原生行为能够正常执行。添加的工作，就是将每一个操作输出一行日志。

### Reflect对象的方法
Reflect.apply(target,thisArg,args) 

Reflect.construct(target,args)
等同于new target(...args)，这提供了一种不使用new，来调用构造函数的方法。

Reflect.get(target,name,receiver)
查找并返回target对象的name属性，如果没有该属性，则返回undefined。
如果name属性部署了读取函数，则读取函数的this绑定receiver。
```javascript
var obj = {
  get foo() { return this.bar(); },
  bar: function() { ... }
};

// 下面语句会让 this.bar()
// 变成调用 wrapper.bar()
Reflect.get(obj, "foo", wrapper);


Reflect.set(target,name,value,receiver)
Reflect.defineProperty(target,name,desc)
Reflect.deleteProperty(target,name)
Reflect.has(target,name)
Reflect.ownKeys(target)
Reflect.isExtensible(target)
Reflect.preventExtensions(target)
Reflect.getOwnPropertyDescriptor(target, name)

Reflect.getPrototypeOf(target)
```
读取对象的__proto__属性，对应Object.getPrototypeOf(obj)

Reflect.setPrototypeOf(target, prototype)


## 第12章 二进制数组
二进制数组允许开发者`以数组下标的形式，直接操作内存`，`使得开发者有可能通过JavaScript与操作系统的原生接口进行二进制通信`。

### 二进制数组由三类对象组成
1. `ArrayBuffer`对象：代表内存之中的一段二进制数据，可以通过“视图”进行操作。“视图”部署了数组接口，这意味着，可以用数组的方法操作内存。

2. `TypedArray`视图：是一组不同类型视图的统称，共包括9种类型的视图，比如Uint8Array（无符号8位整数）数组视图, Int16Array（16位整数）数组视图, Float32Array（32位浮点数）数组视图等等。

3. `DataView`视图：可以自定义复合格式的视图，比如第一个字节是Uint8（无符号8位整数）、第二、三个字节是Int16（16位整数）、第四个字节开始是Float32（32位浮点数）等等，此外还可以自定义字节序。

即，ArrayBuffer对象代表原始的二进制数据，TypedArray视图用来读写简单类型的二进制数据，DataView视图用来读写复杂类型的二进制数据。

`注意，二进制数组并不是真正的数组，而是类似数组的对象。`


### ArrayBuffer对象 
ArrayBuffer对象代表储存二进制数据的一段内存，它`不能直接读写，只能通过视图（TypedArray视图和DataView视图)来读写`，视图的作用是以指定格式解读二进制数据：
```javascript
var buf = new ArrayBuffer(32);
var dataView = new DataView(buf);
dataView.getUint8(0) // 0
```

TypedArray视图的构造函数，除了接受ArrayBuffer实例作为参数，还可以接受普通数组作为参数，直接分配内存生成底层的ArrayBuffer实例，并同时完成对这段内存的赋值:
```javascript
var typedArray = new Uint8Array([0,1,2]);
typedArray.length // 3

typedArray[0] = 5;
typedArray // [5, 1, 2]
```

ArrayBuffer实例的`byteLength属性`，返回所分配的内存区域的`字节长度`：
```javascript
var buffer = new ArrayBuffer(32);
buffer.byteLength
// 32
```

ArrayBuffer实例的`slice方法`，可以将内存区域的一部分，拷贝生成一个新的ArrayBuffer对象：
```javascript
var buffer = new ArrayBuffer(8);
var newBuffer = buffer.slice(0, 3);  // 拷贝buffer对象的前3个字节（从0开始，到第3个字节前面结束）
```
除了slice方法，ArrayBuffer对象不提供任何直接读写内存的方法，只允许在其上方建立视图，然后通过视图读写。

ArrayBuffer的静态方法`isView`，返回一个布尔值，表示参数是否为ArrayBuffer的视图实例：
```javascript
var buffer = new ArrayBuffer(8);
ArrayBuffer.isView(buffer) // false

var v = new Int32Array(buffer);
ArrayBuffer.isView(v) // true
```

### TypedArray视图
普通数组与TypedArray数组的差异主要在以下方面：
1. TypedArray数组的所有成员，都是同一种类型。
2. TypedArray数组的成员是连续的，不会有空位。
3. TypedArray数组成员的默认值为0。比如，new Array(10)返回一个普通数组，里面没有任何成员，只是10个空位；new Uint8Array(10)返回一个TypedArray数组，里面10个成员都是0。
4. TypedArray数组只是一层视图，`本身不储存数据`，它的数据都储存在底层的ArrayBuffer对象之中，要获取底层对象必须使用buffer属性。

视图可以不通过ArrayBuffer对象，直接分配内存而生成：
```javascript
var f64a = new Float64Array(8);
f64a[0] = 10;
f64a[1] = 20;
f64a[2] = f64a[0] + f64a[1];
```
普通数组的操作方法和属性，对TypedArray数组完全适用，除了concat方法。
`TypedArray数组只能处理小端字节序！DataView对象，可以设定字节序。`


不同的视图类型，所能容纳的数值范围是确定的。超出这个范围，就会出现溢出。TypedArray数组（除了Uint8ClampedArray）的溢出处理规则，简单来说，就是抛弃溢出的位，然后按照视图类型进行解释。
```javascript
var uint8 = new Uint8Array(1);

uint8[0] = 256;
uint8[0] // 0

uint8[0] = -1;
uint8[0] // 255
```
Uint8ClampedArray规定，凡是发生正向溢出，该值一律等于当前数据类型的最大值，即255；如果发生负向溢出，该值一律等于当前数据类型的最小值，即0。

buffer属性，返回整段内存区域对应的ArrayBuffer对象。该属性为只读属性。
set方法用于复制数组（普通数组或TypedArray数组），也就是将一段内容完全复制到另一段内存。
subarray方法是对于TypedArray数组的一部分，再建立一个新的视图。
(其他方法，略)

### DataView视图
在设计目的上，ArrayBuffer对象的各种TypedArray视图，是用来向网卡、声卡之类的本机设备传送数据，所以使用本机的字节序就可以了；而DataView视图的设计目的，是用来处理网络设备传来的数据，所以大端字节序或小端字节序是可以自行设定的。

DataView实例提供8个方法读取内存：
getInt8：读取1个字节，返回一个8位整数。
getUint8：读取1个字节，返回一个无符号的8位整数。
getInt16：读取2个字节，返回一个16位整数。
getUint16：读取2个字节，返回一个无符号的16位整数。
getInt32：读取4个字节，返回一个32位整数。
getUint32：读取4个字节，返回一个无符号的32位整数。
getFloat32：读取4个字节，返回一个32位浮点数。
getFloat64：读取8个字节，返回一个64位浮点数。
如果一次读取两个或两个以上字节，就必须明确数据的存储方式，到底是小端字节序还是大端字节序。默认情况下，DataView的get方法使用大端字节序解读数据，如果需要使用小端字节序解读，必须在get方法的第二个参数指定true。


## 第13章 Set和Map数据结构

### Set
可以利用Set去除数组重复成员：
```javascript
// 去除数组的重复成员
[...new Set(array)]
```

Set的属性：
Set.prototype.constructor：构造函数，默认就是Set函数。
Set.prototype.size：返回Set实例的成员总数。

Set的操作方法：
add(value)：添加某个值，返回Set结构本身。
delete(value)：删除某个值，返回一个布尔值，表示删除是否成功。
has(value)：返回一个布尔值，表示该值是否为Set的成员。
clear()：清除所有成员，没有返回值。

Set的遍历方法：
keys()：返回键名的遍历器，由于Set结构没有键名，只有键值（或者说键名和键值是同一个值），所以key方法和value方法的行为完全一致。
values()：返回键值的遍历器
entries()：返回键值对的遍历器
forEach()：使用回调函数遍历每个成员
```javascript
let set = new Set([1, 2, 3]);
set.forEach((value, key) => console.log(value * 2) )
// 2
// 4
// 6
```
`Set的遍历顺序就是插入顺序`。

使用Set可以很容易地实现并集（Union）、交集（Intersect）和差集（Difference）：
```javascript
let a = new Set([1, 2, 3]);
let b = new Set([4, 3, 2]);

// 并集
let union = new Set([...a, ...b]);
// Set {1, 2, 3, 4}

// 交集
let intersect = new Set([...a].filter(x => b.has(x)));
// set {2, 3}

// 差集
let difference = new Set([...a].filter(x => !b.has(x)));
// Set {1}
```

### WeakSet
WeakSet与Set有两个区别：
1. WeakSet的成员只能是对象，而不能是其他类型的值。
2. WeakSet中的对象都是弱引用，即垃圾回收机制不考虑WeakSet对该对象的引用，也就是说，如果其他对象都不再引用该对象，那么垃圾回收机制会自动回收该对象所占用的内存，不考虑该对象还存在于WeakSet之中。这个特点意味着，无法引用WeakSet的成员（WeakSet没有size属性），因此WeakSet是不可遍历的。
WeakSet的一个用处，是储存DOM节点，而不用担心这些节点从文档移除时，会引发内存泄漏。

### Map
Map数据结构类似于对象，也是键值对的集合，但是“键”的范围不限于字符串，各种类型的值（包括对象）都可以当作键：
```javascript
var m = new Map();
var o = {p: 'Hello World'};

m.set(o, 'content')
m.get(o) // "content"

m.has(o) // true
m.delete(o) // true
m.has(o) // false
```
只有对同一个对象的引用，Map结构才将其视为同一个键，Map的键实际上是跟内存地址绑定的。

Map的操作方法和遍历方法与Set类似，略。

如果所有Map的键都是字符串，它可以转为对象：
```javascript
function strMapToObj(strMap) {
  let obj = Object.create(null);
  for (let [k,v] of strMap) {
    obj[k] = v;
  }
  return obj;
}

let myMap = new Map().set('yes', true).set('no', false);
strMapToObj(myMap)
// { yes: true, no: false }
```

Map转为JSON要区分两种情况。一种情况是，Map的键名都是字符串，这时可以选择转为对象JSON：
```javascript
function strMapToJson(strMap) {
  return JSON.stringify(strMapToObj(strMap));
}

let myMap = new Map().set('yes', true).set('no', false);
strMapToJson(myMap)
// '{"yes":true,"no":false}'
```
另一种情况是，Map的键名有非字符串，这时可以选择转为数组JSON：
```javascript
function mapToArrayJson(map) {
  return JSON.stringify([...map]);
}

let myMap = new Map().set(true, 7).set({foo: 3}, ['abc']);
mapToArrayJson(myMap)
// '[[true,7],[{"foo":3},["abc"]]]'
```

### WeakMap
WeakMap结构与Map结构基本类似，唯一的区别是它只接受对象作为键名（null除外），不接受其他类型的值作为键名，而且键名所指向的对象，不计入垃圾回收机制。
WeakMap的设计目的在于，键名是对象的弱引用（垃圾回收机制不将该引用考虑在内），所以其所对应的对象可能会被自动回收。当对象被回收后，WeakMap自动移除对应的键值对。典型应用是，一个对应DOM元素的WeakMap结构，当某个DOM元素被清除，其所对应的WeakMap记录就会自动被移除。基本上，WeakMap的专用场合就是，它的键所对应的对象，可能会在将来消失。WeakMap结构有助于防止内存泄漏。
WeakMap与Map在API上的区别主要是两个，一是没有遍历操作（即没有key()、values()和entries()方法），也没有size属性；二是无法清空，即不支持clear方法。这与WeakMap的键不被计入引用、被垃圾回收机制忽略有关。因此，WeakMap只有四个方法可用：get()、set()、has()、delete()。

WeakMap应用的典型场合就是DOM节点作为键名：
```javascript
let myElement = document.getElementById('logo');
let myWeakmap = new WeakMap();

myWeakmap.set(myElement, {timesClicked: 0});

myElement.addEventListener('click', function() {
  let logoData = myWeakmap.get(myElement);
  logoData.timesClicked++;
  myWeakmap.set(myElement, logoData);
}, false);
```

## 第14章 Iterator和for...of循环

Iterator的作用有三个：
1. 为各种数据结构，提供一个统一的、简便的访问接口；
2. 使得数据结构的成员能够按某种次序排列；
3. ES6创造了一种新的遍历命令for...of循环，Iterator接口主要供for...of消费。

Iterator的遍历过程：
1. 创建一个指针对象，指向当前数据结构的起始位置。也就是说，遍历器对象本质上，就是一个指针对象。
2. 第一次调用指针对象的next方法，可以将指针指向数据结构的第一个成员。
3. 第二次调用指针对象的next方法，指针就指向数据结构的第二个成员。
4. 不断调用指针对象的next方法，直到它指向数据结构的结束位置。
每一次调用next方法，都会返回数据结构的当前成员的信息。具体来说，就是返回一个包含value和done两个属性的对象。其中，value属性是当前成员的值，done属性是一个布尔值，表示遍历是否结束。

由于Iterator只是把接口规格加到数据结构之上，所以，遍历器与它所遍历的那个数据结构，实际上是分开的，完全可以写出没有对应数据结构的遍历器对象，或者说用遍历器对象模拟出数据结构：
```javascript
// 无限运行的遍历器对象
var it = idMaker();

it.next().value // '0'
it.next().value // '1'
it.next().value // '2'
// ...

function idMaker() {
  var index = 0;

  return {
    next: function() {
      return {value: index++, done: false};
    }
  };
}
```


### 数据结构的默认Iterator接口
ES6规定，默认的Iterator接口部署在数据结构的Symbol.iterator属性。一个数据结构只要具有Symbol.iterator属性，就可以认为是“可遍历的”（iterable）。调用Symbol.iterator方法，就会得到当前数据结构默认的遍历器生成函数。Symbol.iterator本身是一个表达式，返回Symbol对象的iterator属性，这是一个预定义好的、类型为Symbol的特殊值，所以要放在方括号内。一个对象如果要有可被for...of循环调用的Iterator接口，就必须在Symbol.iterator的属性上部署遍历器生成方法（原型链上的对象具有该方法也可）：
```javascript
class RangeIterator {
  constructor(start, stop) {
    this.value = start;
    this.stop = stop;
  }

  [Symbol.iterator]() { return this; }

  next() {
    var value = this.value;
    if (value < this.stop) {
      this.value++;
      return {done: false, value: value};
    } else {
      return {done: true, value: undefined};
    }
  }
}

function range(start, stop) {
  return new RangeIterator(start, stop);
}

for (var value of range(0, 3)) {
  console.log(value);
}
```

对象（Object）之所以没有默认部署Iterator接口，是因为对象的哪个属性先遍历，哪个属性后遍历是不确定的，需要开发者手动指定。

对于类似数组的对象（存在数值键名和length属性），部署Iterator接口，有一个简便方法，就是Symbol.iterator方法直接引用数组的Iterator接口：
```javascript
let iterable = {
  0: 'a',
  1: 'b',
  2: 'c',
  length: 3,
  [Symbol.iterator]: Array.prototype[Symbol.iterator]
};
for (let item of iterable) {
  console.log(item); // 'a', 'b', 'c'
}
```
注意，普通对象部署数组的Symbol.iterator方法，并无效果：
```javascript
let iterable = {
  a: 'a',
  b: 'b',
  c: 'c',
  length: 3,
  [Symbol.iterator]: Array.prototype[Symbol.iterator]
};
for (let item of iterable) {
  console.log(item); // undefined, undefined, undefined
}
```

### 默认调用Iterator接口（即Symbol.iterator方法）的场合
1. 解构赋值
2. 扩展运算符
3. yield*
4. 由于数组的遍历会调用遍历器接口，所以任何接受数组作为参数的场合，其实都调用了遍历器接口，如：
for...of
Array.from()
Map(), Set(), WeakMap(), WeakSet()（比如new Map([['a',1],['b',2]])）
Promise.all()
Promise.race()

### 字符串的Iterator接口
字符串是一个类似数组的对象，也原生具有Iterator接口。
```javascript
var someString = "hi";
typeof someString[Symbol.iterator]
// "function"

var iterator = someString[Symbol.iterator]();

iterator.next()  // { value: "h", done: false }
iterator.next()  // { value: "i", done: false }
iterator.next()  // { value: undefined, done: true }
```

### Iterator接口与Generator函数
```javascript
let obj = {
  * [Symbol.iterator]() {
    yield 'hello';
    yield 'world';
  }
};

for (let x of obj) {
  console.log(x);
}
// hello
// world
```

### 遍历器对象的return()，throw()
遍历器对象除了具有next方法，还可以具有return方法和throw方法。如果自己写遍历器对象生成函数，那么next方法是必须部署的，return方法和throw方法是否部署是可选的。
return方法的使用场合是，如果for...of循环提前退出（通常是因为出错，或者有break语句或continue语句），就会调用return方法。如果一个对象在完成遍历前，需要清理或释放资源，就可以部署return方法。
```javascript
function readLinesSync(file) {
  return {
    next() {
      if (file.isAtEndOfFile()) {
        file.close();
        return { done: true };
      }
    },
    return() {
      file.close();
      return { done: true };
    },
  };
}

for (let line of readLinesSync(fileName)) {
  console.log(x);  // 触发return()方法
  break;
}
```

throw方法主要是配合Generator函数使用，一般的遍历器对象用不到这个方法。

### for...of循环
一个数据结构只要部署了Symbol.iterator属性，就被视为具有iterator接口，就可以用for...of循环遍历它的成员。


## 第15章 Generator 函数
调用Generator函数后，该函数并不执行，返回的也不是函数运行结果，而是一个指向内部状态的指针对象，也就是遍历器对象（Iterator Object）。必须调用遍历器对象的next方法，使得指针移向下一个状态，每次调用next方法，内部指针就从函数头部或上一次停下来的地方开始执行，直到遇到下一个yield语句（或return语句）为止。换言之，Generator函数是分段执行的，yield语句是暂停执行的标记，而next方法可以恢复执行。
```javascript
function* helloWorldGenerator() {
  yield 'hello';
  yield 'world';
  return 'ending';
}

var hw = helloWorldGenerator();

hw.next()
// { value: 'hello', done: false }

hw.next()
// { value: 'world', done: false }

hw.next()
// { value: 'ending', done: true }

hw.next()
// { value: undefined, done: true }
```

### yield语句
遍历器对象的next方法的运行逻辑如下：
1. 遇到yield语句，就暂停执行后面的操作，并将紧跟在yield后面的那个表达式的值，作为返回的对象的value属性值。
2. 下一次调用next方法时，再继续往下执行，直到遇到下一个yield语句。
3. 如果没有再遇到新的yield语句，就一直运行到函数结束，直到return语句为止，并将return语句后面的表达式的值，作为返回的对象的value属性值。
4. 如果该函数没有return语句，则返回的对象的value属性值为undefined。

yield语句与return语句既有相似之处，也有区别。相似之处在于，都能返回紧跟在语句后面的那个表达式的值。区别在于每次遇到yield，函数暂停执行，`下一次再从该位置继续向后执行`，而return语句不具备位置记忆的功能。一个函数里面，只能执行一次（或者说一个）return语句，但是可以执行多次（或者说多个）yield语句。正常函数只能返回一个值，因为只能执行一次return；Generator函数可以返回一系列的值，因为可以有任意多个yield。从另一个角度看，也可以说Generator生成了一系列的值。

由于Generator函数就是遍历器生成函数，因此可以把Generator赋值给对象的Symbol.iterator属性，从而使得该对象具有Iterator接口(此时不再需要调用next方法)：
```javascript
var myIterable = {};
myIterable[Symbol.iterator] = function* () {
  yield 1;
  yield 2;
  yield 3;
};

[...myIterable] // [1, 2, 3]
```

### next方法的参数
yield句本身没有返回值，或者说总是返回undefined。next方法可以带一个参数，该参数就会被当作上一个yield语句的返回值。
```javascript
function* f() {
  for(var i=0; true; i++) {
    var reset = yield i;
    if(reset) { i = -1; }
  }
}

var g = f();

g.next() // { value: 0, done: false }
g.next() // { value: 1, done: false }
g.next(true) // { value: 0, done: false }
```
通过next方法的参数，就有办法在Generator函数开始运行之后，继续向函数体内部注入值。也就是说，可以在Generator函数运行的不同阶段，从外部向内部注入不同的值，从而调整函数行为。

注意，由于next方法的参数表示上一个yield语句的返回值，所以第一次使用next方法时，不能带有参数。V8引擎直接忽略第一次使用next方法时的参数，只有从第二次使用next方法开始，参数才是有效的。

### Generator.prototype.throw()
Generator函数返回的遍历器对象，都有一个throw方法，可以在函数体外抛出错误，然后在Generator函数体内捕获。
```javascript
var g = function* () {
  try {
    yield;
  } catch (e) {
    console.log('内部捕获', e);
  }
};

var i = g();
i.next();

try {
  i.throw('a');
  i.throw('b');
} catch (e) {
  console.log('外部捕获', e);
}
// 内部捕获 a
// 外部捕获 b
```
上面代码中，遍历器对象i连续抛出两个错误。第一个错误被Generator函数体内的catch语句捕获。i第二次抛出错误，由于Generator函数内部的catch语句已经执行过了，不会再捕捉到这个错误了，所以这个错误就被抛出了Generator函数体，被函数体外的catch语句捕获。
注意，`不要混淆遍历器对象的throw方法和全局的throw命令`。上面代码的错误，是用遍历器对象的throw方法抛出的，而不是用throw命令抛出的。后者只能被函数体外的catch语句捕获。

如果Generator函数内部没有部署try...catch代码块，那么throw方法抛出的错误，将被外部try...catch代码块捕获：
```javascript
var g = function* () {
  while (true) {
    yield;
    console.log('内部捕获', e);
  }
};

var i = g();
i.next();

try {
  i.throw('a');
  i.throw('b');
} catch (e) {
  console.log('外部捕获', e);
}
// 外部捕获 a
```

throw方法被捕获以后，会附带执行下一条yield语句。也就是说，会附带执行一次next方法：
```javascript
var gen = function* gen(){
  try {
    yield console.log('a');
  } catch (e) {
    // ...
  }
  yield console.log('b');
  yield console.log('c');
}

var g = gen();
g.next() // a
g.throw() // b
g.next() // c
```

Generator函数体内抛出的错误，也可以被函数体外的catch捕获：
```javascript
function *foo() {
  var x = yield 3;
  var y = x.toUpperCase();
  yield y;
}

var it = foo();

it.next(); // { value:3, done:false }

try {
  it.next(42);
} catch (err) {
  console.log(err);
}
```

一旦Generator执行过程中抛出错误，且没有被内部捕获，就不会再执行下去了。如果此后还调用next方法，将返回一个value属性等于undefined、done属性等于true的对象，即JavaScript引擎认为这个Generator已经运行结束了。


### Generator.prototype.return()
Generator函数返回的遍历器对象，还有一个return方法，可以返回给定的值，并且终结遍历Generator函数。
```javascript
function* gen() {
  yield 1;
  yield 2;
  yield 3;
}

var g = gen();

g.next()        // { value: 1, done: false }
g.return('foo') // { value: "foo", done: true }
g.next()        // { value: undefined, done: true }
```

如果Generator函数内部有try...finally代码块，那么return方法会推迟到finally代码块执行完再执行:
```javascript
function* numbers () {
  yield 1;
  try {
    yield 2;
    yield 3;
  } finally {
    yield 4;
    yield 5;
  }
  yield 6;
}
var g = numbers()
g.next() // { done: false, value: 1 }
g.next() // { done: false, value: 2 }
g.return(7) // { done: false, value: 4 }
g.next() // { done: false, value: 5 }
g.next() // { done: true, value: 7 }
```

### yield*语句
如果在Generater函数内部，调用另一个Generator函数，默认情况下是没有效果的。yield*语句，用来在一个Generator函数里面执行另一个Generator函数。
```javascript
function* foo() {
  yield 'a';
  yield 'b';
}

function* bar() {
  yield 'x';
  yield* foo();
  yield 'y';
}

// 等同于
function* bar() {
  yield 'x';
  for (let v of foo()) {
    yield v;
  }
  yield 'y';
}

for (let v of bar()){
  console.log(v);
}
// "x"
// "a"
// "b"
// "y"
```
yield*后面的Generator函数（没有return语句时），不过是for...of的一种简写形式，完全可以用后者替代前者。有return语句时，则需要用var value = yield* iterator的形式获取return语句的值。实际上，任何数据结构只要有Iterator接口，就可以被yield*遍历。

### 作为对象属性的Generator函数
如果一个对象的属性是Generator函数，可以简写成下面的形式：
```javascript
let obj = {
  * myGeneratorMethod() {
    ···
  }
};
```

### Generator函数的this
（略）

### Generator与协程
一个线程（或函数）执行到一半，可以暂停执行，将执行权交给另一个线程（或函数），等到稍后收回执行权的时候，再恢复执行。这种可以并行执行、交换执行权的线程（或函数），就称为协程。它与普通的线程很相似，都有自己的执行上下文、可以分享全局变量。它们的不同之处在于，同一时间可以有多个线程处于运行状态，但是运行的协程只能有一个，其他协程都处于暂停状态。此外，普通的线程是抢先式的，到底哪个线程优先得到资源，必须由运行环境决定，但是协程是合作式的，执行权由协程自己分配。
从实现上看，在内存中，子例程只使用一个栈（stack），而协程是同时存在多个栈，但只有一个栈是在运行状态，也就是说，协程是以多占用内存为代价，实现多任务的并行。

Generator函数是ECMAScript 6对协程的实现，但属于不完全实现。Generator函数被称为“半协程”（semi-coroutine），意思是只有Generator函数的调用者，才能将程序的执行权还给Generator函数。如果是完全执行的协程，任何函数都可以让暂停的协程继续执行。
如果将Generator函数当作协程，完全可以将多个需要互相协作的任务写成Generator函数，它们之间使用yield语句交换控制权。

### Generator的应用
（略）


## 第16章 Promise对象
ES6原生提供了Promise对象。
Promise对象有以下两个特点。
1. 对象的状态不受外界影响。Promise对象代表一个异步操作，有三种状态：Pending（进行中）、Resolved（已完成，又称Fulfilled）和Rejected（已失败）。只有异步操作的结果，可以决定当前是哪一种状态，任何其他操作都无法改变这个状态。这也是Promise这个名字的由来，它的英语意思就是“承诺”，表示其他手段无法改变。
2. 一旦状态改变，就不会再变，任何时候都可以得到这个结果。Promise对象的状态改变，只有两种可能：从Pending变为Resolved和从Pending变为Rejected。只要这两种情况发生，状态就凝固了，不会再变了，会一直保持这个结果。就算改变已经发生了，你再对Promise对象添加回调函数，也会立即得到这个结果。这与事件（Event）完全不同，事件的特点是，如果你错过了它，再去监听，是得不到结果的。
有了Promise对象，就可以将异步操作以同步操作的流程表达出来，避免了层层嵌套的回调函数。此外，Promise对象提供统一的接口，使得控制异步操作更加容易。
Promise也有一些缺点。首先，无法取消Promise，一旦新建它就会立即执行，无法中途取消。其次，如果不设置回调函数，Promise内部抛出的错误，不会反应到外部。第三，当处于Pending状态时，无法得知目前进展到哪一个阶段（刚刚开始还是即将完成）。
Promise构造函数接受一个函数作为参数，该函数的两个参数分别是resolve和reject：
```javascript
var promise = new Promise(function(resolve, reject) {
  // ... some code

  if (/* 异步操作成功 */){
    resolve(value);
  } else {
    reject(error);
  }
});
```
resolve函数的作用是，将Promise对象的状态从“未完成”变为“成功”（即从Pending变为Resolved），在异步操作成功时调用，并将异步操作的结果，作为参数传递出去；reject函数的作用是，将Promise对象的状态从“未完成”变为“失败”（即从Pending变为Rejected），在异步操作失败时调用，并将异步操作报出的错误，作为参数传递出去。

Promise实例生成以后，可以用then方法分别指定Resolved状态和Reject状态的回调函数：
```javascript
promise.then(function(value) {
  // success
}, function(error) {
  // failure
});
```
Promise新建后就会立即执行，然后，then方法指定的回调函数，将在当前脚本所有同步任务执行完才会执行，所以“Resolved”最后输出。

reject函数的参数通常是Error对象的实例，表示抛出的错误；resolve函数的参数除了正常的值以外，还可能是另一个Promise实例，表示异步操作的结果有可能是一个值，也有可能是另一个异步操作：
```javascript
var p1 = new Promise(function (resolve, reject) {
  setTimeout(() => reject(new Error('fail')), 3000)
})

var p2 = new Promise(function (resolve, reject) {
  setTimeout(() => resolve(p1), 1000)
})

p2
  .then(result => console.log(result))
  .catch(error => console.log(error))
// Error: fail

```
这时p1的状态就会传递给p2，也就是说，p1的状态决定了p2的状态。如果p1的状态是Pending，那么p2的回调函数就会等待p1的状态改变；如果p1的状态已经是Resolved或者Rejected，那么p2的回调函数将会立刻执行.

### Promise.prototype.then()
它的作用是为Promise实例添加状态改变时的回调函数。then方法返回的是一个新的Promise实例（不是原来那个Promise实例）。因此可以采用链式写法，即then方法后面再调用另一个then方法:
```javascript
getJSON("/posts.json").then(function(json) {
  return json.post;
}).then(function(post) {
  // ...
});
```

### Promise.prototype.catch()
Promise.prototype.catch方法是.then(null, rejection)的别名，用于指定发生错误时的回调函数。
```javascript
getJSON("/posts.json").then(function(posts) {
  // ...
}).catch(function(error) {
  // 处理 getJSON 和 前一个回调函数运行时发生的错误
  console.log('发生错误！', error);
});
```
Promise对象的错误具有“冒泡”性质，会一直向后传递，直到被捕获为止。也就是说，错误总是会被下一个catch语句捕获。
建议总是使用catch方法，而不使用then方法的第二个参数。

跟传统的try/catch代码块不同的是，如果没有使用catch方法指定错误处理的回调函数，Promise对象抛出的错误不会传递到外层代码，即不会有任何反应。

### Promise.all()
Promise.all方法用于将多个Promise实例，包装成一个新的Promise实例。
var p = Promise.all([p1, p2, p3]);  // p1、p2、p3都是Promise对象的实例，如果不是，就会先调用Promise.resolve方法，将参数转为Promise实例，再进一步处理

p的状态由p1、p2、p3决定，分成两种情况:
1. 只有p1、p2、p3的状态都变成fulfilled，p的状态才会变成fulfilled，此时p1、p2、p3的返回值组成一个数组，传递给p的回调函数。
2. 只要p1、p2、p3之中有一个被rejected，p的状态就变成rejected，此时第一个被reject的实例的返回值，会传递给p的回调函数。
```javascript
// 生成一个Promise对象的数组
var promises = [2, 3, 5, 7, 11, 13].map(function (id) {
  return getJSON("/post/" + id + ".json");
});

Promise.all(promises).then(function (posts) {
  // ...
}).catch(function(reason){
  // ...
});
```

### Promise.race()
Promise.race方法同样是将多个Promise实例，包装成一个新的Promise实例:
var p = Promise.race([p1,p2,p3]);
只要p1、p2、p3之中有一个实例率先改变状态，p的状态就跟着改变。那个率先改变的Promise实例的返回值，就传递给p的回调函数。
```javascript
// 处理超时
var p = Promise.race([
  fetch('/resource-that-may-take-a-while'),
  new Promise(function (resolve, reject) {
    setTimeout(() => reject(new Error('request timeout')), 5000)
  })
])
p.then(response => console.log(response))
p.catch(error => console.log(error))
```

### Promise.resolve()
将现有对象转为Promise对象。参数分成四种情况：
1. 参数是一个Promise实例
如果参数是Promise实例，那么Promise.resolve将不做任何修改、原封不动地返回这个实例。

2. 参数是一个thenable对象
thenable对象指的是具有then方法的对象，Promise.resolve会将这个对象转为Promise对象，然后就立即执行thenable对象的then方法。

3. 参数不是具有then方法的对象，或根本就不是对象
Promise.resolve返回一个新的Promise对象，状态为Resolved。

4. 不带有任何参数
直接返回一个Resolved状态的Promise对象。所以，如果希望得到一个Promise对象，比较方便的方法就是直接调用Promise.resolve方法。

需要注意的是，立即resolve的Promise对象，是在本轮“事件循环”（event loop）的结束时，而不是在下一轮“事件循环”的开始时：
```javascript
setTimeout(function () {
  console.log('three');
}, 0);

Promise.resolve().then(function () {
  console.log('two');
});

console.log('one');

// one
// two
// three
```

### Promise.reject() 
Promise.reject(reason)方法也会返回一个新的Promise实例，该实例的状态为rejected。它的参数用法与Promise.resolve方法完全一致。

### done() 
(略)

### finally()
(略)

### Generator函数与Promise的结合
(略)


## 第17章 异步操作和Async函数

### 回调函数、Promise
（略） 

### Generator函数执行异步任务
```javascript
var fetch = require('node-fetch');

function* gen(){
  var url = 'https://api.github.com/users/github';
  var result = yield fetch(url);
  console.log(result.bio);
}

var g = gen();
var result = g.next();

result.value.then(function(data){
  return data.json();
}).then(function(data){
  g.next(data);
});
```

### Thunk函数
```javascript
var x = 1;

function f(m){
  return m * 2;
}

f(x + 5)
```

`传值调用`：即在进入函数体之前，就计算x + 5的值（等于6），再将这个值传入函数f 。C语言就采用这种策略。
```javascript
f(x + 5)
// 传值调用时，等同于
f(6)
```

`传名调用`:即直接将表达式x + 5传入函数体，只在用到它的时候求值。Haskell语言采用这种策略。
```javascript
f(x + 5)
// 传名调用时，等同于
(x + 5) * 2
```

编译器的"传名调用"实现，往往是将参数放到一个临时函数之中，再将这个临时函数传入函数体。这个临时函数就叫做Thunk函数:

```javascript
function f(m){
  return m * 2;
}

f(x + 5);

// 等同于

var thunk = function () {
  return x + 5;
};

function f(thunk){
  return thunk() * 2;
}
```
凡是用到原参数的地方，对Thunk函数求值即可。这就是Thunk函数的定义，它是"传名调用"的一种实现策略，用来替换某个表达式。

### JavaScript语言的Thunk函数
在JavaScript语言中，Thunk函数替换的不是表达式，而是多参数函数，将其替换成单参数的版本，且只接受回调函数作为参数。
```javascript
// 正常版本的readFile（多参数版本）
fs.readFile(fileName, callback);

// Thunk版本的readFile（单参数版本）
var readFileThunk = Thunk(fileName);
readFileThunk(callback);

var Thunk = function (fileName){
  return function (callback){
    return fs.readFile(fileName, callback);
  };
};
```
任何函数，只要参数有回调函数，就能写成Thunk函数的形式：
```javascript
// Thunk函数转换器
var Thunk = function(fn) {
  return function (...args) {
    return function (callback) {
      return fn.call(this, ...args, callback);
    }
  };
};
```

### Thunkify模块
$ npm install thunkify
```javascript
var thunkify = require('thunkify');
var fs = require('fs');

var read = thunkify(fs.readFile);
read('package.json')(function(err, str){
  // ...
});
```

### Generator 函数的流程管理
ES6有了Generator函数，Thunk函数现在可以用于Generator函数的自动流程管理:
```javascript
function run(fn) {
  var gen = fn();

  function next(err, data) {
    var result = gen.next(data);
    if (result.done) return;
    result.value(next);
  }

  next();
}

function* g() {
  // ...
}

run(g);
```

### co模块

### async函数
async函数就是Generator函数的语法糖。
```javascript
var fs = require('fs');

var readFile = function (fileName) {
  return new Promise(function (resolve, reject) {
    fs.readFile(fileName, function(error, data) {
      if (error) reject(error);
      resolve(data);
    });
  });
};

var gen = function* (){
  var f1 = yield readFile('/etc/fstab');
  var f2 = yield readFile('/etc/shells');
  console.log(f1.toString());
  console.log(f2.toString());
};
```
写成async函数:
```javascript
var asyncReadFile = async function (){
  var f1 = await readFile('/etc/fstab');
  var f2 = await readFile('/etc/shells');
  console.log(f1.toString());
  console.log(f2.toString());
};
```
async函数对 Generator 函数的改进，体现在以下四点。
1. 内置执行器。Generator函数的执行必须靠执行器，所以才有了co模块，而async函数自带执行器。也就是说，async函数的执行，与普通函数一模一样，只要一行。
  var result = asyncReadFile();
上面的代码调用了asyncReadFile函数，然后它就会自动执行，输出最后结果。这完全不像Generator函数，需要调用next方法，或者用co模块，才能得到真正执行，得到最后结果。

2. 更好的语义。async和await，比起星号和yield，语义更清楚了。async表示函数里有异步操作，await表示紧跟在后面的表达式需要等待结果。

3. 更广的适用性。 co模块约定，yield命令后面只能是Thunk函数或Promise对象，而async函数的await命令后面，可以是Promise对象和原始类型的值（数值、字符串和布尔值，但这时等同于同步操作）。

4. 返回值是Promise。async函数的返回值是Promise对象，这比Generator函数的返回值是Iterator对象方便多了。可以用then方法指定下一步的操作。

进一步说，async函数完全可以看作多个异步操作，包装成的一个Promise对象，而await命令就是内部then命令的语法糖。

### async函数语法
1. async函数返回一个Promise对象
async函数内部return语句返回的值，会成为then方法回调函数的参数:
```javascript
async function f() {
  return 'hello world';
}

f().then(v => console.log(v))
// "hello world"
```
async函数内部抛出错误，会导致返回的Promise对象变为reject状态。抛出的错误对象会被catch方法回调函数接收到。

2. async函数返回的Promise对象，必须等到内部所有await命令的Promise对象执行完，才会发生状态改变。也就是说，只有async函数内部的异步操作执行完，才会执行then方法指定的回调函数。

3. 正常情况下，await命令后面是一个Promise对象。如果不是，会被转成一个立即resolve的Promise对象。

4. 如果await后面的异步操作出错，那么等同于async函数返回的Promise对象被reject。

### async函数的实现
async 函数的实现，就是将 Generator 函数和自动执行器，包装在一个函数里：
```javascript
async function fn(args){
  // ...
}

// 等同于

function fn(args){
  return spawn(function*() {
    // ...
  });
}
```
所有的async函数都可以写成上面的第二种形式，其中的 spawn 函数就是自动执行器。

-------------
（略）


## 第18章 Class
基本上，ES6的class可以看作只是一个语法糖，它的绝大部分功能，ES5都可以做到，新的class写法只是让对象原型的写法更加清晰、更像面向对象编程的语法而已。ES6的类，完全可以看作构造函数的另一种写法：
```javascript
class Point {
  // ...
}

typeof Point // "function"
Point === Point.prototype.constructor // true
```
使用的时候，也是直接对类使用new命令，跟构造函数的用法完全一致。

构造函数的prototype属性，在ES6的“类”上面继续存在。事实上，类的所有方法都定义在类的prototype属性上面。
```javascript
class Point {
  constructor(){
    // ...
  }

  toString(){
    // ...
  }

  toValue(){
    // ...
  }
}

// 等同于

Point.prototype = {
  toString(){},
  toValue(){}
};
```
在类的实例上面调用方法，其实就是调用原型上的方法：
```
class B {}
let b = new B();

b.constructor === B.prototype.constructor // true
```
prototype对象的constructor属性，直接指向“类”的本身，这与ES5的行为是一致的：
```javascript
Point.prototype.constructor === Point // true
```

类的内部所有定义的方法，都是不可枚举的（non-enumerable）。这一点与ES5的行为不一致：
```javascript
// ES 6
class Point {
  constructor(x, y) {
    // ...
  }

  toString() {
    // ...
  }
}

Object.keys(Point.prototype)
// []
Object.getOwnPropertyNames(Point.prototype)
// ["constructor","toString"]


// ES 5
var Point = function (x, y) {
  // ...
};

Point.prototype.toString = function() {
  // ...
};

Object.keys(Point.prototype)
// ["toString"]
Object.getOwnPropertyNames(Point.prototype)
// ["constructor","toString"]
```

### constructor方法
constructor方法默认返回实例对象（即this），完全可以指定返回另外一个对象。
类的构造函数，不使用new是没法调用的，会报错。这是它跟普通构造函数的一个主要区别，后者不用new也可以执行。

### 类的实例对象
与ES5一样，实例的属性除非显式定义在其本身（即定义在this对象上），否则都是定义在原型上（即定义在class上）。
```javascript
//定义类
class Point {

  constructor(x, y) {
    this.x = x;
    this.y = y;
  }

  toString() {
    return '(' + this.x + ', ' + this.y + ')';
  }

}

var point = new Point(2, 3);

point.toString() // (2, 3)

point.hasOwnProperty('x') // true
point.hasOwnProperty('y') // true
point.hasOwnProperty('toString') // false  !!!
point.__proto__.hasOwnProperty('toString') // true
```

与ES5一样，类的所有实例共享一个原型对象：
```javascript
var p1 = new Point(2,3);
var p2 = new Point(3,2);

p1.__proto__ === p2.__proto__
//true
```

不存在变量提升：
```javascript
new Foo(); // ReferenceError
class Foo {}
```

与函数一样，类也可以使用表达式的形式定义：
```javascript
const MyClass = class {
  getClassName() {
    return Me.name;
  }
};
```

ES6的类不提供私有方法，可以利用Symbol来模拟：
```javascript
const bar = Symbol('bar');
const snaf = Symbol('snaf');

export default class myClass{

  // 公有方法
  foo(baz) {
    this[bar](baz);
  }

  // 私有方法
  [bar](baz) {
    return this[snaf] = baz;
  }

  // ...
};
```

### this的指向
类的方法内部如果含有this，它默认指向类的实例，单独使用方法时容易出错：
```javascript
class Logger {
  printName(name = 'there') {
    this.print(`Hello ${name}`);
  }

  print(text) {
    console.log(text);
  }
}

const logger = new Logger();
const { printName } = logger;
printName(); // TypeError: Cannot read property 'print' of undefined
```
应该使用箭头函数：
```javascript
class Logger {
  constructor() {
    this.printName = (name = 'there') => {
      this.print(`Hello ${name}`);
    };
  }

  // ...
}
```

类和模块的内部，默认就是严格模式。

### Class的继承 
```javascript
class ColorPoint extends Point {
  constructor(x, y, color) {
    super(x, y); // 调用父类的constructor(x, y)
    this.color = color;
  }

  toString() {
    return this.color + ' ' + super.toString(); // 调用父类的toString()
  }
}
```
ES5的继承，实质是先创造子类的实例对象this，然后再将父类的方法添加到this上面（Parent.apply(this)）。ES6的继承机制完全不同，实质是先创造父类的实例对象this（所以必须先调用super方法），然后再用子类的构造函数修改this。

### 类的prototype属性和__proto__属性
Class作为构造函数的语法糖，同时有prototype属性和__proto__属性，因此同时存在两条继承链。
1. 子类的__proto__属性，表示`构造函数的继承`，总是指向父类。
2. 子类prototype属性的__proto__属性，表示`方法的继承`，总是指向父类的prototype属性。
```javascript
class A {
}

class B extends A {
}

B.__proto__ === A // true
B.prototype.__proto__ === A.prototype // true
```
这样的结果是因为，类的继承是按照下面的模式实现的:
```javascript
class A {
}

class B {
}

// B的实例继承A的实例
Object.setPrototypeOf(B.prototype, A.prototype);

// B继承A的静态属性
Object.setPrototypeOf(B, A);
```
这两条继承链，可以这样理解：作为一个对象，子类（B）的原型（__proto__属性）是父类（A）；作为一个构造函数，子类（B）的原型（prototype属性）是父类的实例。

### Extends 的继承目标
只要是一个有prototype属性的函数，就能被继承。由于函数都有prototype属性（除了Function.prototype函数），因此可以是任意函数。

子类继承Object类：
```javascript
class A extends Object {
}

A.__proto__ === Object // true
A.prototype.__proto__ === Object.prototype // true
```
这种情况下，A其实就是构造函数Object的复制，A的实例就是Object的实例。

不存在任何继承:
```javascript
class A {
}

A.__proto__ === Function.prototype // true
A.prototype.__proto__ === Object.prototype // true
```
这种情况下，A作为一个基类（即不存在任何继承），就是一个普通函数，所以直接继承Funciton.prototype。但是，A调用后返回一个空对象（即Object实例），所以A.prototype.__proto__指向构造函数（Object）的prototype属性。


子类继承null：
```javascript
class A extends null {
}

A.__proto__ === Function.prototype // true
A.prototype.__proto__ === undefined // true
```

### Object.getPrototypeOf() 
Object.getPrototypeOf方法可以用来从子类上获取父类。
```
Object.getPrototypeOf(ColorPoint) === Point
// true
```
因此，可以使用这个方法判断，一个类是否继承了另一个类。

### super关键字
super这个关键字，有两种用法，含义不同。
1. 作为函数调用时（即super(...args)），super代表父类的构造函数。
2. 作为对象调用时（即super.prop或super.method()），super代表父类。注意，此时super即可以引用父类实例的属性和方法，也可以引用父类的静态方法。

### 实例的__proto__属性
子类实例的__proto__属性的__proto__属性，指向父类实例的__proto__属性。也就是说，子类的原型的原型，是父类的原型。

### 原生构造函数的继承
ECMAScript的原生构造函数大致有下面这些：
Boolean()
Number()
String()
Array()
Date()
Function()
RegExp()
Error()
Object()
ES6允许继承原生构造函数定义子类，因为ES6是先新建父类的实例对象this，然后再用子类的构造函数修饰this，使得父类的所有行为都可以继承。

### Class的取值函数（getter）和存值函数（setter）
与ES5一样，在Class内部可以使用get和set关键字，对某个属性设置存值函数和取值函数，拦截该属性的存取行为。

### Class的静态方法
类相当于实例的原型，所有在类中定义的方法，都会被实例继承。如果在一个方法前，加上static关键字，就表示该方法不会被实例继承，而是直接通过类来调用，这就称为“静态方法”。
```javascript
class Foo {
  static classMethod() {
    return 'hello';
  }
}

Foo.classMethod() // 'hello'

var foo = new Foo();
foo.classMethod()
// TypeError: foo.classMethod is not a function
```

### Class的静态属性和实例属性
ES6明确规定，Class内部只有静态方法，没有静态属性。
目前只有这种方法可行：
```javascript
class Foo {
}

Foo.prop = 1;
Foo.prop // 1
```

### new.target属性 
new是从构造函数生成实例的命令。ES6为new命令引入了一个new.target属性，（在构造函数中）返回new命令作用于的那个构造函数。如果构造函数不是通过new命令调用的，new.target会返回undefined，因此这个属性可以用来确定构造函数是怎么调用的。需要注意的是，子类继承父类时，new.target会返回子类。

### Mixin模式的实现
(略)

## 第19章 修饰器
修饰器（Decorator）是一个函数，用来修改类的行为。这是ES7的一个提案。修饰器对类的行为的改变，是代码编译时发生的，而不是在运行时。这意味着，修饰器能在编译阶段运行代码：
```javascript
function testable(target) {
  target.isTestable = true;
}

@testable
class MyTestableClass {}

console.log(MyTestableClass.isTestable) // true
```
修饰器本质就是编译时执行的函数。修饰器函数的第一个参数，就是所要修饰的目标类。


可以在修饰器外面再封装一层函数来为修饰器增加参数：
```javascript
function testable(isTestable) {
  return function(target) {
    target.isTestable = isTestable;
  }
}

@testable(true)
class MyTestableClass {}
MyTestableClass.isTestable // true

@testable(false)
class MyClass {}
MyClass.isTestable // false
```

### 方法的修饰

修饰器函数一共可以接受三个参数，第一个参数是所要修饰的目标对象，第二个参数是所要修饰的属性名，第三个参数是该属性的描述对象。
```javascript
class Person {
  @nonenumerable
  get kidCount() { return this.children.length; }
}

function nonenumerable(target, name, descriptor) {
  descriptor.enumerable = false;
  return descriptor;
}
```

### 为什么修饰器不能用于函数？
修饰器只能用于类和类的方法，不能用于函数，因为存在函数提升。
```javascript
var counter = 0;

var add = function () {
  counter++;
};

@add
function foo() {
}
```
因为函数提升，使得实际执行的代码是下面这样：
```javascript
var counter;
var add;

@add
function foo() {
}

counter = 0;

add = function () {
  counter++;
};
```

### core-decorators.js
（略）


### 使用修饰器实现自动发布事件
（略）

### Mixin
（略）

### Trait
（略）


## 第20章 Module
在ES6之前，社区制定了一些模块加载方案，最主要的有CommonJS和AMD两种。前者用于服务器，后者用于浏览器。ES6在语言规格的层面上，实现了模块功能，而且实现得相当简单，完全可以取代现有的CommonJS和AMD规范，成为浏览器和服务器通用的模块解决方案。

ES6模块的设计思想，是尽量的静态化，使得编译时就能确定模块的依赖关系，以及输入和输出的变量。`CommonJS和AMD模块，都只能在运行时确定这些东西`。比如，CommonJS模块就是对象，输入时必须查找对象属性。

```javascript
// CommonJS模块
let { stat, exists, readFile } = require('fs');

// 等同于
let _fs = require('fs');
let stat = _fs.stat, exists = _fs.exists, readfile = _fs.readfile;
```
上面代码的实质是整体加载fs模块（即加载fs的所有方法），生成一个对象（_fs），然后再从这个对象上面读取3个方法。这种加载称为“运行时加载”，因为只有运行时才能得到这个对象，导致完全没办法在编译时做“静态优化”。

ES6模块不是对象，而是通过export命令显式指定输出的代码，输入时也采用静态命令的形式。
```javascript
// ES6模块
import { stat, exists, readFile } from 'fs';
```
上面代码的实质是从fs模块加载3个方法，其他方法不加载。这种加载称为`“编译时加载”，即ES6可以在编译时就完成模块加载，效率要比CommonJS模块的加载方式高`。由于ES6模块是编译时加载，使得静态分析成为可能。

浏览器使用ES6模块的语法如下:
```javascript
<script type="module" src="foo.js"></script>
```

### ES6的模块自动采用严格模式
严格模式主要有以下限制:
变量必须声明后再使用
函数的参数不能有同名属性，否则报错
不能使用with语句
不能对只读属性赋值，否则报错
不能使用前缀0表示八进制数，否则报错
不能删除不可删除的属性，否则报错
不能删除变量delete prop，会报错，只能删除属性delete global[prop]
eval不会在它的外层作用域引入变量
eval和arguments不能被重新赋值
arguments不会自动反映函数参数的变化
不能使用arguments.callee
不能使用arguments.caller
禁止this指向全局对象
不能使用fn.caller和fn.arguments获取函数调用的堆栈
增加了保留字（比如protected、static和interface）

### export命令
export命令用于规定模块的对外接口。一个模块就是一个独立的文件。该文件内部的所有变量，外部无法获取。如果希望外部能够读取模块内部的某个变量，就必须使用export关键字输出该变量。
```javascript
// profile.js
var firstName = 'Michael';
var lastName = 'Jackson';
var year = 1958;

export {firstName, lastName, year};
```
通常情况下，export输出的变量就是本来的名字，但是可以使用as关键字重命名。

需要特别注意的是，export命令规定的是对外的接口，必须与模块内部的变量建立一一对应关系:
```javascript
// 写法一
export var m = 1;

// 写法二
var m = 1;
export {m};

// 写法三
var n = 1;
export {n as m};
```
它们的实质是，在接口名与模块内部变量之间，建立了一一对应的关系。
export语句输出的接口，与其对应的值是动态绑定关系，即`通过该接口，可以取到模块内部实时的值`。
这一点与CommonJS规范完全不同。CommonJS模块输出的是值的缓存，不存在动态更新。
export命令可以出现在模块的任何位置，只要处于模块顶层就可以。如果处于块级作用域内，就会报错，下一节的import命令也是如此。这是因为处于条件代码块之中，就没法做静态优化了，违背了ES6模块的设计初衷。

### import命令
import命令接受一个对象（用大括号表示），里面指定要从其他模块导入的变量名。大括号里面的变量名，必须与被导入模块对外接口的名称相同。如果想为输入的变量重新取一个名字，import命令要使用as关键字，将输入的变量重命名。

注意，import命令具有提升效果，会提升到整个模块的头部，首先执行：
```javascript
foo(); // 不会报错，因为import的执行早于foo的调用

import { foo } from 'my_module';
```

### 模块的整体加载
```javascript
import * as circle from './circle';

console.log('圆面积：' + circle.area(4));
console.log('圆周长：' + circle.circumference(14));
```


### export default命令
```javascript
// export-default.js
export default function () {
  console.log('foo');
}

// import-default.js
import customName from './export-default';
customName(); // 'foo'
```

export default命令用于指定模块的默认输出。本质上，export default就是输出一个叫做default的变量或方法，然后系统允许导入时为它取任意名字。

export default也可以用来输出类:
```javascript
// MyClass.js
export default class { ... }

// main.js
import MyClass from 'MyClass';
let o = new MyClass();
```

### 模块的继承
```javascript
export * from 'circle';  // 继承了circle模块
export var e = 2.71828182846;
export default function(x) {
  return Math.exp(x);
}
```
export *命令会忽略circle模块的default方法。然后，上面代码又输出了自定义的e变量和默认方法。

### ES6模块加载的实质
CommonJS模块输出的是一个值的拷贝，而ES6模块输出的是值的引用。
ES6模块的运行机制与CommonJS不一样，它遇到模块加载命令import时，不会去执行模块，而是只生成一个动态的只读引用。等到真的需要用到时，再到模块里面去取值，换句话说，ES6的输入有点像Unix系统的“符号连接”，原始值变了，import输入的值也会跟着变。因此，ES6模块是动态引用，并且不会缓存值，模块里面的变量绑定其所在的模块。

export通过接口，输出的是同一个值。不同的脚本加载这个接口，得到的都是同样的实例。
```javascript
// mod.js
function C() {
  this.sum = 0;
  this.add = function () {
    this.sum += 1;
  };
  this.show = function () {
    console.log(this.sum);
  };
}

export let c = new C();

// x.js
import {c} from './mod';
c.add();

// y.js
import {c} from './mod';
c.show();

// main.js
import './x';
import './y';
```
$ babel-node main.js
1

### 循环加载
CommonJS模块的重要特性是加载时执行，即脚本代码在require的时候，就会全部执行。一旦出现某个模块被"循环加载"，就只输出已经执行的部分，还未执行的部分不会输出。

ES6处理“循环加载”与CommonJS有本质的不同。ES6模块是动态引用，如果使用import从一个模块加载变量（即import foo from 'foo'），那些变量不会被缓存，而是成为一个指向被加载模块的引用，需要开发者自己保证，真正取值的时候能够取到值。(即，可能会由于循环加载导致取到的值为undefined)






















