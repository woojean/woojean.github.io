# 如何区分原生与非原生JavaScript对象？

`typeof 操作符经常会导致检测数据类型时得到不靠谱的结果`。如Safari（直至第 4 版）在对正则表达式应用 typeof 操作符时会返回 " function " ，因此很难确定某个值到底是不是函数。
`instanceof 操作符在存在多个全局作用域（像一个页面包含多个 frame）的情况下，也存在很多问题`。一个经典的例子就是像下面这样将对象标识为数组：
var isArray = value instanceof Array;
以上代码要返回 true ， value 必须是一个数组，而且还必须与 Array 构造函数在同个全局作用域中。（别忘了， Array 是 window 的属性。）如果 value 是在另个 frame 中定义的数组，那么以上代码就会返回 false 。

在任何值上调用 Object 原生的 toString() 方法，都会返回一个 [object NativeConstructorName] 格式的字符串。每个类在内部都有一个 [[Class]] 属性，这个属性中就指定了上述字符串中的构造函数名：
alert(Object.prototype.toString.call(value)); //"[object Array]"
`由于原生数组的构造函数名与全局作用域无关，因此使用 toString() 就能保证返回一致的值`。利用这一点，可以创建如下函数：
function isArray(value){
return Object.prototype.toString.call(value) == "[object Array]";
}
同样，也可以基于这一思路来测试某个值是不是原生函数或正则表达式：
function isFunction(value){
return Object.prototype.toString.call(value) == "[object Function]";
}
function isRegExp(value){
return Object.prototype.toString.call(value) == "[object RegExp]";
}
这一技巧也广泛应用于检测原生 JSON 对象。 Object 的 toString() 方法不能检测非原生构造函数的构造函数名。因此，开发人员定义的任何构造函数都将返回[object Object]。有些 JavaScript 库会包含与下面类似的代码。
var isNativeJSON = window.JSON && Object.prototype.toString.call(JSON) =="[object JSON]";