# JavaScript是否有块级作用域？

JavaScript 没有块级作用域
if (true) {
var color = "blue";
}
alert(color); //"blue"
使用 var 声明的变量会自动被添加到最接近的环境中。在函数内部，最接近的环境就是函数的局部环境；在 with 语句中，最接近的环境是函数环境。如果初始化变量时没有使用 var 声明，该变量会自动被添加到全局环境。