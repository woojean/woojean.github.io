# 什么是attribute？与property有什么关系？有哪些attribute？有哪些property？

特性（attribute）用来`描述属性（property）的各种特征`，特性是为了实现 JavaScript 引擎用的，因此在 JavaScript 中不能直接访问它们。为了表示特性是内部值，ECMA-262规范把它们放在了两对方括号中，例如 [[Enumerable]]

ECMAScript 中有两种属性：数据属性和访问器属性。
数据属性包含一个数据值的位置。在这个位置可以读取和写入值。数据属性有 4 个描述其行为的
特性。
[[Configurable]] 
[[Enumerable]] 
[[Writable]] 
[[Value]] 

要修改属性默认的特性，必须使用 ECMAScript 5 的 `Object.defineProperty()` 方法：
```
var person = {};
Object.defineProperty(person, "name", {
writable: false,
value: "Nicholas"
});
alert(person.name); //"Nicholas"
person.name = "Greg";
alert(person.name); //"Nicholas"
```

访问器属性不包含数据值；它们包含一对getter和setter函数（不过，这两个函数都不是必需的）。访问器属性有如下 4 个特性。
[[Configurable]]
[[Enumerable]] 
[[Get]] 
[[Set]]

访问器属性不能直接定义，必须使用 Object.defineProperty() 来定义。
```
var book = {
_year: 2004,
edition: 1
};
Object.defineProperty(book, "year", {
get: function(){
return this._year;
},
set: function(newValue){
if (newValue > 2004) {
this._year = newValue;
this.edition += newValue - 2004;
}
}
});
book.year = 2005;
alert(book.edition); //2
```
ECMAScript 5 定义了一个 Object.defineProperties() 方法。利用这个方法可以通过描述符一次定义多个属性。
使用 ECMAScript 5 的 Object.getOwnPropertyDescriptor() 方法，可以取得给定属性的描述符。