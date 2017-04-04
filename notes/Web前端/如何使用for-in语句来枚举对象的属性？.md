# 如何使用for-in语句来枚举对象的属性？

for (var propName in window) {
document.write(propName);
}
ECMAScript 对象的属性没有顺序。因此，通过 for-in 循环输出的属性名的顺序是不可预测的。
如果表示要迭代的对象的变量值为 null 或 undefined ， for-in 语句会抛出错误。
ECMAScript 5 更正了这一行为；对这种情况不再抛出错误，而只是不执行循环体。为了保证最大限度的
兼容性，建议在使用 for-in 循环之前，先检测确认该对象的值不是 null 或 undefined