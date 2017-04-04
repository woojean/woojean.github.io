# ECMAScript有哪些数据类型？各有什么样的取值？

ECMAScript 中有 5 种简单数据类型（也称为基本数据类型）： Undefined 、 Null 、 Boolean 、 Number
和 String 。还有 1种复杂数据类型—— Object ， Object 本质上是由一组无序的名值对组成的。ECMAScript
不支持任何创建自定义类型的机制，而所有值最终都将是上述 6 种数据类型之一。

Undefined 类型只有一个值，即特殊的 undefined 。在使用 var 声明变量但未对其加以初始化时，
这个变量的值就是 undefined ，例如：
var message;
alert(message == undefined); //true
不过，包含 undefined 值的变量与尚未定义的变量还是不一样的：
var message; // 这个变量声明之后默认取得了 undefined 值
// 下面这个变量并没有声明
// var age
alert(message); // "undefined"
alert(age); // 产生错误
然而，令人困惑的是：对未初始化的变量执行 typeof 操作符会返回 undefined 值，而对未声明的变量执行 typeof 操作符同样也会返回 undefined 值：
var message; // 这个变量声明之后默认取得了 undefined 值
// 下面这个变量并没有声明
// var age
alert(typeof message); // "undefined"
alert(typeof age); // "undefined"


Null 类型也是只有一个值的数据类型，这个特殊的值是 null 。从逻辑角度来看， null 值表
示一个空对象指针，而这也正是使用 typeof 操作符检测 null 值时会返回 "object" 的原因，如下面
的例子所示：
var car = null;
alert(typeof car); // "object"

实际上， undefined 值是派生自 null 值的，因此 ECMA-262规定对它们的相等性测试要返回 true ：
alert(null == undefined); //true
要注意的是，这个
操作符出于比较的目的会转换其操作数

尽管 null 和 undefined 有这样的关系，但它们的用途完全不同。如前所述，无论在什么情况下
都没有必要把一个变量的值显式地设置为 undefined ，可是同样的规则对 null 却不适用。换句话说，
只要意在保存对象的变量还没有真正保存对象，就应该明确地让该变量保存 null 值。这样做不仅可以
体现 null 作为空对象指针的惯例，而且也有助于进一步区分 null 和 undefined 。

Boolean 类型的字面值 true 和 false 是区分大小写的。也就是说， True 和 False
（以及其他的混合大小写形式）都不是 Boolean 值，只是标识符。