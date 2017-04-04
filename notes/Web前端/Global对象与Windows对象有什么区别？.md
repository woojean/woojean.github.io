# Global对象与Windows对象有什么区别？

在大多数 ECMAScript实现中都不能直接访问 Global 对象；不过，Web 浏览器实现了承担该角色的 window 对象。全局变量和函数都是 Global 对象的属性。Web 浏览器都是将Global全局对象作为window 对象的一部分加以实现的。因此，在全局作用域中声明的所有变量和函数，就都成为了 window对象的属性：
var color = "red";
function sayColor(){
alert(window.color);
}
window.sayColor(); //"red"

JavaScript中的 window 对象除了扮演ECMAScript规定的 Global 对象的角色外，还承担了很多别的任务。

另一种取得 Global 对象的方法是使用以下代码：
// 立即调用的函数表达式
var global = function(){
return this;
}();