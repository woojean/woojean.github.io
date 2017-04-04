# 在JavaScript中使用方括号表示法访问对象属性与使用点表示法访问对象属性有什么不同？

方括号语法的主要优点是可以通过变量
来访问属性，例如：
var propertyName = "name";
alert(person[propertyName]); //"Nicholas"
如果属性名中包含会导致语法错误的字符，或者属性名使用的是关键字或保留字，也可以使用方括
号表示法。例如：
person["first name"] = "Nicholas";