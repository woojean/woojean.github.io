# ECMAScript中支持函数重载吗？如果定义了同名的函数会怎样？

ECMAScript 函数不能像传统意义上那样实现重载，如果在 ECMAScript中定义了两个名字相同的函数，则该名字只属于后定义的函数：

```
function addSomeNumber(num){
return num + 100;
}

function addSomeNumber(num) {
return num + 200;
}
var result = addSomeNumber(100); //300
```
