# 《ECMAScript 6入门》读书笔记

## 第1章 ECMAScript 6简介

### Babel转码器
#### .babelrc配置文件
放在项目根目录下，用来配置转码规则和插件，如：
```
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
```
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
```
<script src="node_modules/babel-core/browser.js"></script>
<script type="text/babel">
// ES6 code
</script>
```
直接在浏览器中进行转码性能太差，可以配合browserify在服务器端把代码转换为浏览器可以直接执行的代码：
$ npm install --save-dev babelify babel-preset-es2015
$ browserify script.js -o bundle.js -t [ babelify --presets [ es2015 ] ]

可以在package.json中进行配置，这样就不用每次都在命令行输入参数了：
```
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

### 块级作用域
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

### const
const声明的变量不得改变值，一旦声明，就必须立即初始化：
```
const foo;
// SyntaxError: Missing initializer in const declaration
```

对于复合类型的变量，变量名不指向数据，而是指向数据所在的地址。const命令只是保证变量名指向的地址不变，并不保证该地址的数据不变：
```
const foo = {};
foo.prop = 123;

foo.prop
// 123

foo = {}; // TypeError: "foo" is read-only
```
如果真的想将对象冻结，应该使用`Object.freeze`方法：
```
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
```
var a = 1;
// 如果在Node的REPL环境，可以写成global.a
// 或者采用通用方法，写成this.a
window.a // 1

let b = 1;
window.b // undefined
```


## 第3章 变量的解构赋值

### 数组的解构赋值
```
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
```
// 报错
let [foo] = 1;
let [foo] = false;
let [foo] = NaN;
let [foo] = undefined;
let [foo] = null;
let [foo] = {};
```

如果默认值是一个表达式，那么这个表达式是惰性求值的：
```
// 因为x能取到值，所以函数f根本不会执行
function f() {
  console.log('aaa');
}

let [x = f()] = [1];
```

### 对象的解构赋值
对象的属性没有次序，变量必须与属性同名，才能取到正确的值：
```
var { bar, foo } = { foo: "aaa", bar: "bbb" };
foo // "aaa"
bar // "bbb"

var { baz } = { foo: "aaa", bar: "bbb" };
baz // undefined
```

对象的解构赋值是下面形式的简写：
```
var { foo: foo, bar: bar } = { foo: "aaa", bar: "bbb" };
```
即对象的解构赋值的内部机制，是先找到同名属性，然后再赋给对应的变量。真正被赋值的是后者，而不是前者。因此，如果变量名与属性名不一致，可以写成下面这样：
```
var { foo: baz } = { foo: 'aaa', bar: 'bbb' };
baz // "aaa"

let obj = { first: 'hello', last: 'world' };
let { first: f, last: l } = obj;
f // 'hello'
l // 'world'
```

解构也可以用于嵌套结构的对象：
```
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
```
var x;
{x} = {x: 1};
// SyntaxError: syntax error
```
上面代码的写法会报错，因为JavaScript引擎会将{x}理解成一个代码块，从而发生语法错误。`只有不将大括号写在行首，避免JavaScript将其解释为代码块`，才能解决这个问题。
```
({x} = {x: 1});
```

### 字符串的解构赋值
```
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
```
let {toString: s} = 123;
s === Number.prototype.toString // true

let {toString: s} = true;
s === Boolean.prototype.toString // true
```

解构赋值的规则是，只要等号右边的值不是对象，就先将其转为对象。`由于undefined和null无法转为对象，所以对它们进行解构赋值，都会报错`。
```
let { prop: x } = undefined; // TypeError
let { prop: y } = null; // TypeError
```

### 函数参数的解构赋值
```
function add([x, y]){
  return x + y;
}

add([1, 2]); // 3

[[1, 2], [3, 4]].map(([a, b]) => a + b);
// [ 3, 7 ]
```

undefined就会触发函数参数的默认值:
```
[1, undefined, 3].map((x = 'yes') => x);
// [ 1, 'yes', 3 ]
```


### 圆括号问题
建议只要有可能，就不要在模式中放置圆括号。
（不要自找麻烦）

### 用途
遍历Map结构:
```
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
```
const { SourceMapConsumer, SourceNode } = require("source-map");
```

## 第4章 字符串的扩展
JavaScript`允许采用\uxxxx形式表示一个字符`，其中“xxxx”表示字符的`码点`。但是，这种表示法只限于\u0000——\uFFFF之间的字符。超出这个范围的字符，必须用两个双字节的形式表达。
```
"\uD842\uDFB7"
// "𠮷"

"\u20BB7"  // 超过0xFFFF，ES5会理解成“\u20BB+7”，\u20BB是一个不可打印字符，所以只会显示一个空格
// " 7"
```

ES6对Unicode字符的表示做出了改进，只要将码点放入大括号，就能正确解读4字节字符：
```
"\u{20BB7}"
// "𠮷"
```

因此ES6共有6种方法可以表示一个字符：
```
'\z' === 'z'  // true
'\172' === 'z' // true
'\x7A' === 'z' // true
'\u007A' === 'z' // true
'\u{7A}' === 'z' // true
```


### 字符串的遍历器接口
ES6为字符串添加了遍历器接口，使得字符串可以被for...of循环遍历：
```
for (let codePoint of 'foo') {
  console.log(codePoint)
}
// "f"
// "o"
// "o"
```

### 模板字符串
模板字符串（template string）是增强版的字符串，用反引号（`）标识。它可以当作普通字符串使用，也可以用来定义多行字符串，或者在字符串中嵌入变量。
```
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
```
alert`123`
// 等同于
alert(123)
```
标签模板其实不是模板，而是函数调用的一种特殊形式。
如果模板字符里面有变量，会将模板字符串先处理成多个参数，再调用函数：
```
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
```
new RegExp(/abc/ig, 'i').flags
// "i"
```

### 字符串的正则方法
字符串对象共有4个方法，可以使用正则表达式：match()、replace()、search()和split()。


### u修饰符
ES6对正则表达式添加了u修饰符，用来正确处理大于\uFFFF的Unicode字符
```
/^\uD83D/u.test('\uD83D\uDC2A')
// false
/^\uD83D/.test('\uD83D\uDC2A')
// true
```

### y修饰符
ES6为正则表达式添加了y修饰符，叫做“粘连”（sticky）修饰符，y修饰符的作用与g修饰符类似，也是全局匹配，后一次匹配都从上一次匹配成功的下一个位置开始。不同之处在于，g修饰符只要剩余位置中存在匹配就可，而y修饰符确保匹配必须从剩余的第一个位置开始，这也就是“粘连”的涵义：
```
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
```
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
```
0b111110111 === 503 // true
0o767 === 503 // true
```

### Number.isFinite()
用来检查一个数值是否为有限的（finite）：
```
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
```
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
```
Number.parseInt === parseInt // true
Number.parseFloat === parseFloat // true
```

### Number.isInteger()
用来判断一个值是否为整数，在JavaScript内部，整数和浮点数是同样的储存方法，所以3和3.0被视为同一个值：
```
Number.isInteger(25) // true
Number.isInteger(25.0) // true
Number.isInteger(25.1) // false
Number.isInteger("15") // false
Number.isInteger(true) // false
```

### Number.EPSILON
新增的一个极小的常量：
```
Number.EPSILON
// 2.220446049250313e-16
Number.EPSILON.toFixed(20)
// '0.00000000000000022204'
```

### Number.isSafeInteger()
JavaScript能够准确表示的整数范围在-2^53到2^53之间（不含两个端点），超过这个范围，无法精确表示这个值。ES6引入了Number.MAX_SAFE_INTEGER和Number.MIN_SAFE_INTEGER这两个常量，用来表示这个范围的上下限。Number.isSafeInteger()则是用来判断一个整数是否落在这个范围之内。
实际使用这个函数时，需要注意。验证运算结果是否落在安全整数的范围内，不要只验证运算结果，而要同时验证参与运算的每个值。
```
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
```
2 ** 2 // 4
2 ** 3 // 8
```


## 第7章 数组的扩展

### Array.from()
用于将两类对象转为真正的数组：
1.类似数组的对象（array-like object）
2.可遍历（iterable）的对象（包括ES6新增的数据结构Set和Map，只要是部署了Iterator接口的数据结构，Array.from都能将其转为数组）
```
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
```
// arguments对象
function foo() {
  var args = [...arguments];
}

// NodeList对象
[...document.querySelectorAll('div')]
```
扩展运算符背后调用的是遍历器接口（Symbol.iterator），如果一个对象没有部署这个接口，就无法转换。Array.from方法则是还支持类似数组的对象。`所谓类似数组的对象，本质特征只有一点，即必须有length属性。因此，任何有length属性的对象，都可以通过Array.from方法转为数组，而此时扩展运算符就无法转换`。
```
Array.from({ length: 3 });
// [ undefined, undefined, undefined ]
```

Array.from还可以接受第二个参数，作用类似于数组的map方法，用来对每个元素进行处理，将处理后的值放入返回的数组:
```
Array.from(arrayLike, x => x * x);
// 等同于
Array.from(arrayLike).map(x => x * x);

Array.from([1, 2, 3], (x) => x * x)
// [1, 4, 9]
```

如果map函数里面用到了this关键字，还可以传入Array.from的第三个参数，用来绑定this。

### Array.of()
用于将一组值，转换为数组：
```
Array.of(3, 11, 8) // [3,11,8]
Array.of(3) // [3]
Array.of(3).length // 1

Array.of() // []
Array.of(undefined) // [undefined]
Array.of(1) // [1]
Array.of(1, 2) // [1, 2]
```
这个方法的主要目的，是弥补数组构造函数Array()的不足。因为参数个数的不同，会导致Array()的行为有差异（只有当参数个数不少于2个时，Array()才会返回由参数组成的新数组。参数个数只有一个时，实际上是指定数组的长度）：
```
Array() // []
Array(3) // [, , ,]
Array(3, 11, 8) // [3, 11, 8]
```

### 数组实例的copyWithin() 
在当前数组内部，将指定位置的成员复制到其他位置（会覆盖原有成员），然后返回当前数组。也就是说，使用这个方法，会修改当前数组。
Array.prototype.copyWithin(target, start = 0, end = this.length)
```
[1, 2, 3, 4, 5].copyWithin(0, 3)
// [4, 5, 3, 4, 5]
```

### 数组实例的find()和findIndex() 
find方法，用于找出第一个符合条件的数组成员。它的参数是一个回调函数，所有数组成员依次执行该回调函数，直到找出第一个返回值为true的成员，然后返回该成员。如果没有符合条件的成员，则返回undefined。
```
[1, 4, -5, 10].find((n) => n < 0)
// -5

[1, 5, 10, 15].find(function(value, index, arr) {
  return value > 9;
}) // 10
```

findIndex方法的用法与find方法非常类似，返回第一个符合条件的数组成员的位置，如果所有成员都不符合条件，则返回-1。
这两个方法都可以发现NaN，弥补了数组的IndexOf方法的不足。
```
[NaN].indexOf(NaN)
// -1

[NaN].findIndex(y => Object.is(NaN, y))
// 0
```

### 数组实例的fill()
使用给定值，填充一个数组
```
['a', 'b', 'c'].fill(7)
// [7, 7, 7]

new Array(3).fill(7)
// [7, 7, 7]

['a', 'b', 'c'].fill(7, 1, 2)
// ['a', 7, 'c']
```


### 数组实例的entries()，keys()和values()
ES6提供三个新的方法——entries()，keys()和values()——用于遍历数组。它们都返回一个遍历器对象，可以用for...of循环进行遍历，唯一的区别是keys()是对键名的遍历、values()是对键值的遍历，entries()是对键值对的遍历。
```
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
```
[1, 2, 3].includes(2);     // true
[1, 2, 3].includes(4);     // false
[1, 2, NaN].includes(NaN); // true
```

### 数组的空位
数组的空位指，数组的某一个位置没有任何值。比如，Array构造函数返回的数组都是空位：
```
Array(3) // [, , ,]
```
注意，空位不是undefined，一个位置的值等于undefined，依然是有值的：
```
0 in [undefined, undefined, undefined] // true
0 in [, , ,] // false
```

ES5对空位的处理很不一致，大多数情况下会忽略空位:
```
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

### 函数的length属性
指定了默认值以后，函数的length属性，将返回没有指定默认值的参数个数。
```
(function (a) {}).length // 1
(function (a = 5) {}).length // 0
(function (a, b, c = 5) {}).length // 2
```

### rest参数
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

### 扩展运算符
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

### name属性
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

### 箭头函数
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


### 函数绑定
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

### 尾调用优化
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

### 函数参数的尾逗号
ES7有一个提案，允许函数的最后一个参数有尾逗号（trailing comma）,这样，当以后需要再添加新参数时，这一行代码在版本控制中不会有改变：
```
function clownsEverywhere(
  param1,
  param2,
) { /* ... */ }
```


## 第9章 对象的扩展

### 属性的简洁表示法
ES6允许直接写入变量和函数，作为对象的属性和方法（允许在对象之中，只写属性名，不写属性值。这时，属性值等于属性名所代表的变量的值）：
```
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
```
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
```
// 报错
var foo = 'bar';
var bar = 'abc';
var baz = { [foo] };

// 正确
var foo = 'bar';
var baz = { [foo]: 'abc'};
```

### 方法的name属性
```
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
```
(new Function()).name // "anonymous"

var doSomething = function() {
  // ...
};
doSomething.bind().name // "bound doSomething"
```

如果对象的方法是一个Symbol值，那么name属性返回的是这个Symbol值的描述：
```
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
```
+0 === -0 //true
NaN === NaN // false

Object.is(+0, -0) // false
Object.is(NaN, NaN) // true
```

### Object.assign()
Object.assign方法用于对象的合并，将源对象（source）的所有可枚举属性，复制到目标对象（target）：
```
var target = { a: 1 };

var source1 = { b: 2 };
var source2 = { c: 3 };

Object.assign(target, source1, source2);
target // {a:1, b:2, c:3}
```
如果目标对象与源对象有同名属性，或多个源对象有同名属性，则后面的属性会覆盖前面的属性。
Object.assign拷贝的属性是有限制的，只拷贝源对象的自身属性（不拷贝继承属性），也不拷贝不可枚举的属性（enumerable: false）。
属性名为Symbol值的属性，也会被Object.assign拷贝。
```
Object.assign({ a: 'b' }, { [Symbol('c')]: 'd' })
// { a: 'b', Symbol(c): 'd' }
```
Object.assign方法实行的是浅拷贝，而不是深拷贝。也就是说，如果源对象某个属性的值是对象，那么目标对象拷贝得到的是这个对象的引用。

对于嵌套的对象，一旦遇到同名属性，Object.assign的处理方法是替换，而不是添加:
```
var target = { a: { b: 'c', d: 'e' } }
var source = { a: { b: 'hello' } }
Object.assign(target, source)
// { a: { b: 'hello' } }
```

Object.assign可以用来处理数组，但是会把数组视为对象：
```
Object.assign([1, 2, 3], [4, 5])
// [4, 5, 3]
```

### 属性的可枚举性
对象的每个属性都有一个描述对象（Descriptor），用来控制该属性的行为。`Object.getOwnPropertyDescriptor`方法可以获取该属性的描述对象。
```
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
1.for...in循环：只遍历对象自身的和继承的可枚举的属性
2.Object.keys()：返回对象自身的所有可枚举的属性的键名
3.JSON.stringify()：只串行化对象自身的可枚举的属性
ES6新增了一个操作Object.assign()，会忽略enumerable为false的属性，只拷贝对象自身的可枚举的属性。
这四个操作之中，只有for...in会返回继承的属性。
另外，ES6规定，所有Class的原型的方法都是不可枚举的。
```
Object.getOwnPropertyDescriptor(class {foo() {}}.prototype, 'foo').enumerable
// false
```

### 属性的遍历
ES6一共有5种方法可以遍历对象的属性。
1.`for...in`
for...in循环遍历对象自身的和继承的可枚举属性（不含Symbol属性）。

2.`Object.keys(obj)`
Object.keys返回一个数组，包括对象自身的（不含继承的）所有可枚举属性（不含Symbol属性）。

3.`Object.getOwnPropertyNames(obj)`
Object.getOwnPropertyNames返回一个数组，包含对象自身的所有属性（不含Symbol属性，但是包括不可枚举属性）。

4.`Object.getOwnPropertySymbols(obj)`
Object.getOwnPropertySymbols返回一个数组，包含对象自身的所有Symbol属性。

5.`Reflect.ownKeys(obj)`
Reflect.ownKeys返回一个数组，包含对象自身的所有属性，不管是属性名是Symbol或字符串，也不管是否可枚举。

以上的5种方法遍历对象的属性，都遵守同样的属性遍历的次序规则：
首先遍历所有属性名为`数值`的属性，按照数字排序。
其次遍历所有属性名为`字符串`的属性，按照生成时间排序。
最后遍历所有属性名为`Symbol值`的属性，按照生成时间排序。
```
Reflect.ownKeys({ [Symbol()]:0, b:0, 10:0, 2:0, a:0 })
// ['2', '10', 'b', 'a', Symbol()]
```

### __proto__属性，Object.setPrototypeOf()，Object.getPrototypeOf() 
__proto__属性用来读取或设置当前对象的prototype对象,在实现上，__proto__调用的是Object.prototype.__proto__。如果一个对象本身部署了__proto__属性，则该属性的值就是对象的原型。
```
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
```
let { x, y, ...z } = { x: 1, y: 2, a: 3, b: 4 };
x // 1
y // 2
z // { a: 3, b: 4 }
```
Rest解构赋值不会拷贝继承自原型对象的属性：
```
let o1 = { a: 1 };
let o2 = { b: 2 };
o2.__proto__ = o1;
let o3 = { ...o2 };
o3 // { b: 2 }
```

扩展运算符（...）用于取出参数对象的所有可遍历属性，拷贝到当前对象之中：
```
let z = { a: 3, b: 4 };
let n = { ...z };
n // { a: 3, b: 4 }
```
这等同于使用Object.assign方法。


### Object.getOwnPropertyDescriptors() 
ES7有一个提案，提出了Object.getOwnPropertyDescriptors方法，返回指定对象所有自身属性（非继承属性）的描述对象。

可以用来实现Mixin（混入）模式：
```
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

```
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
```
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
```
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
```
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
```
var s1 = Symbol.for('foo');
var s2 = Symbol.for('foo');

s1 === s2 // true
```
Symbol.for为Symbol值登记的名字，是全局环境的，可以在不同的iframe或service worker中取到同一个值。

Symbol.keyFor方法返回一个已登记的Symbol类型值的key：
```
var s1 = Symbol.for("foo");
Symbol.keyFor(s1) // "foo"

var s2 = Symbol("foo");
Symbol.keyFor(s2) // undefined
```

由于以Symbol值作为名称的属性，不会被常规方法遍历得到，可以利用这个特性，为对象定义一些非私有的、但又希望只用于内部的方法：
```
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
```
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
1.Symbol.hasInstance：foo instanceof Foo在语言内部，实际调用的是Foo[Symbol.hasInstance](foo)

2.Symbol.isConcatSpreadable：表示该对象使用Array.prototype.concat()时，是否可以展开
```
let arr1 = ['c', 'd'];
['a', 'b'].concat(arr1, 'e') // ['a', 'b', 'c', 'd', 'e']
arr1[Symbol.isConcatSpreadable] // undefined

let arr2 = ['c', 'd'];
arr2[Symbol.isConcatSpreadable] = false;
['a', 'b'].concat(arr2, 'e') // ['a', 'b', ['c','d'], 'e']
```

3.Symbol.species:指向一个方法。该对象作为构造函数创造实例时，会调用这个方法。即如果this.constructor[Symbol.species]存在，就会使用这个属性作为构造函数，来创造新的实例对象。

4.Symbol.match :指向一个函数。当执行str.match(myObject)时，如果该属性存在，会调用它，返回该方法的返回值

5.Symbol.replace:指向一个方法，当该对象被String.prototype.replace方法调用时，会返回该方法的返回值。

6.Symbol.search:指向一个方法，当该对象被String.prototype.search方法调用时，会返回该方法的返回值。

7.Symbol.split:指向一个方法，当该对象被String.prototype.split方法调用时，会返回该方法的返回值。

8.Symbol.iterator:指向该对象的默认遍历器方法
```
var myIterable = {};
myIterable[Symbol.iterator] = function* () {
  yield 1;
  yield 2;
  yield 3;
};

[...myIterable] // [1, 2, 3]
```

9.Symbol.toPrimitive：指向一个方法。该对象被转为原始类型的值时，会调用这个方法，返回该对象对应的原始类型值。

10.Symbol.toStringTag：指向一个方法。在该对象上面调用Object.prototype.toString方法时，如果这个属性存在，它的返回值会出现在toString方法返回的字符串之中，表示对象的类型。也就是说，这个属性可以用来定制[object Object]或[object Array]中object后面的那个字符串。ES6新增内置对象的Symbol.toStringTag属性值如下：
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

11.Symbol.unscopables：指向一个对象。该对象指定了使用with关键字时，哪些属性会被with环境排除。



















































