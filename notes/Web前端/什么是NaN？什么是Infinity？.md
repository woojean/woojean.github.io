# 什么是NaN？什么是Infinity？

Number类型的取值范围是Number.MIN_VALUE~Number.MAX_VALUE
如果某次计算的结果得到了一个超出 JavaScript 数值范围的值，那么这个数值将被自动转换成特殊的 Infinity 值
要想确定一个数值是不是有穷的（换句话说，是不是位于最小和最大的数值之间），可以使用 `isFinite()` 函数。

NaN ，即非数值（Not a Number），是一个特殊的数值，用于表示一个本来要返回数值的操作数
未返回数值的情况。例如，在 ECMAScript中，任何数值除以 0会返回 NaN

NaN 与任何值都不相等，包括 NaN 本身。

ECMAScript 定义了`isNaN()`函数用于判断一个数值是否为NaN：
alert(isNaN(NaN)); //true
alert(isNaN("blue")); //true（不能转换成数值）
alert(isNaN("10")); //false（可以被转换成数值 10）
isNaN() 也适用于对象。在基于对象调用 isNaN()函数时，会首先调用对象的 valueOf() 方法，然后确定该方法返回的值是否可以转换为数值。如果不能，则基于这个返回值再调用 toString() 方法，再测试返回值。