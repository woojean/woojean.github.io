# 为什么说Boolean 对象在 ECMAScript 中的用处不大？

var falseObject = new Boolean(false);
var result = falseObject && true;
alert(result); //true
var falseValue = false;
result = falseValue && true;
alert(result); //false
alert(typeof falseObject); //object
alert(typeof falseValue); //boolean
alert(falseObject instanceof Boolean); //true
alert(falseValue instanceof Boolean); //false
`布尔表达式中的所有对象都会被转换为 true` ，因此 false Object 对象在布尔表达式中代表的是 true 。
建议是永远不要使用 Boolean 对象。

不建议直接实例化 Number 类型，而原因与显式创建 Boolean 对象一样：在使用typeof 和 instanceof 操作符测试基本类型数值与引用类型数值时，得到的结果完全不同。