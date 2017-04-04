# ES6对数值的扩展

## 二进制和八进制表示法
ES6提供了二进制和八进制数值的新的写法，分别用前缀0b（或0B）和0o（或0O）表示：
```
0b111110111 === 503 // true
0o767 === 503 // true
```

## Number.isFinite()
用来检查一个数值是否为有限的（finite）：
```
Number.isFinite(15); // true
Number.isFinite(0.8); // true
Number.isFinite(NaN); // false
Number.isFinite(Infinity); // false
Number.isFinite(-Infinity); // false
Number.isFinite('foo'); // false
Number.isFinite('15'); // false
Number.isFinite(true); // false
```

## Number.isNaN()
用来检查一个值是否为NaN：
```
Number.isNaN(NaN) // true
Number.isNaN(15) // false
Number.isNaN('15') // false
Number.isNaN(true) // false
Number.isNaN(9/NaN) // true
Number.isNaN('true'/0) // true
Number.isNaN('true'/'true') // true
```

## Number.parseInt(), Number.parseFloat()
ES6将全局方法parseInt()和parseFloat()，移植到Number对象上面，行为完全保持不变。
```
Number.parseInt === parseInt // true
Number.parseFloat === parseFloat // true
```

## Number.isInteger()
用来判断一个值是否为整数，在JavaScript内部，整数和浮点数是同样的储存方法，所以3和3.0被视为同一个值：
```
Number.isInteger(25) // true
Number.isInteger(25.0) // true
Number.isInteger(25.1) // false
Number.isInteger("15") // false
Number.isInteger(true) // false
```

## Number.EPSILON
新增的一个极小的常量：
```
Number.EPSILON
// 2.220446049250313e-16
Number.EPSILON.toFixed(20)
// '0.00000000000000022204'
```

## Number.isSafeInteger()
JavaScript能够准确表示的整数范围在-2^53到2^53之间（不含两个端点），超过这个范围，无法精确表示这个值。ES6引入了Number.MAX_SAFE_INTEGER和Number.MIN_SAFE_INTEGER这两个常量，用来表示这个范围的上下限。Number.isSafeInteger()则是用来判断一个整数是否落在这个范围之内。
实际使用这个函数时，需要注意。验证运算结果是否落在安全整数的范围内，不要只验证运算结果，而要同时验证参与运算的每个值。
```
Number.isSafeInteger(9007199254740993)
// false
Number.isSafeInteger(990)
// true
Number.isSafeInteger(9007199254740993 - 990)
// true
9007199254740993 - 990
// 返回结果 9007199254740002
// 正确答案应该是 9007199254740003
```

## Math对象的扩展
Math.trunc()  // 用于去除一个数的小数部分，返回整数部分
Math.sign()  // 用来判断一个数到底是正数、负数、还是零
Math.cbrt()  // 用于计算一个数的立方根
Math.clz32()  // 返回一个数的32位无符号整数形式有多少个前导0
Math.imul()  // 返回两个数以32位带符号整数形式相乘的结果，返回的也是一个32位的带符号整数
Math.fround()  // 返回一个数的单精度浮点数形式
Math.hypot()  // 返回所有参数的平方和的平方根
（新增若干对数、指数、三角函数方法，略）

## 指数运算符
ES7新增了一个指数运算符（**）
```
2 ** 2 // 4
2 ** 3 // 8
```