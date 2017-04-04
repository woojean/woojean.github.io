# 在JavaScript中通过对象字面量方式定义对象与通过构造函数方式定义对象有什么不同？

普通的创建引用实例的方式：
var person = new Object();
person.name = "Nicholas";
person.age = 29;

使用对象字面量方式创建：
var person = {
name : "Nicholas",
age : 29  // 在最后一个属性后面添加逗号会在IE7及更早版本，以及Opera中出错
};

在使用对象字面量语法时，属性名也可以使用字符串：
var person = {
"name" : "Nicholas",
"age" : 29,
5 : true  // 数值属性名会自动转换为字符串
};

在通过对象字面量定义对象时，实际上不会调用 Object 构造函数。