# Global对象有哪些属性和方法？

ECMAScript 中的 Global 对象在某种意义上是作为一个终极的“兜底儿对象”来定义的。换句话说，不属于任何其他对象的属性和方法，最终都是它的属性和方法。事实上，没有全局变量或全局函数；所有在全局作用域中定义的属性和函数，都是 Global 对象的属性。在所有代码执行之前，作用域中就已经存在两个内置对象： Global 和 Math

encodeURI()  // 对 URI进行编码，以便发送给浏览器，不会对本身属于 URI 的特殊字符进行编码，例如冒号、正斜杠、问号和井字号
encodeURIComponent() // 会对它发现的任何非标准字符进行编码
decodeURI()
decodeURIComponent()

escape() 和 unescape()已经被废弃，因为它们只能处理ASCII

eval() // 就像是一个完整的 ECMAScript 解析器，它只接受一个参数，即要执行的 ECMAScript字符串。当解析器发现代码中调用 eval() 方法时，它会将传入的参数当作实际的 ECMAScript 语句来解析，然后把执行结果插入到原位置。通过 eval() 执行的代码被认为是包含该次调用的执行环境的一部分，因此被执行的代码具有与该执行环境相同的作用域链。这意味着通过 eval() 执行的代码可以引用在包含环境中定义的变量：
var msg = "hello world";
eval("alert(msg)"); //"hello world"

eval("function sayHi() { alert('hi'); }");
sayHi();
在 eval() 中创建的任何变量或函数都不会被提升，因为在解析代码的时候，它们被包含在一个字符串中；它们只在 eval() 执行的时候创建。

undefined
NaN
Infinity
所有原生引用类型的构造函数，像Object 、Function、各种Error ，也都是 Global 对象的属性