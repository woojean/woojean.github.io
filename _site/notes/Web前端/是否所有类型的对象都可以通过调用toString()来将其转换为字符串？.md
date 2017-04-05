# 是否所有类型的对象都可以通过调用toString()来将其转换为字符串？

数值、布尔值、对象和字符串值都有 toString() 方法。但 null 和 undefined 值没有这个方法。在不知道要转换的值是不是 null 或 undefined 的情况下，还可以使用转型函数 String() ，这个函数能够将任何类型的值转换为字符串。 String() 函数遵循下列转换规则：
如果值有 toString() 方法，则调用该方法（没有参数）并返回相应的结果；
如果值是 null ，则返回 "null" ；
如果值是 undefined ，则返回 "undefined" 。