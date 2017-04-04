# ES6对正则的扩展

## 构造函数的改变
ES6改变了ES5中RegExp的构造函数的行为：构造函数第一个参数是一个正则对象，那么可以使用第二个参数指定修饰符。而且，返回的正则表达式会忽略原有的正则表达式的修饰符，只使用新指定的修饰符。
```
new RegExp(/abc/ig, 'i').flags
// "i"
```

## 字符串的正则方法
字符串对象共有4个方法，可以使用正则表达式：match()、replace()、search()和split()。


## u修饰符
ES6对正则表达式添加了u修饰符，用来正确处理大于\uFFFF的Unicode字符
```
/^\uD83D/u.test('\uD83D\uDC2A')
// false
/^\uD83D/.test('\uD83D\uDC2A')
// true
```

## y修饰符
ES6为正则表达式添加了y修饰符，叫做“粘连”（sticky）修饰符，y修饰符的作用与g修饰符类似，也是全局匹配，后一次匹配都从上一次匹配成功的下一个位置开始。不同之处在于，g修饰符只要剩余位置中存在匹配就可，而y修饰符确保匹配必须从剩余的第一个位置开始，这也就是“粘连”的涵义：
```
var s = 'aaa_aa_a';
var r1 = /a+/g;
var r2 = /a+/y;

r1.exec(s) // ["aaa"]
r2.exec(s) // ["aaa"]

r1.exec(s) // ["aa"]
r2.exec(s) // null
```

## sticky属性 
ES6的正则对象多了sticky属性，表示是否设置了y修饰符

## flags属性
新增了flags属性，会返回正则表达式的修饰符
```
// ES5的source属性
// 返回正则表达式的正文
/abc/ig.source
// "abc"

// ES6的flags属性
// 返回正则表达式的修饰符
/abc/ig.flags
// 'gi'
```